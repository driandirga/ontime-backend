<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        // Get input request
        $id = $request->input('id');
        $name = $request->input('name');
        $age = $request->input('age');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $limit = $request->input('limit', 10);

        // Get employee
        $employeeQuery = Employee::query();

        // Get single data
        if ($id) {
            $employee = $employeeQuery->with(['team', 'role'])->find($id);
            // Check response
            if ($employee) {
                return ResponseFormatter::success($employee, 'Employee found');
            }
            return ResponseFormatter::error('Employee not found', 404);
        }

        // Get multiple data
        $employees = $employeeQuery;
        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
        }
        if ($age) {
            $employees->where('age', $age);
        }
        if ($email) {
            $employees->where('email', $email);
        }
        if ($phone) {
            $employees->where('phone', 'like', '%' . $phone . '%');
        }
        if ($team_id) {
            $employees->where('team_id', $team_id);
        }
        if ($role_id) {
            $employees->where('role_id', $role_id);
        }

        // Response success
        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employees found'
        );
    }

    public function create(CreateEmployeeRequest $request): JsonResponse
    {
        try {
            // Get employee
            $employeeQuery = Employee::query();

            // Upload photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // Create employee
            $employee = $employeeQuery->create([
                'name' => $request->input('name'),
                'gender' => $request->input('gender'),
                'age' => $request->input('age'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'photo' => $path ?? $request->input('photo'),
                'team_id' => $request->input('team_id'),
                'role_id' => $request->input('role_id'),
            ]);

            // Check if employee exists
            if (!$employee) {
                throw new Exception('Employee not created');
            }
            // Response success
            return ResponseFormatter::success($employee, 'Employee created');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function update(UpdateEmployeeRequest $request, $id): JsonResponse
    {
        $employeeQuery = Employee::query();
        try {
            // Get employee
            $employee = $employeeQuery->find($id);

            // Check if employee exists
            if (!$employee) {
                throw new Exception('Employee not found');
            }

            // Upload photos
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            // Update employee
            $employee->update([
                'name' => $request->input('name'),
                'gender' => $request->input('gender'),
                'age' => $request->input('age'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'photo' => $path ?? $employee->value('photo'),
                'team_id' => $request->input('team_id'),
                'role_id' => $request->input('role_id'),
            ]);

            // Response success
            return ResponseFormatter::success($employee, 'Employee updated');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            // Get employee
            $employee = Employee::query()->find($id);

            // Check if employee exists
            if (!$employee) {
                throw new Exception('Employee not found');
            }

            // Delete employee
            $employee->delete();

            // Response success
            return ResponseFormatter::success(null,'Employee deleted');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }
}
