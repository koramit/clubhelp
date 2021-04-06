<?php

namespace App\Http\Controllers;

use App\Contracts\AuthenticationAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class QuarantinedUserController extends Controller
{
    public function index()
    {
        // prevent nosy users
        if (! Auth::user()->needQuarantine()) {
            abort(404);
        }

        // props
        $props = [
            'mode' => Auth::user()->needQuarantine(),
            'socialProvider' => Auth::user()->profile['social']['provider'],
        ];
        $props['botLink'] = config('services.'.$props['socialProvider'])['bot_link_url'] ?? '';

        // title and menu
        Request::session()->flash('page-title', 'Quarantine');
        Request::session()->flash('main-menu-links', []);
        Request::session()->flash('action-menu', []);

        return Inertia::render('Users/Quarantine', $props);
    }

    public function store(AuthenticationAPI $api)
    {
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
