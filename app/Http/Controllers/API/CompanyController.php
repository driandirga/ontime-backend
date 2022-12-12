<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        // Get input request
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // Get company relation user
        $companyQuery = Company::with('users')->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        });

        // Get single data
        if ($id) {
            $company = $companyQuery->find($id);
            // Check response
            if (!$company) {
                return ResponseFormatter::error('Company not found', 404);
            }
            return ResponseFormatter::success($company, 'Company found');
        }

        // Get multiple data
        $companies = $companyQuery;
        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        // Response success
        return ResponseFormatter::success(
            $companies->paginate($limit), 'Companies found'
        );
    }

    public function create(CreateCompanyRequest $request): JsonResponse
    {
        try {
            // Get user id & company
            $userId = Auth::id();
            $companyQuery = Company::with('company');

            // Upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // Create company
            $company = $companyQuery->create([
                'name' => $request->input('name'),
                'logo' => $path ?? $request->input('logo'),
                'user_id' => $userId,
            ]);

            // Check if company exists
            if (!$company) {
                throw new Exception('Company not created');
            }

            // Attach company to user
            $user = User::query()->find($userId)->companies();
            $user->attach($company->getAttribute('id'));

            // Load user at company
            $company->load('users');

            // Response success
            return ResponseFormatter::success($company, 'Company created');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function update(UpdateCompanyRequest $request, $id): JsonResponse
    {
        try {
            // Get user id & company
            $userId = Auth::id();
            $company = Company::with('users')->find($id);

            // Check if company exists
            if (!$company) {
                throw new Exception('Company not found');
            }

            // Upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // Update company
            $company->update([
                'name' => $request->input('name'),
                'logo' => $path ?? $company->value('logo'),
                'user_id' => $userId,
            ]);

            // Response success
            return ResponseFormatter::success($company, 'Company updated');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }
}
