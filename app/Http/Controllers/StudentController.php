<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Skill;
use App\Models\skillsStudents;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Student;
use App\Models\User;
use Error;

class StudentController extends Controller
{
    function getStudents($field_id)
    {
        $students = Student::join('users', 'users.id', '=', 'students.user_id')->join('locations', 'locations.id', 'users.location_id')
        ->join('fields', 'fields.id', 'students.field_id')
        ->join('companies','companies.id','=','students.company_id')
        ->where('fields.id', $field_id)
        ->select('students.*' , 'users.email', 'users.first_name', 'users.last_name', 'users.location_id',
        'fields.fName', 'locations.locationName','companies.cName')->get();
        $response =  $students;

        return response($response, 201);
    }

    function getTrainingStudents($field_id)
    {
        $students = Student::join('users', 'users.id', '=', 'students.user_id')->join('locations', 'locations.id', 'users.location_id')
        ->join('fields', 'fields.id', 'students.field_id')
        ->join('companies','companies.id','=','students.company_id')
        ->join('programs','programs.id','=','students.program_id')->where('fields.id', $field_id)
        ->select('students.*' , 'users.email', 'users.first_name', 'users.last_name', 'users.location_id',
        'fields.fName', 'locations.locationName','companies.cName','programs.pTitle')->get();
        $response =  $students;

        return response($response, 201);
    }



    function getAllStudents()
    {
        $students = Student::join('users', 'users.id', '=', 'students.user_id')->join('locations', 'locations.id', 'users.location_id')
        ->join('fields', 'fields.id', 'students.field_id')
        ->select('students.*' , 'users.email', 'users.first_name', 'users.last_name', 'users.location_id','fields.fName', 'locations.locationName')->get();
        $response =  $students;

        return response($response, 201);
    }


    function getProgramStudents($program_id)
    {
        $students = Student::where('students.program_id', $program_id)->join('users', 'users.id', '=', 'students.user_id')
        ->join('fields','fields.id','=','students.field_id')->join('locations','locations.id','=','users.location_id')
        ->select('students.*','users.first_name','users.last_name','users.email','locations.locationName','fields.fName','users.location_id')->get();

        $response =  $students;

        return response($response, 201);
    }

    function getTrainerStudents($trainer_id)
    {
        $students = Student::where('trainer_id', $trainer_id)->join('users', 'users.id', '=', 'students.user_id')
        ->join('fields','fields.id','=','students.field_id')->join('locations','locations.id','=','users.location_id')
        ->select('students.*','users.first_name','users.last_name','users.email','locations.locationName','fields.fName')->get();

        $response =  $students;

        return response($response, 201);
    }

    function getCompanyStudents($company_id)
    {
        $students = Student::where('company_id', $company_id)->join('users', 'users.id', '=', 'students.user_id')
        ->join('fields','fields.id','=','students.field_id')->join('locations','locations.id','=','users.location_id')
        ->select('students.*','users.first_name','users.last_name','users.email','locations.locationName','fields.fName')->get();

        $response =  $students;

        return response($response, 201);
    }


    function deleteStudent($email)
    {
        $student = Student::join('users', 'users.id', '=', 'students.user_id')->where('users.email', $email)->first();

        if ($student) {
            $user = User::where('id', $student->user_id)->first()->delete();
            $student->delete();

            $response = 'The student and associated user(s) have been successfully deleted';
            return response($response, 201);
        } else {
            $response = 'Could not find student with ID ' . $email;
            return response($response, 400);
        }
    }


    function updateStudent(Request $request)
    {

        $fields = $request->validate([
            'id' => 'required',
            'email' => 'required|email',
            'firstName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'lastName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|size:10|regex:/^05\d{8}$/',
            'photo' => 'required',
            'skills' => 'required',
            'location_id' => 'required',

        ], [
            'required' => 'field-required',
            'email.email' => 'email-format',
            'firstName.regex' => 'name-format',
            'lastName.regex' => 'name-format',
            'phone.size' => 'phone-format',
            'phone.regex' => 'phone-format',
        ]);

        $user = User::where('email', $fields['email'])->first();
        $user->email = $fields['email'];
        $user->first_name = $fields['firstName'];
        $user->last_name = $fields['lastName'];
        $user->location_id = $fields['location_id'];
        $user->save();

        $student = Student::where('user_id', $user->id)->first();
        $student->skill()->detach();
        $student->sPhone_number = $fields['phone'];
        $student->sPhoto = $fields['photo'];
        $student->save();


        $skills_id = explode(',', $fields['skills']);
        $student->skill()->attach($skills_id);

        $skillsStudent = skillsStudents::where('student_id', $student->id)->join('skills', 'skills.id', '=', 'skills_students.skill_id')->get();

        $response = [
            'user' => $user,
            'student' => $student,
            'skills' => $skillsStudent,
        ];
        return response($response, 201);
    }

    function getUser($student_id)
    {
        $students = Student::where('students.id', $student_id)
            // ->join('students', 'students.id', '=', 'applications.student_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->join('locations', 'locations.id', '=', 'users.location_id')
            ->join('programs', 'programs.id', '=', 'students.program_id')
            ->join('fields', 'fields.id', '=', 'students.field_id')
            ->select('students.*','users.first_name', 'users.last_name', 'users.email', 'locations.locationName',
            'locations.id AS location_id','fields.fName','programs.pTitle')
            ->get();

        $response = [];

        foreach ($students as $student) {
            $skillsStudents = SkillsStudents::where('student_id', $student_id)
                ->join('skills', 'skills.id', '=', 'skills_students.skill_id')
                ->get();

            $response[] = [
                'student' => $student,
                'skills' => $skillsStudents
            ];
        }

        return response($response, 201);
    }

}
