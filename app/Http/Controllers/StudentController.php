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
    function getStudents(Request $request)
    {
        $students = Student::join('users','users.id','=','students.user_id')->get();
        $response =  $students;

        return response($response, 201);
    }


    function getProgramStudents($id)
    {
        $students = Student::where('program_id',$id)->join('users','users.id','=','students.user_id')->get();
        $response =  $students;

        return response($response, 201);
    }

    function getCompanyStudents($id)
    {
        $students = Student::where('company_id',$id)->join('users','users.id','=','students.user_id')->get();
        $response =  $students;

        return response($response, 201);
    }


    function deleteStudent($id)
    {
        $student = Student::find($id);

        if ($student) {
            $user = User::where('id', $student->user_id)->first();
            if ($user) {
                $user->delete();
            }
            $student->delete();
            $response = 'The student and associated user(s) have been successfully deleted';
            return response($response, 201);
        } else {
            $response = 'Could not find student with ID ' . $id;
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

        $user = User:: where('email',$fields['email'])->first();
        $user->email = $fields['email'];
        $user->first_name = $fields['firstName'];
        $user->last_name = $fields['lastName'];
        $user->location_id = $fields['location_id'];
        $user->save();

        $student = Student::where ('user_id',$user->id)->first();
        $student->skill()->detach();
        $student->sPhone_number = $fields['phone'];
        $student->sPhoto = $fields['photo'];
        $student->save();


        $skills_id = explode(',',$fields['skills']);
        $student->skill()->attach($skills_id);

        $skillsStudent = skillsStudents:: where('student_id',$student->id)->join('skills','skills.id','=','skills_students.skill_id')->get();

        $response = [
            'user' => $user,
            'student'=>$student,
            'skills'=>$skillsStudent,
        ];
        return response($response,201);
    }



}
