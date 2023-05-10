<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Student;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    //
    function getBranches($id)
    {

        $branches = Branch::where('field_id', $id)->get();
        $response = $branches;

        return response($response, 201);
    }
}
