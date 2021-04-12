<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
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

    /**
     * A user may be assigned many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function encounters()
    {
        return $this->belongsToMany(Encounter::class)
                    ->as('subscription')
                    ->withPivot('status', 'as', 'draft')
                    ->withTimestamps();
    }

    public function getAbilitiesAttribute()
    {
        return Cache::remember("uid-{$this->id}-abilities", config('session.lifetime') * 60, function () {
            unset($this->roles);

            return $this->roles->map->abilities->flatten()->pluck('name')->unique();
        });
    }

    public function getTimezoneAttribute()
    {
        return 'asia/bangkok';
    }

    public function getRoleNamesAttribute()
    {
        return Cache::remember("uid-{$this->id}-role-names", config('session.lifetime') * 60, function () {
            unset($this->roles);

            return $this->roles->pluck('name');
        });
    }

    public function getHomePageAttribute()
    {
        return 'preferences';
    }

    public function needQuarantine()
    {
        if (! $this->getNotificationChannel()) {
            return 'notification';
        }

        if ($this->role_names->count() === 0) {
            return 'no_role';
        }

        if ($this->next_activation_at->isPast()) {
            return 'reactivation';
        }

        return false;
    }

    /**
     * Assign a new role to the user.
     *
     * @param  mixed  $role
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrCreate(['name' => $role]);
        }
        $this->roles()->syncWithoutDetaching($role);
        unset($this->roles);
        Cache::put("uid-{$this->id}-role-names", $this->roles->pluck('name'), config('session.lifetime') * 60);
        Cache::put("uid-{$this->id}-abilities", $this->roles->map->abilities->flatten()->pluck('name')->unique(), config('session.lifetime') * 60);
    }

    /**
     * revoke a role from the user.
     *
     * @param  mixed  $role
     */
    public function revokeRole($role)
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrCreate(['name' => $role]);
        }
        $this->roles()->detach($role);
        unset($this->roles);
        Cache::put("uid-{$this->id}-role-names", $this->roles->pluck('name'), config('session.lifetime') * 60);
        Cache::put("uid-{$this->id}-abilities", $this->roles->map->abilities->flatten()->pluck('name')->unique(), config('session.lifetime') * 60);
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

    /**
     * Route notifications for the Log channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForLog($notification)
    {
        return $this->getNotificationRecipient('log');
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

    public function assignDivision($division)
    {
        $profile = $this->profile;
        $profile['divisions'] = [$division];
        $this->profile = $profile;

        return $this->save();
    }
}
