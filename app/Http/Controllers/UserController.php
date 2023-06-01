<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use App\Models\Company;
use App\Models\Employee;
use App\Models\NotificationToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Student;
use App\Models\Skill;
use App\Models\Field;
use App\Models\skillsStudents;
use App\Models\Trainer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\DBAL\TimestampType;

class UserController extends Controller
{


    function register(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|unique:users,email|email',
            'firstName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'lastName' => 'required|regex:/^[\x{0621}-\x{064a} A-Za-z]+$/u',
            'phone' => 'required|unique:students,sPhone_number|size:10|regex:/^05\d{8}$/',
            'password' => 'required|min:8|max:32|',
            'photo' => 'required',
            'skills' => 'required',
            'field_id' => 'required',
            'location_id' => 'required',

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
            'email_verified_at' => now(),
        ]);

        $student = new Student();
        $student->sPhone_number = $fields['phone'];
        $student->user_id = $user->id;
        $student->field_id = $fields['field_id'];
        $student->sPhoto = $fields['photo'];

        $code = random_int(0, 9999);
        $code = str_pad($code, 4, 0, STR_PAD_LEFT);
        $user->verification_token = bcrypt($code);
        $user->save();
        $student->save();

        $skills_id = explode(',', $fields['skills']);
        $student->skill()->attach($skills_id);

        session(['verification_' . $user->id => time()]);
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject("Verify Your Email");
        $email->addTo($user->email, $user->first_name . ' ' . $user->last_name);
        $email->addContent(
            "text/html",
            view('emails.verification', ['code' => $code])->render()
        );
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($email);
            $response = [
                'user' => $user,
                'student' => $student
            ];
            return response()->json($response, 201);
        } catch (Exception $e) {
            return response([], 400);
        }
    }


    function verify(Request $request)
    {
        $fields = $request->validate(
            [
                'code' => 'required|size:6|regex:/^\d{6}$/',
                'email' => 'required',
            ],
            [
                'required' => 'field-required',
                'code.size' => 'invalid-token',
                'code.regex' => 'invalid-token',
            ]
        );
        error_log($request->email);
        $user = User::where('email', $request->email)->first();

        if (!session('verification_' . $user->id) || (session('verification_' . $user->id) && time() - session('verification_' . $user->id) > 600)) {
            // error_log($request->session()->get('verification_1') . 'hi');
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
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject("Verify Your Email");
        $email->addTo($user->email, $user->first_name . ' ' . $user->last_name);
        $email->addContent(
            "text/html",
            view('emails.verification', ['code' => $code])->render()
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

    function login(Request $request)
    {
        $fields = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required'
            ],
            [
                'required' => 'field-required',
                'email.email' => 'email-format',
            ]
        );

        $user = User::where('email', $fields['email'])->first();
        // error_log($user);
        if ($user) {
            if (!$user || !Hash::check($fields['password'], $user->password)) {
                $response = [
                    'errors' => [
                        'message' => array('credentials-invalid')
                    ]
                ];
                return response($response, 400);
            }

            $user->tokens()->delete();

            $student = Student::where('user_id', $user->id)->join('fields', 'fields.id', 'students.field_id')
            ->select('students.id','students.sPhone_number','students.sPhoto','students.user_id','students.field_id','fields.fName','fields.college_id')->first();

            $trainer = Trainer::where ('user_id',$user->id)->first();

            $employee = Employee::where('user_id', $user->id)->join('fields', 'fields.id', 'employees.field_id')->first();

            if ($student) {
                $skillsStudent = skillsStudents::where('student_id', $student->id)->join('skills', 'skills.id', '=', 'skills_students.skill_id')
                    ->get();
                if ($user->email_verified_at !== null) {
                    $token = $user->createToken('upTrainToken')->plainTextToken;
                    error_log($token);
                    $userWithLocation = User::where('email', $fields['email'])->join('locations', 'locations.id', '=', 'users.location_id')
                    ->select('users.id','users.email','users.first_name','users.last_name','users.password','users.location_id','locations.locationName')->first();
                    $response = [
                        'user' => $userWithLocation,
                        'student' => $student,
                        'skills' => $skillsStudent,
                        'token' => $token
                    ];
                } else {
                        $response = [
                            'user' => $user,
                            'student'=>$student,
                            'skills'=>$skillsStudent
                        ];
                    }
            }
            else if ($trainer){
                $userWithLocation = User::where('email', $fields['email'])->join('locations', 'locations.id', '=', 'users.location_id')
                ->select('users.id','users.email','users.first_name','users.last_name','users.password','users.location_id','locations.locationName')->first();
                if ($user->email_verified_at !== null) {
                    $token = $user->createToken('upTrainToken')->plainTextToken;
                    error_log($token);
                    $response = [
                        'user' => $userWithLocation,
                        'trainer' => $trainer,
                        'token' => $token
                    ];
                } else {
                    $response = [
                        'user' => $userWithLocation,
                        'trainer' => $trainer
                    ];
                }
            }
            else if ($employee){
                $userWithLocation = User::where('email', $fields['email'])->join('locations', 'locations.id', '=', 'users.location_id')
                ->select('users.id','users.email','users.first_name','users.last_name','users.password','users.location_id','locations.locationName')->first();
                if ($user->email_verified_at !== null) {
                    $token = $user->createToken('upTrainToken')->plainTextToken;
                    error_log($token);
                    $response = [
                        'user' => $userWithLocation,
                        'employee' => $employee,
                        'token' => $token
                    ];
                } else {
                    $response = [
                        'user' => $userWithLocation,
                        'employee' => $employee
                    ];
                }
            }

        }
        else {
            $company = Company::where('cEmail',$fields['email'])->join('locations','locations.id','companies.location_id')->select('companies.*','locations.locationName')->first();
            if ($company){
                if (!$company || !Hash::check($fields['password'], $company->cPassword)) {
                    $response = [
                        'errors' => [
                            'message' => array('credentials-invalid')
                        ]
                    ];
                    return response($response, 400);
                }
                $response = [
                    'company'=>$company
                ];
            }
        }
        return response($response, 201);
    }

    function requestReset(Request $request) // link send token with url 
    {
        $fields = $request->validate(
            [
                'email' => 'required|email',
            ],
            [
                'required' => 'field-required',
                'email.email' => 'email-format',
            ]
        );
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $code = random_int(0, 9999); // string 
            $code = str_pad($code, 4, 0, STR_PAD_LEFT);
            $user->reset_token = bcrypt($code);
            $user->save();
            $request->session()->put('reset_' . $user->id, time());
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $email->setSubject("Verify Your Email");
            $email->addTo($user->email, $user->first_name . ' ' . $user->last_name);
            $email->addContent(
                "text/html",
                view('emails.verification', ['code' => $code])->render()
            );
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            try {
                $sendgrid->send($email);
                $response = [
                    'message' => 'Email sent successfully',
                    'email' => $user->email
                ];
                return response($response, 201);
            } catch (Exception $e) {
                return response([], 400);
            }
        } else {
            $response = [
                'errors' => [
                    'message' => array('email-not-found')
                ]
            ];
            return response($response, 404);
        }
    }

    function verifyResetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

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
        if (!session('reset_' . $user->id) || (session('reset_' . $user->id) && time() - session('reset_' . $user->id) > 600)) {
            $response = [
                'errors' => [
                    'message' => array('expired-token')
                ]
            ];
            return response($response, 400);
        }
        if (!Hash::check($request->code, $user->reset_token)) {
            $response = [
                'errors' => [
                    'message' => array('invalid-token')
                ]
            ];
            return response($response, 400);
        }
        $token = $user->createToken('sakankomToken')->plainTextToken;
        $response = [
            'message' => 'Verified code successfully',
            'token' => $token
        ];
        return response($response, 201);
    }

    function resetPassword(Request $request)
    {
        $fields = $request->validate(
            [
                'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,32}$/|confirmed'
            ],
            [
                'password.required' => 'field-required',
                'password.confirmed' => 'password-not-match',
                'password.min' => 'password-length',
                'password.max' => 'password-length',
                'password.regex' => 'password-format',
            ]
        );


        $user = auth()->user();

        if (Hash::check($fields['password'], $user->password)) {
            $response = [
                'errors' => [
                    'message' => array('password-duplicate')
                ]
            ];
            return response($response, 400);
        }
        $user->password = bcrypt($fields['password'] . '');
        $user->save();
        $user->tokens()->delete();


        $response = [
            'message' => 'Password changed successfully'
        ];


        return response($response, 201);
    }

    function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        $response = [
            'message' => 'logged out'
        ];
        return response($response, 201);
    }
}
