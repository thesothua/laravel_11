<?php

namespace App\Services;

use App\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    use ApiResponse;
    public $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function collection($request)
    {
        try {
            // Decode the JSON-encoded 'select' parameter from the query string
            $columns = json_decode($request->query('select', '["*"]'), true);
            // Ensure $columns is an array and default to selecting all columns
            if (!is_array($columns)) {
                return response()->json(['error' => 'Invalid select parameter format.'], 400);
            }
            // Get all permissions
            $permissions = Permission::select($columns)->paginate();

            return $this->success($permissions, 'Permission fetched successfully');
        } catch (\Exception $e) {
            return $this->error('An error occurred while fetching permissions.');
        }
    }

    public function store($request)
    {
        // Create a new permission
        $permissions =  Permission::create(['name' => $request->name]);

        return $this->success($permissions, 'Permission created successfully.');
    }

    public function show($id)
    {
        // Show a single permission
        $permission = Permission::findOrFail($id);
        return $this->success($permission, 'Permission fetched successfully');
    }

    public function update($request, $id)
    {
        // Find the permission
        $permission = Permission::findOrFail($id);

        // Update permission name
        $permission->update(['name' => $request->name]);

        return $this->success($permission, 'Permission updated successfully');
    }

    public function destroy($id)
    {
        // Find and delete the permission
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return $this->success(null, 'Permission deleted successfully.');
    }
}
