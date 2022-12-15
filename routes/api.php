<?php

use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ResponsibilityController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Auth API
Route::controller(UserController::class)
    ->name('auth.')->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', 'logout')->name('logout');
            Route::post('/user', 'fetch')->name('fetch');
        });
    });

// Company API
Route::controller(CompanyController::class)
    ->middleware('auth:sanctum')->name('company.')->group(function () {
        Route::get('/company', 'fetch')->name('fetch');
        Route::post('/company', 'create')->name('create');
        Route::post('/company/update/{id}', 'update')->name('update');
    });

// Team API
Route::controller(TeamController::class)
    ->middleware('auth:sanctum')->name('team.')->group(function () {
        Route::get('/team', 'fetch')->name('fetch');
        Route::post('/team', 'create')->name('create');
        Route::post('/team/update/{id}', 'update')->name('update');
        Route::delete('/team/{id}', 'destroy')->name('delete');
    });

// Role API
Route::controller(RoleController::class)
    ->middleware('auth:sanctum')->name('role.')->group(function () {
        Route::get('/role', 'fetch')->name('fetch');
        Route::post('/role', 'create')->name('create');
        Route::post('/role/update/{id}', 'update')->name('update');
        Route::delete('/role/{id}', 'destroy')->name('delete');
    });

// Responsibility API
Route::controller(ResponsibilityController::class)
    ->middleware('auth:sanctum')->name('responsibility.')->group(function () {
        Route::get('/responsibility', 'fetch')->name('fetch');
        Route::post('/responsibility', 'create')->name('create');
        Route::delete('/responsibility/{id}', 'destroy')->name('delete');
    });

// Employee API
Route::controller(EmployeeController::class)
    ->middleware('auth:sanctum')->name('employee.')->group(function () {
        Route::get('/employee', 'fetch')->name('fetch');
        Route::post('/employee', 'create')->name('create');
        Route::post('/employee/update/{id}', 'update')->name('update');
        Route::delete('/employee/{id}', 'destroy')->name('delete');
    });
