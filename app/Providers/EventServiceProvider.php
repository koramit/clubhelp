<?php

namespace App\Providers;

use App\Events\ConsultationNoteCreated;
use App\Events\InvalidMembership;
use App\Events\Registered;
use App\Listeners\AssignRoleToUser;
use App\Listeners\LogoutUserFromOtherDevices;
use App\Listeners\OpenInvalidMembershipTicket;
use App\Listeners\SendConsultationNoteNotification;
use App\Listeners\UpdateUserSocialProfile;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            AssignRoleToUser::class,
        ],
        InvalidMembership::class => [
            OpenInvalidMembershipTicket::class,
        ],
        Login::class => [
            LogoutUserFromOtherDevices::class,
            UpdateUserSocialProfile::class,
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
