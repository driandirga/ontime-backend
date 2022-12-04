<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function all(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        if ($id) {
            $company = Company::with(['users'])->find($id);

            if (!$company) {
                return ResponseFormatter::error('Company not found', 404);
            }
            return ResponseFormatter::success($company, 'Company found');
        }

        $companies = Company::with(['users']);
        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),'Companies found'
        );
    }
}
