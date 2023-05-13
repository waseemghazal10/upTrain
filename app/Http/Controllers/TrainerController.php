<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use PhpParser\Builder\Trait_;

class TrainerController extends Controller
{
    //
    function getTrainers(Request $request)
    {
        $trainers = Trainer::join('users', 'users.id', '=', 'trainers.user_id')->get();
        $response = $trainers;

        return response($response, 201);
    }


    function addTrainer(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|unique:users,email|email',
            'firstName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'lastName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|unique:trainers,tPhone_number|size:10|regex:/^05\d{8}$/',
            'password' => 'required|min:8|max:32|',
            'photo' => 'required',
            'company_id' => 'required',
            'location_id' => 'rquired'
        ], [
            'required' => 'field-required',
            'password.min' => 'password-length',
            'password.max' => 'password-length',
            'password.regex' => 'password-format',
            'email.unique' => 'email-exists',
            'email.email' => 'email-format',
            'firstName.regex' => 'name-format',
            'lastName.regex' => 'name-format',
            'phone.size' => 'phone-format',
            'phone.regex' => 'phone-format',
            'phone.unique' => 'phone-exists'
        ]);

        $user = User::create([
            'email' => $fields['email'],
            'first_name' => $fields['firstName'],
            'last_name' => $fields['lastName'],
            'password' => bcrypt($fields['password']),
            'location_id' => $fields['location_id'],
            'email_verified_at' => now()
        ]);

        $trainer = new Trainer();
        $trainer->tPhone_number = $fields['phone'];
        $trainer->user_id = $user->id;
        $trainer->company_id = $fields['company_id'];
        $trainer->tPhoto = $fields['photo'];

        $code = random_int(0, 9999);
        $code = str_pad($code, 4, 0, STR_PAD_LEFT);
        $user->verification_token = bcrypt($code);
        $user->save();
        $trainer->save();

        $response = [
            'user' => $user,
            'trainer' => $trainer
        ];

        return response($response, 201);
    }

    function getProgramTrainer($trainerName)
    {
        $trainer = Trainer::where('first_name', $trainerName)->join('users', 'users.id', '=', 'trainers.user_id')->join('companies', 'companies.id', '=', 'trainers.company_id')->get();

        $response = $trainer;

        return response($response, 201);
    }

    function getCompanyTrainers($id)
    {
        $trainers = Trainer::where('company_id', $id)->get();
        $response = $trainers;

        return response($response, 201);
    }

    function deleteTrainer($id)
    {
        $trainer = Trainer::find($id);

        if ($trainer) {
            $user = User::where('id', $trainer->user_id)->first();
            if ($user) {
                $user->delete();
            }
            $trainer->delete();
            $response = 'The trainer and associated user(s) have been successfully deleted';
            return response($response, 201);
        } else {
            $response = 'Could not find trainer with ID ' . $id;
            return response($response, 400);
        }
    }


    function updateTrainer(Request $request)
    {

        $fields = $request->validate([
            'id' => 'required',
            'email' => 'required|email',
            'firstName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'lastName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|size:10|regex:/^05\d{8}$/',
            'photo' => 'required',
            'company_id' => 'required',
            'location_id' => 'rquired'
        ], [
            'required' => 'field-required',
            'password.min' => 'password-length',
            'password.max' => 'password-length',
            'password.regex' => 'password-format',
            'email.email' => 'email-format',
            'firstName.regex' => 'name-format',
            'lastName.regex' => 'name-format',
            'phone.size' => 'phone-format',
            'phone.regex' => 'phone-format',
        ]);

        $trainer = Trainer::find($fields['id']);
        $trainer->tPhone_number = $fields['phone'];
        $trainer->tPhoto = $fields['photo'];
        $trainer->company_id = $fields['company_id'];
        $trainer->save();

        $user = User:: find($trainer->user_id);
        $user->email = $fields['email'];
        $user->first_name = $fields['firstName'];
        $user->last_name = $fields['lastName'];
        $user->location_id = $fields['location_id'];

        $user->save();

        $response = [
            'user' => $user,
            'trainer'=>$trainer
        ];
        return response($response,201);
    }
}
