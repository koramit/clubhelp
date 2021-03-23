<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class LogChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Send notification to the $notifiable instance...
        $payload = $notification->toLog($notifiable);

        if (! $user = $payload['user']) { // check if use route notification
            if (! $user = $notifiable->routeNotificationFor('log', $notification)) { // check if no recipient provided
                return null;
            }
            $payload['user'] = $user;
        }

        \Log::info($user.' => '.$payload['text']);

        return [];
    }
}
