<?php

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {


    $tokens = User::select('verification_token')->get();
        
    $SERVER_API_KEY = 'AAAAexnYYC8:APA91bEeYQkJrDzQwpGbVwbFFOH7pv5QuoU9BcVTv1FJCpkZmgCp4Qd2El0H_LbxNyMFdlpJLdUUZschLvgmrbT02v4Zt0Nmpwb3S9XNje-lhGI1BG3ekB2m2dMYdRpYggnjcpRVLK7W';

    $token_1 = 'dq5nPlheTDGaoJSCKuwIhu:APA91bFBsgITYzbxhyYphGBQDbA5qmq17WFSIARqNViBDNHXOS9Xq1INUTiLF58U2LL3vNKi9hNocr_RhN9JZzRCyMiITVHbufPErEzYKdrL05bJ-rPKmD5GoDq-4eAF6rmdmY67-cRK';

    $data = [

        "registration_ids" => [
            $token_1
        ],

        "notification" => [

            "title" => 'Welcome',

            "body" => 'New task added',

            "sound"=> "default" // required for sound on ios

        ],

    ];

    $dataString = json_encode($data);

    $headers = [

        'Authorization: key=' . $SERVER_API_KEY,

        'Content-Type: application/json',

    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

    $response = curl_exec($ch);

    dd($response);

});

Route::get('/loginUser', [UserController::class, 'loginUser']);


