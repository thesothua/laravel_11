<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\UserManagement\Http\Requests\CreateUserRequest;
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
    public function store(CreateUserRequest $request)
    {
       
        return  $this->userService->store($request);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return  $this->userService->show($id);
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
    // public function destroy($id)
    // {
    //     //
    // }
}
