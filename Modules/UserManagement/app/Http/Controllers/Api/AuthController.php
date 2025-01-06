<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
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
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $this->authService->register($request);
    }

    /**
     * Login a user and return a token
     */
    public function login(Request $request)
    {
        $this->authService->login($request);
    }

    /**
     * Logout a user (revoke the token)
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request);
    }
}
