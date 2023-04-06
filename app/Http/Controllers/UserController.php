<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use App\Models\NotificationToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Student;
use App\Models\Skill;
use App\Models\Field;
use App\Models\skillsStudents;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;




class UserController extends Controller
{


    function getSkills(Request $request)
    {
        $skills = Skill::all();
        $response = $skills;

        return response($response, 201);
    }

    function getFields(Request $request)
    {
        $fields = Field::all();
        $response =  $fields;

        return response($response, 201);
    }

    function register(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|unique:users,email|email',
            'firstName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'lastName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|unique:students,phone_number|size:10|regex:/^05\d{8}$/',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,32}$/',
            'picture' => 'required',
            'skills' => 'required',
            'field' => 'required'
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

        $student = new Student();
        $student->phone_number = $fields['phone'];
        $student->user_id = $user->id;
        $student->field_id = $fields['field'];

        $image = $fields['picture'];
        $imageEncoded = $image['data'];
        $imageEncoded = str_replace('data:image/jpeg;base64,', '', $imageEncoded);
        $imageEncoded = str_replace(' ', '+', $imageEncoded);
        $imageDecoded = base64_decode($imageEncoded);
        $name = time() . '_' . $user->id . '.jpg';
        Storage::disk('studentProfile')->put($name, $imageDecoded);
        $student->photo = $name;
        // $student->photo = $fields['picture'];

        $code = random_int(0, 9999);
        $code = str_pad($code, 4, 0, STR_PAD_LEFT);
        $user->verification_token = bcrypt($code);
        $user->save();
        $student->save();

        $student_id = Student::where('user_id',$user->id)->get('id');
        $skills_id = explode(',',$fields['skills']);
        $student->skill()->attach($skills_id);

        session(['verification_' . $user->id => time()]);
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject("Verify Your Email");
        $email->addTo($user->email, $user->first_name . ' ' . $user->last_name);
        $email->addContent(
            "text/html", view('emails.verification', ['code' => $code])->render()
        );
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($email);
            $response = [
                'user' => $user,
                'student'=>$student
            ];
            return response($response, 201);
        } catch (Exception $e) {
            return response([], 400);
        }
    }


    function verify(Request $request)
    {
        $fields = $request->validate(
            [
                'code' => 'required|size:6|regex:/^\d{6}$/',
            ],
            [
                'required' => 'field-required',
                'code.size' => 'invalid-token',
                'code.regex' => 'invalid-token',
            ]
        );
        $user = User::where('email', $request->email)->first();

        if (!session('verification_' . $user->id) || (session('verification_' . $user->id) && time() - session('verification_' . $user->id) > 600)) {
            error_log($request->session()->get('verification_1') . 'hi');
            $response = [
                'errors' => [
                    'message' => array('expired-token')
                ]
            ];
            return response($response, 400);
        }
        if (!Hash::check($request->code, $user->verification_token)) {
            $response = [
                'errors' => [
                    'message' => array('invalid-token')
                ]
            ];
            return response($response, 400);
        }

        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();
        $token = $user->createToken('upTrainToken')->plainTextToken;
        $response = [
            'message' => 'Verified code successfully',
            'token' => $token,
            'user' => $user,
        ];
        return response($response, 201);
    }


    function resendCode(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $code = random_int(0, 999999);
        $code = str_pad($code, 6, 0, STR_PAD_LEFT);
        $user->verification_token = bcrypt($code);
        $user->save();
        $request->session()->put('verification_' . $user->id, time());
        error_log($request->session()->get('verification_1'));
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject("Verify Your Email");
        $email->addTo($user->email, $user->first_name . ' ' . $user->last_name);
        $email->addContent(
            "text/html", view('emails.verification', ['code' => $code])->render()
        );
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($email);
            $response = [
                'message' => 'success-email'
            ];
            return response($response, 201);
        } catch (Exception $e) {
            return response([], 400);
        }
    }

}

