<?php

namespace Modules\UserManagement\Services;

use App\ApiResponse;
use Spatie\Permission\Models\Role;

class RoleService
{
    use ApiResponse;
    public $roleModel;

    public function __construct(Role $roleModel)
    {
        $this->roleModel = $roleModel;
    }

    public function collection($request)
    {
        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();
        return $this->successResponse('Roles fetched successfully', $roles);
    }

    public function store($request)
    {
        // Create the role
        $role = Role::create(['name' => $request->name]);

        // Attach permissions to the role
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return $this->createdResponse('Role created successfully', $role);
    }

    public function show($id)
    {
        // Show a single role with its permissions
        $role = Role::with('permissions')->findOrFail($id);

        return $this->successResponse('Role fetched successfully', $role);
    }

    public function update($request, $id)
    {
        // Find the role
        $role = Role::findOrFail($id);
        // Update role name
        $role->update(['name' => $request->name]);
        // Sync role permissions
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return $this->successResponse('Role updated successfully', $role);
    }

    public function destroy($id)
    {
        // Find the role and delete it
        $role = Role::findOrFail($id);

        $role->delete();

        return $this->successResponse('Role deleted successfully', null);
    }
}
