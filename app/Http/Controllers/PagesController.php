<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PagesController extends Controller
{
    public function welcome()
    {
        return Redirect::route('login');
    }

    public function policies()
    {
        return Inertia::render('Policy');
    }
}
