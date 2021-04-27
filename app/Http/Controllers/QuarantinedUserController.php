<?php

namespace App\Http\Controllers;

use App\Contracts\AuthenticationAPI;
use App\Events\InvalidMembership;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class QuarantinedUserController extends Controller
{
    public function index()
    {
        $user = Request::user();
        $mode = $user->needQuarantine();

        // prevent nosy users
        if (! $mode) {
            abort(404);
        }

        // props
        $props = [
            'mode' => $mode,
            'socialProvider' => $user->profile['social']['provider'],
        ];
        $props['botLink'] = config('services.'.$props['socialProvider'])['bot_link_url'] ?? '';

        // title and menu
        Request::session()->flash('page-title', 'Quarantine');
        Request::session()->flash('main-menu-links', []);
        Request::session()->flash('action-menu', []);

        if ($props['mode'] == 'no_role') {
            InvalidMembership::dispatch($user);
        }

        return Inertia::render('Users/Quarantine', $props);
    }

    public function show()
    {
        return Request::user()->getNotificationChannel();
    }

    public function store(AuthenticationAPI $api)
    {
        $apiUser = $api->authenticate(Request::input('login', ''), Request::input('password', ''));
        if (! $apiUser['found']) {
            return $apiUser;
        }

        $sessionUser = Request::user();

        if ($apiUser['org_id'] !== $sessionUser->profile['org_id']) {
            return [
                'found' => false,
                'message' => 'Organization ID not match',
            ];
        }

        $sessionUser->reactivate($apiUser['password_expires_in_days']);

        return [
            'found' => true,
        ];
    }
}
