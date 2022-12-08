<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['users'])->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        });

        // Get single data
        if ($id) {
            $company = $companyQuery->find($id);

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

        return ResponseFormatter::success(
            $companies->paginate($limit), 'Companies found'
        );
    }

    public function create(CreateCompanyRequest $request): JsonResponse
    {
        try {
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            $company = Company::with(['company'])->create([
                'name' => $request->name,
                'logo' => $path,
                'user_id' => Auth::id(),
            ]);

            if (!$company) {
                throw new Exception('Company not created');
            }
            //Attach company to user
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);
            // Load user at company
            $company->load('users');
            return ResponseFormatter::success($company, 'Company created');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function update(UpdateCompanyRequest $request, $id): JsonResponse
    {
        try {
            // Get company
            $company = Company::with(['users'])->find($id);
            // If company exists
            if (!$company) {
                throw new Exception('Company not found');
            }

            // Upload logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }
            // Update company
            $company->update([
                'name' => $request->name,
                'logo' => $path ?? $company->logo,
                'user_id' => Auth::id(),
            ]);

            return ResponseFormatter::success($company, 'Company updated');
        } catch (Exception $exception) {
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }
}
