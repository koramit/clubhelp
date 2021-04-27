<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class LogoutUserFromOtherDevices
{
    /**
     * Logout user from other devices.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        Auth::logoutOtherDevices($event->user->profile['logout_other_devices_token']);
    }
}
