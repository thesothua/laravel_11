<?php

namespace Modules\UserManagement\Services;

use App\ApiResponse;
use App\Models\User;
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

            return $this->successResponse('Permission fetched successfully', $permissions);
        } catch (\Exception $e) {
            return $this->error('An error occurred while fetching permissions.');
        }
    }

    public function store($request)
    {
        // Create a new permission
        $permissions = Permission::create(['name' => $request->name]);

        return $this->createdResponse('Permission created successfully.', $permissions);
    }

    public function show($id)
    {
        // Show a single permission
        $permission = Permission::findOrFail($id);
        return $this->successResponse('Permission fetched successfully', $permission);
    }

    public function update($request, $id)
    {
        // Find the permission
        $permission = Permission::findOrFail($id);

        // Update permission name
        $permission->update(['name' => $request->name]);

        return $this->successResponse('Permission updated successfully', $permission);
    }

    public function destroy($id)
    {
        // Find and delete the permission
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return $this->successResponse('Permission deleted successfully.', null);
    }
}
