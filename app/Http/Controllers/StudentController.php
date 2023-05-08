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

    // function getCompanyStudents($id)
    // {
    //     // $programs = Program::where('company_id', $id)->get();

    //     // foreach ($programs as $program) {

    //     //     $students = Student::where('program_id',$program->id)->join('users','users.id','=','students.user_id')->get();
    //     // }
    //     // $response =  $students;
    //     // return response($response, 201);

    //     $students = [];
    //     $programs = Program::where('company_id', $id)->get();

    //     foreach ($programs as $program) {
    //         $programStudents = Student::where('program_id', $program->id)
    //             ->join('users', 'users.id', '=', 'students.user_id')
    //             ->get();
    //         $students = array_merge($students, $programStudents);
    //     }

    //     $response = $students;
    //     return response($response, 201);
    // }

    // function getCompanyStudents($id)
    // {
    //     $students = [];

    //     $programs = Program::where('company_id', $id)->get();

    //     foreach ($programs as $program) {
    //         $programStudents = Student::where('program_id', $program->id)
    //             ->join('users', 'users.id', '=', 'students.user_id')
    //             ->get();

    //         if (is_array($programStudents)) {
    //             $students = array_merge($students, $programStudents);
    //         }
    //     }

    //     $response = $students;
    //     return response($response, 201);
    // }

    function deleteStudent($id)
    {
        $student = Student::find($id);
        $student->delete();
        $response = 'The student successfully deleted';

        return response($response, 201);
    }

}
