<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationToken;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    function getNotifications(Request $requestd)
    {
        $notifications = Notification::all();

        $response = [
            'notifications' => $notifications,
        ];

        return response($response, 201);
    }
}
