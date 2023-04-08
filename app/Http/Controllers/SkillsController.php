<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;

class SkillsController extends Controller
{
    //

    function getSkills(Request $request)
    {
        $skills = Skill::all();
        $response = $skills;

        return response($response, 201);
    }

}
