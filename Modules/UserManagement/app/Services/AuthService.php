<?php
namespace Modules\UserManagement\Services;

use App\ApiResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use ApiResponse;
    public $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function register($request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Optional: Create profile if data provided
        if ($request->hasAny(['phone_number', 'date_of_birth', 'profile_image'])) {
            $user->profile()->create([
                'phone_number'  => $request->phone_number,
                'date_of_birth' => $request->date_of_birth,
                'profile_image' => $request->profile_image ? $request->file('profile_image')->store('profile_images') : null,
            ]);
        }

        // Optional: Create address if data provided
        if ($request->hasAny(['address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code'])) {
            $user->addresses()->create([
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city'           => $request->city,
                'state'          => $request->state,
                'country'        => $request->country,
                'postal_code'    => $request->postal_code,
                'type'           => $request->type ?? 'home',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        event(new Registered($user));

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    public function login($request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ];

        return $this->successResponse('User Login successfully', $data);
    }

    public function logout($request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    // Step 1: Request Reset Link
    public function sendResetLinkEmail($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => __($status)])
        : response()->json(['message' => __($status)], 400);
    }

    // public function resetPasswordForm($request)
    // {
    //     // Send Welcome Email
    //     $email = $request->email;
    //     Mail::to($email)->send(new WelcomeMail('445gsgigggsgs'));

    //     return response()->json(['message' => 'Welcome email sent successfully!']);
    // }

    // Step 2: Reset Password
    public function resetPassword($request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'          => Hash::make($password),
                    'email_verified_at' => Carbon::now(),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => __($status)])
        : response()->json(['message' => __($status)], 400);
    }

    // Handle email verification
    public function verify($request, $id, $hash)
    {
        $user = $request->user();

        if (! hash_equals((string) $id, (string) $user->getKey()) ||
            ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    // Resend verification email
    public function resend($request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent'], 200);
    }

    // Notice for unverified users
    public function notice()
    {
        return response()->json(['message' => 'Please verify your email address'], 401);
    }
}
