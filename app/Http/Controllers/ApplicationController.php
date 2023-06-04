<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Program;
use App\Models\skillsStudents;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

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

        $filename = $student->first_name . '_' . $student->last_name . '_' . $request->program_id . '.'. $file->getClientOriginalExtension();

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

        $this->sendApplicationNotification('Greeting,Your application in process');

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

        $this->sendApplicationNotification('Congrats!,Your application accepted');

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

        $this->sendApplicationNotification('Sorry,Your application declined');
        
        return response()->json(['message' => 'Application accepted succefully'], 201);
    }
    

    public function sendApplicationNotification(string $status)
    {

        // Retrieve student tokens from the database
        // $tokens = User::pluck('verification_token')->toArray();

        $SERVER_API_KEY = 'AAAAexnYYC8:APA91bEeYQkJrDzQwpGbVwbFFOH7pv5QuoU9BcVTv1FJCpkZmgCp4Qd2El0H_LbxNyMFdlpJLdUUZschLvgmrbT02v4Zt0Nmpwb3S9XNje-lhGI1BG3ekB2m2dMYdRpYggnjcpRVLK7W';

        $token_1 = 'dq5nPlheTDGaoJSCKuwIhu:APA91bFBsgITYzbxhyYphGBQDbA5qmq17WFSIARqNViBDNHXOS9Xq1INUTiLF58U2LL3vNKi9hNocr_RhN9JZzRCyMiITVHbufPErEzYKdrL05bJ-rPKmD5GoDq-4eAF6rmdmY67-cRK';

        $data = [
            "registration_ids" => [
                $token_1,
                // $tokens
            ],
            "notification" => [

                "title" => 'Application status updated',
                "body" => $status,
                "sound" => "default"
            ],

        ];
       // Create a notification and add it to the database
       $notification = new Notification();
       $notification->title = 'Application status updated';
       $notification->body = $status;
       $notification->save();

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        dd($response);
    }

}
