<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    //
    function getPrograms(Request $request)
    {
        $programs = Program::all();
        $response = $programs;

        return response($response, 201);
    }
}
