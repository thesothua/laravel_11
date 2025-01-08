<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\UserManagement\Http\Requests\CreatePermissionRequest;
use Modules\UserManagement\Http\Requests\UpdatePermissionRequest;
use Modules\UserManagement\Services\PermissionService;


class PermissionController extends Controller
{
    use ApiResponse;

    public $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index(Request $request)
    {
        return $this->permissionService->collection($request);
    }

    public function store(CreatePermissionRequest $request)
    {
        return $this->permissionService->store($request);
    }

    public function show($id)
    {
        return  $this->permissionService->show($id);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        return $this->permissionService->update($request, $id);
    }

    public function destroy($id)
    {
        return  $this->permissionService->destroy($id);
    }
}
