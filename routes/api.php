<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\LocationController;
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

Route::post('/requestResetPassword', [UserController::class, 'requestReset']);//inside
Route::post('/verifyResetPassword', [UserController::class, 'verifyResetPassword']);//inside
Route::post('/resetPassword', [UserController::class, 'resetPassword']);//inside

Route::get('/getProgramStudents/{program_id}', [StudentController::class, 'getProgramStudents']);
Route::get('/getTrainerStudents/{trainer_id}', [StudentController::class, 'getTrainerStudents']);
Route::get('/getCompanyStudents/{company_id}', [StudentController::class, 'getCompanyStudents']);

Route::get('/getCompanyTrainers/{company_id}', [TrainerController::class, 'getCompanyTrainers']);

Route::get('/getTrainerPrograms/{email}', [ProgramController::class, 'getTrainerPrograms']);//check if wepu;; can get the id

Route::get('/getCompanyPrograms/{name}', [ProgramController::class, 'getCompanyPrograms']);



Route::get('/getSkills', [SkillsController::class, 'getSkills']);
Route::get('/getFields', [FieldsController::class, 'getFields']);
Route::get('/getbranches/{field_id}', [BranchController::class, 'getbranches']);
Route::get('/getStudents/{field_id}', [StudentController::class, 'getStudents']);
Route::get('/getAllStudents', [StudentController::class, 'getAllStudents']);
Route::get('/getLocations', [LocationController::class, 'getLocations']);


Route::get('/getPrograms/{field_id}', [ProgramController::class, 'getPrograms']);
Route::get('/getRecommendedPrograms/{student_id}', [ProgramController::class, 'getRecommendedPrograms']);
Route::get('/getProgramCompany/{name}', [CompanyController::class, 'getProgramCompany']);
Route::get('/getProgramTrainer/{name}', [TrainerController::class, 'getProgramTrainer']);


Route::get('/getCompanies', [CompanyController::class, 'getCompanies']);
Route::get('/getEmployees', [EmployeeController::class, 'getEmployees']);
Route::get('/getAdmin', [EmployeeController::class, 'getAdmin']);
Route::get('/getTrainers', [TrainerController::class, 'getTrainers']);

Route::post('/admin/deleteStudent/{email}', [StudentController::class, 'deleteStudent']);//inside
Route::post('/company/deleteTrainer/{trainer_id}', [TrainerController::class, 'deleteTrainer']);//inside
Route::post('/company/deleteProgram/{program_id}', [ProgramController::class, 'deleteProgram']);//inside
Route::delete('/admin/deleteCompany/{companyName}', [CompanyController::class, 'deleteCompany']);//inside
Route::delete('/admin/deleteEmployee/{email}', [EmployeeController::class, 'deleteEmployee']);//inside





Route::post('/updateStudent', [StudentController::class, 'updateStudent']);//inside
Route::post('/updateTrainer', [TrainerController::class, 'updateTrainer']);//inside
Route::post('/updateEmployee', [EmployeeController::class, 'updateEmployee']);//inside
Route::post('/updateCompany', [CompanyController::class, 'updateCompany']);//inside
Route::post('/updateProgram', [ProgramController::class, 'updateProgram']);//inside

Route::post('/company/addProgram', [ProgramController::class, 'addProgram']);//inside

Route::post('/admin/addCompany', [CompanyController::class, 'addCompany']);//inside

Route::post('/addApplication', [ApplicationController::class, 'addApplication']);//inside
Route::get('/getApplications/{program_id}', [ApplicationController::class, 'getApplications']);
Route::get('/getStudentApplications/{student_id}', [ApplicationController::class, 'getStudentApplications']);
Route::get('/downloadFile/{application_id}',[ApplicationController::class, 'downloadFile']);
Route::post('/acceptApplication/{application_id}', [ApplicationController::class, 'acceptApplication']);
Route::post('/declineApplication/{application_id}', [ApplicationController::class, 'declineApplication']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', [UserController::class, 'logout']);


    Route::post('/admin/addEmployee', [EmployeeController::class, 'addEmployee']);



    Route::post('/company/addTrainer', [TrainerController::class, 'addTrainer']);
});
