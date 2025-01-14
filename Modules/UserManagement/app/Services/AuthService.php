<?php

namespace Modules\UserManagement\Services;

use App\ApiResponse;
use App\Models\User;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // Optional profile fields
            'phone_number' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'profile_image' => 'nullable|image|max:2048', // Example for profile image
            // Optional address fields
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'type' => 'nullable|in:home,work,billing,shipping', // Address type validation
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Optional: Create profile if data provided
        if ($request->hasAny(['phone_number', 'date_of_birth', 'profile_image'])) {
            $user->profile()->create([
                'phone_number' => $request->phone_number,
                'date_of_birth' => $request->date_of_birth,
                'profile_image' => $request->profile_image ? $request->file('profile_image')->store('profile_images') : null,
            ]);
        }

        // Optional: Create address if data provided
        if ($request->hasAny(['address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code'])) {
            $user->addresses()->create([
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'type' => $request->type ?? 'home',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function login($request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
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
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => __($status)])
        : response()->json(['message' => __($status)], 400);
    }
}
