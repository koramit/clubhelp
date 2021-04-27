<?php

namespace App\Listeners;

use App\Events\InvalidMembership;
use Illuminate\Support\Facades\Log;

class OpenInvalidMembershipTicket
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
     * @param  InvalidMembership  $event
     * @return void
     */
    public function handle(InvalidMembership $event)
    {
        Log::info($event->user->name.' invalid membership');
    }
}
