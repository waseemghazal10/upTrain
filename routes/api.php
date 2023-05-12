<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TrainerController;
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

Route::get('/getProgramStudents/{id}', [StudentController::class, 'getProgramStudents']);
Route::get('/getTrainerStudents/{id}', [StudentController::class, 'getTrainerStudents']);
Route::get('/getCompanyStudents/{id}', [StudentController::class, 'getCompanyStudents']);

Route::get('/getCompanyTrainers/{id}', [TrainerController::class, 'getCompanyTrainers']);

Route::get('/getTrainerPrograms/{name}', [ProgramController::class, 'getTrainerPrograms']);
Route::get('/getCompanyPrograms/{name}', [ProgramController::class, 'getCompanyPrograms']);


Route::get('/getSkills', [SkillsController::class, 'getSkills']);
Route::get('/getFields', [FieldsController::class, 'getFields']);
Route::get('/getbranches/{id}', [BranchController::class, 'getbranches']);
Route::get('/getStudents', [StudentController::class, 'getStudents']);

Route::get('/getPrograms/{id}', [ProgramController::class, 'getPrograms']);
Route::get('/getProgramCompany/{name}', [CompanyController::class, 'getProgramCompany']);
Route::get('/getProgramTrainer/{name}', [TrainerController::class, 'getProgramTrainer']);


Route::get('/getCompanies', [CompanyController::class, 'getCompanies']);
Route::get('/getEmployees', [EmployeeController::class, 'getEmployees']);
Route::get('/getTrainers', [TrainerController::class, 'getTrainers']);

Route::post('/admin/deleteStudent/{id}', [StudentController::class, 'deleteStudent']);
Route::post('/company/deleteTrainer/{id}', [TrainerController::class, 'deleteTrainer']);
Route::post('/company/deleteProgram/{id}', [ProgramController::class, 'deleteProgram']);


Route::post('/company/addProgram', [ProgramController::class, 'addProgram']);

Route::post('/admin/addCompany', [CompanyController::class, 'addCompany']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [UserController::class, 'logout']);


    Route::post('/admin/addEmployee', [EmployeeController::class, 'addEmployee']);



    Route::post('/company/addTrainer', [TrainerController::class, 'addTrainer']);
});
