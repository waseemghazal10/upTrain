<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SkillsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('/resendCode', [UserController::class, 'resendCode']);
Route::post('/requestResetPassword', [UserController::class, 'requestReset']);
Route::post('/verifyResetPassword', [UserController::class, 'verifyResetPassword']);
Route::post('/resetPassword', [UserController::class, 'resetPassword']);

Route::get('/getSkills', [SkillsController::class, 'getSkills']);
Route::get('/getFields', [FieldsController::class, 'getFields']);
Route::get('/getPrograms', [ProgramController::class, 'getPrograms']);


Route::get('/getCompanies', [CompanyController::class, 'getCompanies']);
Route::post('/addCompany', [CompanyController::class, 'addCompany']);

Route::get('/getEmployees', [EmployeeController::class, 'getEmployees']);
Route::post('/addEmployee', [EmployeeController::class, 'addEmployee']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [UserController::class, 'logout']);
});