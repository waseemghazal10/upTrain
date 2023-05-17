<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Field;

class FieldsController extends Controller
{
    //



    function getFields()
    {
        $fields = Field::where('fields.id', '!=', '17')->join('colleges', 'colleges.id', 'fields.college_id')
            ->get();

        // $fieldsWEmployee = $fields->join('employees', 'employees.field_id', 'fields.id')->get();
        $fieldsWEmployee = Field::join('employees', 'employees.field_id', 'fields.id')
            ->where('field_id', '!=', '17')->where('employees.eRole','!=','1')->join('users', 'users.id', 'employees.user_id')
            ->get();


        if ($fieldsWEmployee->count() != 0) {
            $response =  $fieldsWEmployee;
            
        } else {
            $response =  $fields;
        }

        return response($response, 201);
    }

    // function getEmployeesFields(Request $request)
    // {
    //     $fields = Field::join('employees', 'employees.field_id', 'fields.id')
    //         ->where('field_id', '!=', 17)->join('users', 'users.id', 'employees.user_id')
    //         ->get();

    //     $response =  $fields;

    //     return response($response, 201);
    // }
}
