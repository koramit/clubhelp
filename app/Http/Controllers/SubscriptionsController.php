<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class SubscriptionsController extends Controller
{
    public function index()
    {
        Request::session()->flash('page-title', 'My Cases');
        Request::session()->flash('main-menu-links', [
            // ['icon' => 'patient', 'label' => 'Patients', 'route' => 'prototypes/PatientsIndex'],
            // ['icon' => 'clinic', 'label' => 'Clinics', 'route' => 'prototypes/ClinicsIndex'],
            // ['icon' => 'procedure', 'label' => 'Procedures', 'route' => 'prototypes/ProceduresIndex'],
        ]);

        Request::session()->flash('action-menu', [
            ['icon' => 'wheelchair', 'label' => 'Add Stay case', 'action' => 'add-stay-case'],
            ['icon' => 'ambulance', 'label' => 'Add Stay case without HN (soon... 😤)', 'action' => 'not-ready'],
            ['icon' => 'procedure', 'label' => 'Add IPD case (later... 😅)', 'action' => 'not-ready'],
        ]);

        $user = Auth::user();

        $encounters = ($user->load([
            'encounters' => fn ($q) => $q->select(['id', 'patient_id', 'slug', 'meta', 'encountered_at'])
                                         ->with('patient'), // ->wherePivotIn('status', ['unlisted'])
            ])
        )->encounters;
        $encounters = $encounters->transform(fn ($e) => [
            'id' => $e->id,
            'slug' => $e->slug,
            'type' => ucwords($e->meta['type']),
            'encountered_at' => $e->encountered_at->tz($user->timezone)->format('d M Y'),
            'patient' => [
                'hn' => $e->patient->hn,
                'fullname' => $e->patient->fullname,
                'first_name' => $e->patient->profile['first_name'],
                'gender' => $e->patient->profile['gender'],
            ],
            'subscription' => [
                'status' => $e->subscription->status,
                'as' => $e->subscription->as,
            ],
        ]);

        // check if user has service requests
        $requests = Encounter::with('patient')
                             ->whereHas('notes', function ($query) use ($user) {
                                 $query->whereType('consult')
                                       ->whereJsonContains('tags', $user->profile['divisions'][0]);
                             })->whereNotIn('id', $encounters->pluck('id')->toArray())
                             ->get();

        $requests = $requests->transform(fn ($e) => [
            'id' => $e->id,
            'slug' => $e->slug,
            'type' => ucwords($e->meta['type']),
            'encountered_at' => $e->encountered_at->tz($user->timezone)->format('d M Y'),
            'patient' => [
                'hn' => $e->patient->hn,
                'fullname' => $e->patient->fullname,
                'first_name' => $e->patient->profile['first_name'],
                'gender' => $e->patient->profile['gender'],
            ],
        ]);

        return Inertia::render('Encounters/User/Index', ['encounters' => $encounters, 'requests' => $requests]);
    }

    public function store()
    {
        if (! Request::has('id') || ! Request::has('as')) { // and check if id is invalid
            Log::info('user '.Auth::user()->id.' send incomplete request');
            abort(400);
        }

        Auth::user()->encounters()->syncWithoutDetaching([
            Request::input('id') => ['status' => 'enlisted', 'as' => Request::input('as')],
        ]);

        return Redirect::route('cases');

        // if (! Request::has('patient_id') || ! (Request::has('type'))) {
        //     Log::info('user '.Auth::user()->id.' send incomplete request');
        //     abort(400);
        // }

        // // patient has no encounters so, make one
        // return Redirect::action(
        //     [EncountersController::class, 'store'],
        //     ['patient_id' => Request::input('patient_id'), 'type' => Request::input('type')]
        // );
    }
}
