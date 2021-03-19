<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TelegramChannel
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
        $payload = $notification->toTelegram($notifiable);

        if (! $chat_id = $payload['chat_id']) { // check if use route notification
            if (! $chat_id = $notifiable->routeNotificationFor('telegram', $notification)) { // check if no recipient provided
                return null;
            }
            $payload['chat_id'] = $chat_id;
        }

        // now support plain text only
        return Http::post('https://api.telegram.org/bot'.config('services.telegram.bot_token').'/sendMessage', [
            'chat_id' => $payload['chat_id'],
            'text' => $payload['text'],
        ]);
    }
}
