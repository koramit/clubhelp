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
        if ($this->getNotificationChannel()) {
            return 'notification';
        }

        if ($this->next_activation_at->isPast()) {
            return 'activation';
        }

        return false;
    }

    public function getNotificationChannel()
    {
        if ($this->profile['notification_channels'] === []) {
            return null;
        }

        return $this->profile['notification_channels'][$this->profile['notification_channels']['provider']];
    }

    public function setNotificationChannel($provider, $userId)
    {
        $notificationChannels = $this->profile['notification_channels'];
        $notificationChannels['provider'] = $provider;
        $notificationChannels[$provider]['id'] = $userId;
        $notificationChannels[$provider]['active'] = true;
        $this->profile['notification_channels'] = $notificationChannels;
        $this->save();
    }
}
