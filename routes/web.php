<?php

use App\Http\Controllers\Auth\ActivatedUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\EncounterSubscriptionsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PatientDataAPIController;
use App\Http\Controllers\QuarantinedUserController;
use App\Http\Controllers\Services\LINEWebhooksController;
use App\Http\Controllers\Services\TelegramWebhooksController;
use Illuminate\Support\Facades\Route;

// Pages
Route::get('/', [PagesController::class, 'welcome']);
Route::get('/policies', [PagesController::class, 'policies'])->name('policies');

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
Route::middleware('auth')->get('/quarantine', [QuarantinedUserController::class, 'index'])->name('quarantine');
Route::middleware('auth')->post('/quarantine', [QuarantinedUserController::class, 'store']);
Route::middleware('auth')->get('/quarantine/{mode}', [QuarantinedUserController::class, 'show']);

// webhooks
Route::post('/webhooks/line', LINEWebhooksController::class);
Route::post('/webhooks/telegram/{token}', TelegramWebhooksController::class);

// frontend apis
Route::middleware('qualify')->post('/search-patient/{hn}', PatientDataAPIController::class);

// Features
Route::middleware('qualify')->get('/cases', [EncounterSubscriptionsController::class, 'index'])->name('cases');
Route::middleware('qualify')->post('/cases', [EncounterSubscriptionsController::class, 'store']);
