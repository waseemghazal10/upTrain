<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Company;
use App\Models\Skill;
use App\Models\skillsPrograms;
use App\Models\skillsStudents;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function GuzzleHttp\Promise\each;

class ProgramController extends Controller
{

    function getPrograms($field_id)
    {
        $programs = Program::where('programs.field_id', $field_id)
            ->join('branches', 'branches.id', '=', 'programs.branch_id')
            ->join('companies', 'companies.id', '=', 'programs.company_id')
            ->join('trainers', 'trainers.id', '=', 'programs.trainer_id')
            ->join('users', 'users.id', '=', 'trainers.user_id')
           ->with('skill')
            ->select(
                'programs.id',
                'programs.pTitle',
                'companies.cPhoto',
                'companies.cName',
                'programs.pStart_date',
                'programs.field_id',
                'programs.pEnd_date',
                'branches.bName',
                'programs.pDetails',
                'users.first_name',
                'users.last_name',
                'users.email',
                'trainers.tPhone_number',
                'trainers.tPhoto'

                // 'trainers.user_id'
            )->get();
          
        $response = $programs;

        return response($response, 201);
    }

    function getRecommendedPrograms($student_id)
    {
        $skills = skillsStudents::where('student_id', $student_id)->pluck('skill_id')->toArray();

        $url = Http::post('http://localhost:8080/recommendation/programs', [
            'skills' => $skills,
            'top_n' => 10,
        ]);

        $data = $url['recommended_program_ids_and_distances'];

        $programIds = array_keys($data);
        $programs = [];

        foreach ($programIds as $programId) {
            $program = Program::where('programs.id', $programId)
                ->join('branches', 'branches.id', '=', 'programs.branch_id')
                ->join('companies', 'companies.id', '=', 'programs.company_id')
                ->join('trainers', 'trainers.id', '=', 'programs.trainer_id')
                ->join('users', 'users.id', '=', 'trainers.user_id')
                ->with('skill')
                ->select(
                    'programs.id',
                    'programs.pTitle',
                    'companies.cPhoto',
                    'companies.cName',
                    'programs.pStart_date',
                    'programs.field_id',
                    'programs.pEnd_date',
                    'branches.bName',
                    'programs.pDetails',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'trainers.tPhone_number',
                    'trainers.tPhoto'
                )
                ->first(); // Use "first()" to get a single program instead of "get()"

            if ($program) {
                $programs[] = $program;
            }
        }

        return response($programs, 201);
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

    function getTrainerPrograms($trainer_id)
    {
        // $trainer = Trainer::join('users', 'users.id', '=', 'trainers.user_id')->where('users.email', $email)
        // ->select('trainers.id', 'users.first_name', 'users.last_name', 'users.email')->with('program')->first();

        $programs = Program::where('programs.trainer_id', $trainer_id)->join('trainers', 'trainers.id', '=',  'programs.trainer_id')->join('users', 'users.id', '=', 'trainers.user_id')->join('branches', 'branches.id', '=', 'programs.branch_id')->join('companies', 'companies.id', '=', 'programs.company_id')
            ->select('branches.bName', 'programs.*', 'users.first_name', 'users.last_name','companies.cPhoto', 'companies.cName')
            ->get();
        // error_log($trainer);

        $response = $programs;

        return response($response, 201);
    }

    function getCompanyPrograms($name)
    {

        $company = Company::where('cName', $name)->first();
        $programs = Program::where('programs.company_id', $company->id)
            ->join('branches', 'branches.id', '=', 'programs.branch_id')
            ->join('companies', 'companies.id', '=', 'programs.company_id')
            ->join('trainers', 'trainers.id', '=', 'programs.trainer_id')
            ->join('users', 'users.id', '=', 'trainers.user_id')->with('skill')->select(
                'programs.id',
                'programs.pTitle',
                'companies.cPhoto',
                'companies.cName',
                'programs.pStart_date',
                'programs.field_id',
                'programs.pEnd_date',
                'branches.bName',
                'programs.pDetails',
                'users.first_name',
                'users.last_name',
                'users.email',
                'trainers.tPhone_number',
                'trainers.tPhoto'

                // 'trainers.user_id'
            )->get();

        $response = $programs;

        return response($response, 201);
    }



    function deleteProgram($program_id)
    {
        $program = Program::find($program_id);

        if ($program) {
            $program->delete();
            $response = 'The program have been successfully deleted';
            return response($response, 201);
        } else {
            $response = 'Could not find program with ID ' . $program_id;
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
        $program->pPhoto = $fields['photo'];

        $program->save();

        $response = [
            'program' => $program
        ];
        return response($response, 201);
    }
}
