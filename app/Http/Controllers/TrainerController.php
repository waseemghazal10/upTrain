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
        $trainers = Trainer::join('users','users.id','=','trainers.user_id')->get();
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
            'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,32}$/',
            'photo' => 'required',
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
        ]);

        $trainer = new Trainer();
        $trainer->tPhone_number = $fields['phone'];
        $trainer->user_id = $user->id;
        // $trainer->tPhoto = $fields['photo'];


        $image = $fields['photo'];
        $imageData = file_get_contents($image);

        $name = time() . '_' . $user->id . '.jpg';

        error_log($name);
        Storage::disk('trainerProfile')->put($name, $imageData);
        $trainer->tPhoto = $name;

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
}
