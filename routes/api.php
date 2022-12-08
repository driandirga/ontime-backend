<?php

use App\Http\Controllers\API\CompanyController;
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
Route::controller(CompanyController::class)->middleware('auth:sanctum')
    ->name('company.')->group(function () {
    Route::get('/company', 'fetch')->name('fetch');
    Route::post('/company',  'create')->name('create');
    Route::post('/company/update/{id}',  'update')->name('update');
});

Route::controller(UserController::class)->name('auth.')->group(function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/register', 'register')->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout')->name('logout');
        Route::post('/user', 'fetch')->name('fetch');
    });
});
