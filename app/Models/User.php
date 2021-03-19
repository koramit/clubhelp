<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'login',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'profile' => 'array',
        'email_verified_at' => 'datetime',
        'next_activation_at' => 'datetime',
    ];

    public function encounters()
    {
        return $this->belongsToMany(Encounter::class)
                    ->as('subscription')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function getTimezoneAttribute()
    {
        return 'asia/bangkok';
    }

    public function needQuarantine()
    {
        if (! $this->getNotificationChannel()) {
            return 'notification';
        }

        if ($this->next_activation_at->isPast()) {
            return 'reactivation';
        }

        return false;
    }

    public function getNotificationChannel()
    {
        if (! isset($this->profile)) {
            return null; // for route notification
        }
        $channels = $this->profile['notification_channels'];
        if ($channels === [] ||
            ! $channels[$channels['provider']]['active'] // user unfollowed
        ) {
            return null;
        }

        return $channels[$channels['provider']];
    }

    public function setNotificationChannel($provider, $userId)
    {
        $profile = $this->profile;
        $profile['notification_channels']['provider'] = $provider;
        $profile['notification_channels'][$provider]['id'] = $userId;
        $profile['notification_channels'][$provider]['active'] = true;
        $this->profile = $profile;
        $this->save();
        Log::info('user '.$this->name.' setup '.$provider.' notification');
    }

    public function disableNotificationChannel($provider)
    {
        if (! isset($this->profile['notification_channels'][$provider])) {
            return;
        }
        $profile = $this->profile;
        $profile['notification_channels'][$provider]['active'] = false;
        $this->profile = $profile;
        $this->save();
        Log::info('user '.$this->name.' disabled '.$provider.' notification');
    }

    /**
     * Route notifications for the Line channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForLine($notification)
    {
        return $this->getNotificationRecipient('telegram');
    }

    /**
     * Route notifications for the Telegram channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForTelegram($notification)
    {
        return $this->getNotificationRecipient('telegram');
    }

    public function getNotificationRecipient($provider)
    {
        $channel = $this->getNotificationChannel();
        if (! $channel || $this->prefers_notification_channel !== $provider) {
            return null;
        }

        return $this->profile['notification_channels'][$provider]['id'];
    }

    public function reactivate($days)
    {
        $this->next_activation_at = $this->next_activation_at->addDays($days);
        $this->save();
    }

    public function getPrefersNotificationChannelAttribute()
    {
        return isset($this->profile['notification_channels']['provider']) ? $this->profile['notification_channels']['provider'] : null;
    }
}
