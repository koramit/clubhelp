<?php

use App\Http\Controllers\Auth\ActivatedUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\QuarantinedUserController;
use App\Http\Controllers\Services\LINEWebhooksController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Redirect::route('login');
});

Route::get('/logo', function () {
    return view('logo');
});

Route::get('/cases', function () {
    return \Inertia\Inertia::render('Encounters/Index');
})->middleware(['auth'])->name('cases');

// login
Route::middleware('guest')->get('/login', [AuthenticatedSessionController::class, 'index'])->name('login');
Route::middleware('guest')->get('/login/{provider}', [AuthenticatedSessionController::class, 'create']);
Route::middleware('guest')->get('/login/{provider}/callback', [AuthenticatedSessionController::class, 'store']);
Route::middleware('auth')->post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// register
Route::middleware('guest')->get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::middleware('guest')->post('/register', [RegisteredUserController::class, 'store']);
Route::middleware('guest')->post('/activate', ActivatedUserController::class);

// quarantine user
Route::middleware('auth')->get('/quarantine', QuarantinedUserController::class)->name('quarantine');

// webhooks
Route::post('/webhooks/line', LINEWebhooksController::class);
