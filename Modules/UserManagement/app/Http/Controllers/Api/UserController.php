<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Services\UserService;

class UserController extends Controller
{
    public $userService;

    public function __construct(UserService $userService)
    {
        return $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        return $this->userService->collection($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User core information
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed', // Requires a matching `password_confirmation` field
            ],

            // Profile information
            'profile' => [
                'required',
                'array',
            ],
            'profile.first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'profile.last_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'profile.phone_number' => [
                'nullable',
                'string',
                'max:15',
            ],
            'profile.date_of_birth' => [
                'nullable',
                'date',
            ],
            'profile.profile_image' => [
                'nullable',
                'image',
                'max:2048', // 2MB limit
            ],

            // Roles
            'roles' => [
                'required',
                'array',
            ],
            'roles.*' => [
                'integer', // Ensure each role ID is an integer
                'exists:roles,id', // Validate that the role ID exists in the roles table
            ],

            // Addresses
            'addresses' => [
                'required',
                'array',
            ],
            'addresses.*.address_line_1' => [
                'required',
                'string',
                'max:255',
            ],
            'addresses.*.city' => [
                'required',
                'string',
                'max:100',
            ],
            'addresses.*.state' => [
                'required',
                'string',
                'max:100',
            ],
            'addresses.*.country' => [
                'required',
                'string',
                'max:100',
            ],
            'addresses.*.postal_code' => [
                'required',
                'string',
                'max:20',
            ],
            'addresses.*.type' => [
                'required',
                'in:home,work,billing,shipping', // Restrict to specific types
            ],
        ]);

        if ($validator->fails()) {
            return $validator->messages();
        }

        return $this->userService->store($request);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return $this->userService->show($id);
    }
    
    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id)
    // {
    //     $this->userService->update($id);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        return $this->userService->destroy($id);
    }
}
