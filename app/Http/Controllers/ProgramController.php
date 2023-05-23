<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Company;
use App\Models\skillsPrograms;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{

    function getPrograms($id)
    {
        $programs = Program::where('field_id', $id)
            ->join('branches', 'branches.id', '=', 'programs.branch_id')
            ->join('companies', 'companies.id', '=', 'programs.company_id')
            ->join('trainers', 'trainers.id', '=', 'programs.trainer_id')
            ->join('users', 'users.id', '=', 'trainers.user_id')->get();
        $response = $programs;

        return response($response, 201);
    }


    function addProgram(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'photo' => 'required',
            'details' => 'required',
            'branch_id' => 'required',
            'company_id' => 'required',
            'trainer_id' => 'required',
            'field_id'=>'required',
            'skills' => 'required'
        ], [
            'required' => 'field-required'
        ]);

        $program = Program::create([
            'pTitle' => $fields['title'],
            'pStart_date' => $fields['start_date'],
            'pEnd_date' => $fields['end_date'],
            'pDetails' => $fields['details'],
            'branch_id' => $fields['branch_id'],
            'company_id' => $fields['company_id'],
            'trainer_id' => $fields['trainer_id'],
            'field_id' => $fields['field_id'],
            'pPhoto' => $fields['photo']
        ]);

        $program->save();

        $skills_id = explode(',', $fields['skills']);
        $program->skill()->attach($skills_id);

        $response = [
            'program' => $program,
        ];

        return response($response, 201);
    }

    function getTrainerPrograms($name)
    {
        $trainer = Trainer::join('users', 'users.id', '=', 'trainers.user_id')->where('first_name', $name)->first();

        $programs = Program::where('programs.trainer_id', $trainer->id)->get();


        $response = $programs;

        return response($response, 201);
    } //doesn't work yet!!!!!

    function getCompanyPrograms($name)
    {

        $company = Company::where('cName', $name)->first();
        $programs = Program::where('programs.company_id', $company->id)
            ->join('branches', 'branches.id', '=', 'programs.branch_id')
            ->join('companies', 'companies.id', '=', 'programs.company_id')
            ->join('trainers', 'trainers.id', '=', 'programs.trainer_id')
            ->join('users', 'users.id', '=', 'trainers.user_id')->get();

        $response = $programs;

        return response($response, 201);
    }

    function deleteProgram($id)
    {
        $program = Program::find($id);

        if ($program) {
            $program->delete();
            $response = 'The program have been successfully deleted';
            return response($response, 201);
        } else {
            $response = 'Could not find program with ID ' . $id;
            return response($response, 400);
        }
    }

    function updateProgram(Request $request)
    {

        $fields = $request->validate([
            'id' => 'required',
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'photo' => 'required',
            'details' => 'required',
            'branch_id' => 'required',
            'trainer_id' => 'required',
            'field_id'=>'required',
            'skills' => 'required'
        ], [
            'required' => 'field-required'
        ]);

        $program = Program::find($fields['id']);

        $program->pTitle = $fields['title'];
        $program->pStart_date = $fields['start_date'];
        $program->pEnd_date = $fields['end_date'];
        $program->pDetails = $fields['details'];
        $program->branch_id = $fields['branch_id'];
        $program->trainer_id = $fields['trainer_id'];
        $program->field_id = $fields['field_id'];
        $program->pPhoto = $fields['photo'];

        $program->save();

        $response = [
            'program' => $program
        ];
        return response($response, 201);
    }
}
