<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    //
    function getCompanies(Request $request)
    {
        $companies = Company::all();
        $response = $companies;

        return response($response, 201);
    }


    function addCompany(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|unique:companies,email|email',
            'name' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,32}$/',
            'photo' => 'required',
            'description' => 'required',
            'webSite' => 'required',
            'location' => 'required'
        ], [
            'required' => 'field-required',
            'password.min' => 'password-length',
            'password.max' => 'password-length',
            'password.regex' => 'password-format',
            'email.unique' => 'email-exists',
            'email.email' => 'email-format',
            'name.regex' => 'name-format'
        ]);

        $company = Company::create([
            'email' => $fields['email'],
            'name' => $fields['name'],
            'description' => $fields['description'],
            'password' => bcrypt($fields['password']),
            'webSite' => $fields['webSite'],
            'location' => $fields['location'],
            // 'photo' => $fields['photo']
        ]);


        $image = $fields['photo'];
        $imageData = file_get_contents($image);

        $name = time() . '_' . $company->id . '.jpg';

        error_log($name);
        Storage::disk('companyProfile')->put($name, $imageData);
        $company->photo = $name;

        $code = random_int(0, 9999);
        $code = str_pad($code, 4, 0, STR_PAD_LEFT);
        $company->verification_token = bcrypt($code);
        $company->save();

        $response = [
            'company' => $company,
        ];

        return response($response, 201);
    }
}
