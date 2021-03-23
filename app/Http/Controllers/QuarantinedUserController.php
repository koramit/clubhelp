<?php

namespace App\Http\Controllers;

use App\APIs\SmuggleAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class QuarantinedUserController extends Controller
{
    public function index()
    {
        if (! Auth::user()->needQuarantine()) {
            abort(404);
        }
        $props = [
            'mode' => Auth::user()->needQuarantine(),
            'socialProvider' => Auth::user()->profile['social']['provider'],
        ];
        $props['botLink'] = config('services.'.$props['socialProvider'])['bot_link_url'] ?? '';

        return Inertia::render('Users/Quarantine', $props);
    }

    public function show($mode)
    {
        if ($mode === 'notification') {
            return Auth::user()->getNotificationChannel();
        }

        if ($mode === 'reactivation') {
            $api = new SmuggleAPI();
        }

        return null;
    }

    public function store()
    {
        $api = new SmuggleAPI();
        $user = $api->authenticate(Request::input('login', ''), Request::input('password', ''));
        if (! $user['found']) {
            return $user;
        }

        if ($user['org_id'] !== Auth::user()->profile['org_id']) {
            return [
                'found' => false,
                'message' => 'Organization ID not match',
            ];
        }

        Auth::user()->reactivate($user['password_expires_in_days']);

        return [
            'found' => true,
        ];
    }
}
