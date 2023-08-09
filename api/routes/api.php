<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// test route

Route::get('test', function () {
    return "Tested";
});

// login route
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login_by_api'])->name('login');




Route::middleware('auth:api')->prefix('v1')->group(function () {

    Route::apiResource('company', CompanyController::class);
    Route::apiResource('employee', EmployeeController::class);
    Route::get('getCompanyList', [CompanyController::class,'getCompanyList'])->name('getCompanyList');


    // logout route
    Route::post('logout', [App\Http\Controllers\Api\UserController::class, 'logout'])->name('logout');
});
