<?php

namespace App\Listeners;

use App\Events\Registered;

class AssignRoleToUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        \Log::debug($event->user->profile);
    }
}
