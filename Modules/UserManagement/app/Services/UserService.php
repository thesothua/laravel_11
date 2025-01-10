<?php

namespace Modules\UserManagement\Services;

use App\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use ApiResponse;
    public $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function collection($request)
    {
        return $this->userModel->all();
    }

    public function store($request)
    {

        $user = $this->userModel->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create profile if provided
        if ($request->has('profile')) {
            $user->profile()->create($request->input('profile'));
        }

        // Create addresses if provided
        if ($request->has('addresses')) {
            $user->addresses()->createMany($request->input('addresses'));
        }

        // Assign roles to the user
        $user->assignRole($request->roles);


        return $this->success($user, 'User created successfully.', 200);
    }

    public function show($id)
    {
        $user = $this->userModel->with('roles', 'permissions')->findOrFail($id);

        return $this->success($user, 'User fetch successfully.', 200);
    }

    // public function update($request, $id)
    // {
    //     $user = $this->userModel->with('roles', 'permissions')->findOrFail($id);

    //     return $this->success($user, 'User fetch successfully.', 200);
    // }
}
