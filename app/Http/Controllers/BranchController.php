<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Student;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    //
    function getBranches(Request $request)
    {
        $user = auth()->user();
        $student = Student::where ('user_id',$user->id)->first();
        $branches = Branch::where('field_id',$student->field_id)->get();
        $response = $branches;

        return response($response, 201);
    }
}
