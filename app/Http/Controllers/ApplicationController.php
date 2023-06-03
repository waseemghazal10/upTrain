<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Program;
use App\Models\skillsStudents;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{

    function addApplication(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'program_id' => 'required',
            'pdf_file' => 'required',

        ], [
            'required' => 'field-required',
        ]);

        $student = Student:: where ('students.id',$request->student_id)
        ->join('users','users.id','=','students.user_id')
        ->select('users.first_name','users.last_name')->first();

        $file = $request->file('pdf_file');

        $filename = $student->first_name . '_' . $student->last_name . '_' . $request->program_id . '.';

        $path = $file->storeAs('public/StudentsCvs', $filename);


        $application = Application::create([
            'status' => 0,
            'cv' => $filename,
            'program_id' => $request->program_id,
            'student_id' => $request->student_id
        ]);


        $response = [
            'application' => $application,
        ];
        return response($response, 201);
    }


    function getApplications($program_id)
    {
        $applications = Application::where('applications.program_id', $program_id)
            ->join('students', 'students.id', '=', 'applications.student_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->join('locations', 'locations.id', '=', 'users.location_id')
            ->join('programs', 'programs.id', '=', 'applications.program_id')
            ->join('fields', 'fields.id', '=', 'students.field_id')
            ->select('applications.*', 'students.sPhone_number', 'students.sPhoto', 'users.first_name', 'users.last_name', 'users.email', 'locations.locationName',
             'locations.id AS location_id', 'fields.id AS field_id', 'fields.fName','programs.pTitle')
            ->get();

        $response = [];

        foreach ($applications as $application) {
            $skillsStudents = SkillsStudents::where('student_id', $application->student_id)
                ->join('skills', 'skills.id', '=', 'skills_students.skill_id')
                ->select('skName')
                ->get();

            $response[] = [
                'application' => $application,
                'skills' => $skillsStudents
            ];
        }

        return response($response, 201);
    }



    function getStudentApplications($student_id)
    {
        $applications = Application::where('student_id', $student_id)
            ->join('programs', 'programs.id', '=', 'applications.program_id')
            ->select('applications.*', 'programs.pTitle')->get();

        $response = $applications;
        return response($response, 201);
    }


    function downloadFile($application_id)
    {

        $application = Application::find($application_id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $filePath = 'public/StudentsCvs/' . $application->cv;

        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $application->status = 2;
        $application->save();
        return Storage::download($filePath);
    }

    function acceptApplication($application_id)
    {
        $application = Application::find($application_id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->status = 3;
        $application->save();

        $student = Student::find($application->student_id);
        $student-> program_id = $application->program_id;

        $program = Program::find($application->program_id);
        $student->trainer_id = $program->trainer_id;
        $student->company_id = $program->company_id;
        $student->save();

        return response()->json(['message' => 'Application accepted succefully'], 201);
    }

    function declineApplication($application_id)
    {
        $application = Application::find($application_id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->status = 4;

        $application->save();

        return response()->json(['message' => 'Application accepted succefully'], 201);
    }
    
}
