<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    //

    function getAdmin(){
        $admin = Employee::where('eRole',1)->join('users','users.id','=','employees.user_id')
        ->join('locations','locations.id','=','users.location_id')->get();
        $response = $admin;

        return response($response, 201);
    }

    function getEmployees(Request $request)
    {
        $employees = Employee::where('eRole',0)->join('users','users.id','=','employees.user_id')
        ->join('locations','locations.id','=','users.location_id')
        ->join('fields','fields.id','=','employees.field_id')->get();
        $response = $employees;

        return response($response, 201);
    }


    function addEmployee(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|unique:users,email|email',
            'first_name' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'last_name' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|unique:employees,ePhone_number|size:10|regex:/^05\d{8}$/',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,32}$/',
            'photo' => 'required',
            'location_id' => 'required',
        ], [
            'required' => 'field-required',
            'password.min' => 'password-length',
            'password.max' => 'password-length',
            'password.regex' => 'password-format',
            'email.unique' => 'email-exists',
            'email.email' => 'email-format',
            'first_name.regex' => 'name-format',
            'last_name.regex' => 'name-format',
            'phone.size' => 'phone-format',
            'phone.regex' => 'phone-format',
            'phone.unique' => 'phone-exists'
        ]);

        $user = User::create([
            'email' => $fields['email'],
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'password' => bcrypt($fields['password']),
            'location_id' => $fields['location_id'],
            'email_verified_at' => now()
        ]);

        $employee = new Employee();
        $employee->ePhone_number = $fields['phone'];
        $employee->user_id = $user->id;
        $employee->eRole = 1;
        $employee->ePhoto = $fields['photo'];

        $code = random_int(0, 9999);
        $code = str_pad($code, 4, 0, STR_PAD_LEFT);
        $user->verification_token = bcrypt($code);
        $user->save();
        $employee->save();

        $response = [
            'user' => $user,
            'employee' => $employee
        ];

        return response($response, 201);
    }

    function updateEmployee(Request $request)
    {

        $fields = $request->validate([
            'id' => 'required',
            'email' => 'required|email',
            'first_name' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'last_name' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|size:10|regex:/^05\d{8}$/',
            'photo' => 'required',
            'location_id' => 'required',
        ], [
            'required' => 'field-required',
            'email.email' => 'email-format',
            'first_name.regex' => 'name-format',
            'last_name.regex' => 'name-format',
            'phone.size' => 'phone-format',
            'phone.regex' => 'phone-format',
        ]);

        $user = User:: where('email',$fields['email'])->first();
        $user->email = $fields['email'];
        $user->first_name = $fields['firstName'];
        $user->last_name = $fields['lastName'];
        $user->location_id = $fields['location_id'];

        $user->save();

        $employee = Employee::where ('user_id',$user->id)->first();
        $employee->ePhone_number = $fields['phone'];
        $employee->ePhoto = $fields['photo'];
        $employee->save();

        $response = [
            'user' => $user,
            'employee'=>$employee
        ];
        return response($response,201);
    }
}
