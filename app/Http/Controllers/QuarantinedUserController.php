<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class QuarantinedUserController extends Controller
{
    public function __invoke()
    {
        $props = [
            'mode' => Auth::user()->needQuarantine(),
            'socialProvider' => Auth::user()->profile['social']['provider'],
        ];
        $props['botLink'] = config('services.'.$props['socialProvider'])['bot_link_url'] ?? '';

        return Inertia::render('Users/Quarantine', $props);
    }
}
