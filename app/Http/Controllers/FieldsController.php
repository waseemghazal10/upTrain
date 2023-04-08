<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Field;

class FieldsController extends Controller
{
    //



    function getFields(Request $request)
    {
        $fields = Field::all();
        $response =  $fields;

        return response($response, 201);
    }


}
