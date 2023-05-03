<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\skillsPrograms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    //
    function getPrograms(Request $request)
    {
        $programs = Program::join('branches','branches.id','=','programs.branch_id')->get();...
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
            'branch_id' => 'required',
            'company_id' => 'required',
            'skills' => 'required'
        ], [
            'required' => 'field-required'
        ]);

        $program = Program::create([
            'pTitle' => $fields['title'],
            'pStart_date' => $fields['start_date'],
            'pEnd_date' => $fields['end_date'],
            'branch_id' => $fields['branch_id'],
            'company_id' => $fields['company_id'],
            'pPhoto' => $fields['photo']
        ]);

        // $image = $fields['photo'];
        // $imageData = file_get_contents($image);

        // $name = time() . '_' . $program->id . '.jpg';

        // error_log($name);
        // Storage::disk('programProfile')->put($name, $imageData);
        // $program->photo = $name;

        $program->save();

        $skills_id = explode(',',$fields['skills']);
        $program->skill()->attach($skills_id);

        $response = [
            'program' => $program,
        ];

        return response($response, 201);
    }
}
