<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Modules\UserManagement\Http\Requests\CreatePermissionRequest;
use Modules\UserManagement\Http\Requests\UpdatePermissionRequest;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Console\Input\Input;

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
        $this->permissionService->collection($request);
    }

    public function store(CreatePermissionRequest $request)
    {
        $this->permissionService->store($request);
    }

    public function show($id)
    {
        $this->permissionService->show($id);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $this->permissionService->update($request, $id);
    }

    public function destroy($id)
    {
        $this->permissionService->destroy($id);
    }
}
