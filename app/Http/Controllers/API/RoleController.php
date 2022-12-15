<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        // Get input request
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);

        // Get role
        $roleQuery = Role::query();

        // Get single data
        if ($id) {
            $role = $roleQuery->with('responsibilities')->find($id);
            // Check response
            if ($role) {
                return ResponseFormatter::success($role, 'Role found');
            }
            return ResponseFormatter::error('Role not found', 404);
        }

        // Get multiple data
        $roles = $roleQuery->where('company_id', $request->input('company_id'));
        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibilities) {
            $roles->with('responsibilities');
        }

        // Response success
        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles found'
        );
    }

    public function create(CreateRoleRequest $request): JsonResponse
    {
        try {
            // Get role
            $roleQuery = Role::query();

            // Create role
            $role = $roleQuery->create([
                'name' => $request->input('name'),
                'company_id' => $request->input('company_id'),
            ]);

            // Check if role exists
            if (!$role) {
                throw new Exception('Role not created');
            }
            // Response success
            return ResponseFormatter::success($role, 'Role created');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id): JsonResponse
    {
        $roleQuery = Role::query();
        try {
            // Get role
            $role = $roleQuery->find($id);

            // Check if role exists
            if (!$role) {
                throw new Exception('Role not found');
            }

            // Update role
            $role->update([
                'name' => $request->input('name'),
                'company_id' => $request->input('company_id'),
            ]);

            // Response success
            return ResponseFormatter::success($role, 'Role updated');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            // Get role
            $role = Role::query()->find($id);

            // Check if role exists
            if (!$role) {
                throw new Exception('Role not found');
            }

            // Delete role
            $role->delete();

            // Response success
            return ResponseFormatter::success(null,'Role deleted');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }
}
