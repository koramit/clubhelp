<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class UpdateUserSocialProfile
{
    protected $profile;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->profile = Session::pull('socialProfile');
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if (! $this->profile) {
            return;
        }
        $profile = $event->user->profile;
        $updates = false;
        foreach (['name', 'nickname', 'avatar'] as $field) {
            if ($this->profile[$field] !== $profile['social'][$field]) {
                $profile['social'][$field] = $this->profile[$field];
                $updates = true;
            }
        }

        if ($updates) {
            $event->user->forceFill(['profile' => $profile]);
        }
    }
}
