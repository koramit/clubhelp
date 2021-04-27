<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class UserPreferencesController extends Controller
{
    public function __invoke()
    {
        // title and menu
        Request::session()->flash('page-title', 'Preferences');
        Request::session()->flash('main-menu-links', []);
        Request::session()->flash('action-menu', []);

        return Inertia::render('Users/Preferences');
    }
}
