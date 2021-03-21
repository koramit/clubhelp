<?php

namespace App\Http\Controllers;

use App\Managers\SubscriptionManager;
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

        $encounters = (Auth::user()->load([
            'encounters' => fn ($q) => $q->select(['id', 'patient_id', 'slug', 'meta'])
                                         ->with('patient'),
            ])
        )->encounters;
        $encounters = $encounters->transform(fn ($e) => [
            'id' => $e->id,
            'slug' => $e->slug,
            'type' => ucwords($e->meta['type']),
            'patient' => [
                'hn' => $e->patient->hn,
                'fullname' => $e->patient->fullname,
                'first_name' => $e->patient->profile['first_name'],
                'gender' => $e->patient->profile['gender'],
            ],
        ]);
        // $encounters = (Auth::user()->load('encounters'))->encounters;

        return Inertia::render('Encounters/User/Index', ['encounters' => $encounters]);
    }

    public function store()
    {
        // by defauly this method accept encouner id
        // but if patient_id and type are presented that means
        // we should create 'Stay' or 'OPD' encounter by redirecting to the route
        if (Request::has('encounter_id')) { // and check if its invalid
            // not implement yet
            abort(500);
        }

        if (! Request::has('patient_id') || ! (Request::has('type'))) {
            Log::info('user '.Auth::user()->id.' send incomplete request');
            abort(400);
        }

        // patient has no encounters so, make one
        return Redirect::action(
            [EncountersController::class, 'store'],
            ['patient_id' => Request::input('patient_id'), 'type' => Request::input('type')]
        );

        /*
         * Validtion Refactor later 😂
        */
        // if (! Request::has('patient_id') || ! Request::has('type')) {
        //     Log::info('user '.Auth::user()->id.' send incomplete request');
        //     abort(400);
        // }
        // $patient = Patient::find(Request::input('patient_id'));
        // if (! $patient) {
        //     Log::info(Auth::user()->id.' send invalid patient id');
        //     abort(400);
        // }
        // // if (Request::input('type') == 'stay' && ! Request::has('dateVisit')) {
        // //     Log::info(Auth::user()->id.' send incomplete request OPD no dateVisit');
        // //     abort(400);
        // // }

        // // $subscription = (new SubscriptionManager())->manage(Request::input('patient_id'), Request::input('type'), Request::input('dateVisit', null));
        // $subscription = (new SubscriptionManager())->manage($patient, Request::input('type'), Request::input('dateVisit', null));

        // if (! $subscription) {
        //     if (Request::input('type') === 'stay') {
        //         return Redirect::route('patient.cases', ['slug' => Patient::find(Request::input('patient_id'))->slug, 'type' => 'stay']);
        //     }
        //     Log::info('user '.Auth::user()->id.' add case with not exists patient ID '.Request::input('patient_id'));
        //     abort(404);
        // }

        // return Redirect::back();
    }
}
