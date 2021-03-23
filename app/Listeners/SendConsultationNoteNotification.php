<?php

namespace App\Listeners;

use App\Events\ConsultationNoteCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendConsultationNoteNotification
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
     * @param  ConsultationNoteCreated  $event
     * @return void
     */
    public function handle(ConsultationNoteCreated $event)
    {
        //
    }
}
