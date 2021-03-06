<?php

use App\Http\Controllers\Auth\ActivatedUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\EncounterNotesController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PatientDataAPIController;
use App\Http\Controllers\PatientEncountersController;
use App\Http\Controllers\QuarantinedUserController;
use App\Http\Controllers\Services\AdmissionEncountersController;
use App\Http\Controllers\Services\LINEWebhooksController;
use App\Http\Controllers\Services\StayEncountersController;
use App\Http\Controllers\Services\TelegramWebhooksController;
use App\Http\Controllers\SubscriptionsController;
use App\Http\Controllers\SupportsController;
use App\Http\Controllers\UserPreferencesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* PLEASE REMOVE AFTER TEST */
Route::get('/login-as/{id}', function ($id) {
    if (config('app.env') === 'production') {
        abort(403);
    }
    $user = Auth::loginUsingId($id);

    return redirect()->route($user->home_page);
});

Route::get('/prototype/{page}', function ($page) {
    return Inertia\Inertia::render('Prototype/'.$page);
});

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
Route::middleware('auth')->get('/quarantine/notification', [QuarantinedUserController::class, 'show']);
Route::middleware('auth')->post('/quarantine', [QuarantinedUserController::class, 'store']);

// webhooks
Route::post('/webhooks/line', LINEWebhooksController::class);
Route::post('/webhooks/telegram/{token}', TelegramWebhooksController::class);
Route::post('/webhooks/stay', StayEncountersController::class);
Route::post('/webhooks/admission', AdmissionEncountersController::class);

// frontend apis
Route::middleware('qualify')->post('/search-patient/{hn}', PatientDataAPIController::class);

// Features
Route::middleware('qualify')->get('/preferences', UserPreferencesController::class)->name('preferences');
Route::middleware('qualify')->get('/supports', [SupportsController::class, 'index']);
// locations - ward selection
// lounge - division consult cases
//
Route::middleware('qualify')->get('/cases', fn () => 'cases');

// SubscriptionsController, EncounterNotesController, PatientEncountersController, NotesController
    // Route::middleware('qualify')->get('/cases', [SubscriptionsController::class, 'index'])->name('cases');
// Route::middleware('qualify')->get('/cases/{encounter:slug}/notes', [EncounterNotesController::class, 'index'])->name('case.notes');
// Route::middleware('qualify')->get('/patients/{patient:slug}/cases', [PatientEncountersController::class, 'index'])->name('patient.cases');
// Route::middleware('qualify')->post('/patients/{patient:slug}/cases', [PatientEncountersController::class, 'store'])->name('patient.cases');
// Route::middleware('qualify')->post('/cases/{encounter}/notes', [NotesController::class, 'store']);
// Route::middleware('qualify')->post('/cases', [SubscriptionsController::class, 'store']);
