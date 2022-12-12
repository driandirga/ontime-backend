<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Models\Responsibility;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        // Get input request
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // Get responsibility
        $responsibilityQuery = Responsibility::query();

        // Get single data
        if ($id) {
            $responsibility = $responsibilityQuery->find($id);
            // Check response
            if ($responsibility) {
                return ResponseFormatter::success($responsibility, 'Responsibility found');
            }
            return ResponseFormatter::error('Responsibility not found', 404);
        }

        // Get multiple data
        $responsibilities = $responsibilityQuery->where('role_id', $request->input('role_id'));
        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        // Response success
        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibilities found'
        );
    }

    public function create(CreateResponsibilityRequest $request): JsonResponse
    {
        try {
            // Get responsibility
            $responsibilityQuery = Responsibility::query();

            // Create responsibility
            $responsibility = $responsibilityQuery->create([
                'name' => $request->input('name'),
                'role_id' => $request->input('role_id'),
            ]);

            // Check if responsibility exists
            if (!$responsibility) {
                throw new Exception('Responsibility not created');
            }
            // Response success
            return ResponseFormatter::success($responsibility, 'Responsibility created');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            // Get responsibility
            $responsibility = Responsibility::query()->find($id);

            // Check if responsibility exists
            if (!$responsibility) {
                throw new Exception('Responsibility not found');
            }

            // Delete responsibility
            $responsibility->delete();

            // Response success
            return ResponseFormatter::success(null,'Responsibility deleted');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }
}
