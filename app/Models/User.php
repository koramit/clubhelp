<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    public function needQuarantine()
    {
        if (! $this->getNotificationChannel()) {
            return 'notification';
        }

        if ($this->next_activation_at->isPast()) {
            return 'activation';
        }

        return false;
    }

    public function getNotificationChannel()
    {
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
        $channels = $this->profile['notification_channels'];
        $channels['provider'] = $provider;
        $channels[$provider]['id'] = $userId;
        $channels[$provider]['active'] = true;
        $profile = $this->profile;
        $profile['notification_channels'] = $channels;
        $this->profile = $profile;
        $this->save();
    }
}
