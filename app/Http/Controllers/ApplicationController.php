<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // error_log($request);
        $student = Student::where('students.id', $request->student_id)
            ->join('users', 'users.id', '=', 'students.user_id')
            ->select('users.first_name', 'users.last_name')->first();
        // error_log($student);
        $file = $request->file('pdf_file');
        // error_log($file);
        $filename = $student->first_name . '_' . $student->last_name . '_' . $request->program_id . '.' . $file->getClientOriginalExtension();
        error_log($filename);
        $path = $file->storeAs('public/StudentsCvs', $filename);
        // error_log($path);
        $url = Storage::url($path);
        error_log($url);

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

        $applications = Application::where('program_id', $program_id)->join('programs', 'programs.id', '=', 'applications.program_id')
            ->select('applications.*', 'programs.pTitle')->get();

        $response = $applications;
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


    public function downloadFile($application_id)
    {
        $application = Application::find($application_id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $filePath = 'public/StudentsCvs/' . $application->cv;
        error_log($filePath);
        if (!Storage::exists($filePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($filePath);
    }
}
