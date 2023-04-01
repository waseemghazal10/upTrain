<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use App\Models\NotificationToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;




class UserController extends Controller
{

    function register(Request $request)
    {
    //    error_log(json_encode($request->image));
        $fields = $request->validate([
            'email' => 'required|unique:users,email|email',
            'firstName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'lastName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|unique:students,phone_number|size:10|regex:/^05\d{8}$/',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,32}$/',
            'picture' => 'required'
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
}

