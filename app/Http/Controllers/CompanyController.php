<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function Ramsey\Uuid\v1;

class CompanyController extends Controller
{
    //
    function getCompanies(Request $request)
    {
        $companies = Company::join('locations', 'locations.id', '=', 'companies.location_id')->get();
        $response = $companies;

        return response($response, 201);
    }


    function deleteCompany($companyName)
    {
        $company = Company::where('cName', $companyName)->delete();

        $response = 'deleted successfully';
        return response($response, 201);
    }
    function getProgramCompany($companyName)
    {
        $company = Company::where('cName', $companyName)->join('locations', 'locations.id', '=', 'companies.location_id')->get();
        $response = $company;

        return response($response, 201);
    }

    function addCompany(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|unique:companies,cEmail|email',
            'name' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'password' => 'required|min:8|max:32|',
            'phone' => 'required',
            'photo' => 'required',
            'description' => 'required',
            'webSite' => 'required',
            'location_id' => 'required'
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
            'cEmail' => $fields['email'],
            'cName' => $fields['name'],
            'cDescription' => $fields['description'],
            'cPassword' => bcrypt($fields['password']),
            'cWebSite' => $fields['webSite'],
            'location_id' => $fields['location_id'],
            'cPhoto' => $fields['photo'],
            'cPhone_number' => $fields['phone']
        ]);

        $code = random_int(0, 9999);
        $code = str_pad($code, 4, 0, STR_PAD_LEFT);
        $company->verification_token = bcrypt($code);
        $company->save();

        $response = [
            'company' => $company,
        ];

        return response($response, 201);
    }

    function updateCompany(Request $request)
    {

        $fields = $request->validate([
            'id' => 'required',
            'email' => 'required|email',
            'name' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required',
            'photo' => 'required',
            'description' => 'required',
            'webSite' => 'required',
            'location_id' => 'required'
        ], [
            'required' => 'field-required',
            'email.email' => 'email-format',
            'name.regex' => 'name-format'
        ]);

        $company = Company::find($fields['id']);
        $company->cPhone_number = $fields['phone'];
        $company->cPhoto = $fields['photo'];
        $company->location_id = $fields['location_id'];
        $company->cName = $fields['name'];
        $company->cEmail = $fields['email'];
        $company->cDescription = $fields['description'];
        $company->cWebSite = $fields['webSite'];
        $company->save();

        $response = [
            'company' => $company
        ];
        return response($response, 201);
    }
}
