<?php

namespace App\Notifications;

use App\Channels\LineChannel;
use App\Channels\TelegramChannel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlainText extends Notification
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // notification will send to all channels in return array
        if (! $notifiable instanceof User) {
            return [LineChannel::class, TelegramChannel::class];
        }

        if ($notifiable->prefers_notification_channel === 'line') {
            return [LineChannel::class];
        } elseif ($notifiable->prefers_notification_channel === 'telegram') {
            return [TelegramChannel::class];
        } else {
            return [];
        }
    }

    public function toLine($notifiable)
    {
        // recipient + formatted message compatible with channel
        return [
            'to' => ($notifiable instanceof User) ? $notifiable->getNotificationRecipient('line') : null,
            'messages' => [[
                'type' => 'text',
                'text' => $this->message,
            ]],
        ];
    }

    public function toTelegram($notifiable)
    {
        // recipient + formatted message compatible with channel
        return [
            'chat_id' => isset($notifiable->profile) ? $notifiable->getNotificationRecipient('telegram') : null,
            'text' => $this->message,
        ];
    }
}
