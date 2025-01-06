<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\UserManagement\Http\Requests\CreateRoleRequest;
use Modules\UserManagement\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;

    public $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        $this->roleService->collection($request);
    }

    public function store(Request $request)
    {
        $this->roleService->store($request);
    }

    public function show($id)
    {
        $this->roleService->show($id);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $this->roleService->update($request, $id);
    }

    public function destroy($id)
    {
        $this->roleService->destroy($id);
    }
}
