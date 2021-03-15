<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationChannelController extends Controller
{
    public function __invoke()
    {
        return Auth::user()->getNotificationChannel();
    }
}
