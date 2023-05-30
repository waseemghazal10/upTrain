<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Student;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    //
    function getBranches($field_id)
    {

        $branches = Branch::where('field_id', $field_id)->get();
        $response = $branches;

        return response($response, 201);
    }
}
