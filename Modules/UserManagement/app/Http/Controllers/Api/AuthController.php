<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\UserManagement\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Register a new user
     */

    public $authService;

    public function __construct(AuthService $authService)
    {
        return  $this->authService = $authService;
    }

    public function register(Request $request)
    {
        return  $this->authService->register($request);
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
        return  $this->authService->logout($request);
    }

    /**
     * Reset password a user (revoke the token)
     */
    public function sendResetLinkEmail(Request $request)
    {
        return  $this->authService->sendResetLinkEmail($request);
    }

    /**
     * Reset password a user (revoke the token)
     */
    public function resetPassword(Request $request)
    {
        return  $this->authService->resetPassword($request);
    }

    public function testmail(Request $request)
    {
        return  $this->authService->testmail($request);
    }
}
