<?php

namespace App\Providers;

// use Illuminate\Auth\Events\Registered;
// use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Events\ConsultationNoteCreated;
use App\Events\InvalidMembership;
use App\Listeners\OpenInvalidMembershipTicket;
use App\Listeners\SendConsultationNoteNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        InvalidMembership::class => [
            OpenInvalidMembershipTicket::class,
        ],
        ConsultationNoteCreated::class => [
            SendConsultationNoteNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
