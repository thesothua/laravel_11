<?php
namespace Modules\UserManagement\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Services\AuthService;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Register a new user
     */

    public $authService;

    public function __construct(AuthService $authService)
    {
        return $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User core information
            'name'                       => [
                'required',
                'string',
                'max:255',
            ],
            'email'                      => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password'                   => [
                'required',
                'string',
                'min:8',
                'confirmed', // Requires a matching `password_confirmation` field
            ],

            // Profile information
            'profile'                    => [
                'required',
                'array',
            ],
            'profile.first_name'         => [
                'required',
                'string',
                'max:255',
            ],
            'profile.last_name'          => [
                'nullable',
                'string',
                'max:255',
            ],
            'profile.phone_number'       => [
                'nullable',
                'string',
                'max:15',
            ],
            'profile.date_of_birth'      => [
                'nullable',
                'date',
            ],
            'profile.profile_image'      => [
                'nullable',
                'image',
                'max:2048', // 2MB limit
            ],

            // Addresses
            'addresses'                  => [
                'required',
                'array',
            ],
            'addresses.*.address_line_1' => [
                'required',
                'string',
                'max:255',
            ],
            'addresses.*.city'           => [
                'required',
                'string',
                'max:100',
            ],
            'addresses.*.state'          => [
                'required',
                'string',
                'max:100',
            ],
            'addresses.*.country'        => [
                'required',
                'string',
                'max:100',
            ],
            'addresses.*.postal_code'    => [
                'required',
                'string',
                'max:20',
            ],
            'addresses.*.type'           => [
                'required',
                'in:home,work,billing,shipping', // Restrict to specific types
            ],
        ]);

        if ($validator->fails()) {
            return $validator->messages();
        }
        
        return $this->authService->register($request);
    }

    /**
     * Login a user and return a token
     */
    public function login(Request $request)
    {
        return $this->authService->login($request);
    }

    /**
     * Logout a user (revoke the token)
     */
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }

    /**
     * Reset password a user (revoke the token)
     */
    public function sendResetLinkEmail(Request $request)
    {
        return $this->authService->sendResetLinkEmail($request);
    }

    /**
     * Reset password a user (revoke the token)
     */
    public function resetPassword(Request $request)
    {
        return $this->authService->resetPassword($request);
    }

    public function verify(Request $request, $id, $hash)
    {
        return $this->authService->verify($request, $id, $hash);
    }
}
