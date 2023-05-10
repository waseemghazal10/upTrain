<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Student;
use App\Models\User;

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



}
