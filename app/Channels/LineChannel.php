<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class LineChannel
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
        $payload = $notification->toLine($notifiable);

        if (! $to = $payload['to']) { // check if use route notification
            if (! $to = $notifiable->routeNotificationFor('line', $notification)) { // check if no recipient provided
                return null;
            }
            $payload['to'] = $to;
        }

        return Http::withToken(config('services.line.bot_token')) // now support plain text only
                   ->post(config('services.line.base_endpoint').'message/push', $payload);
    }
}
