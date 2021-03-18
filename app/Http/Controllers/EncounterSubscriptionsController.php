<?php

namespace App\Http\Controllers;

use App\Managers\SubscriptionManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class EncounterSubscriptionsController extends Controller
{
    public function index()
    {
        Request::session()->flash('page-title', 'Cases');
        // Request::session()->flash('main-menu-links', [
        //     ['icon' => 'patient', 'label' => 'Patients', 'route' => 'prototypes/PatientsIndex'],
        //     ['icon' => 'clinic', 'label' => 'Clinics', 'route' => 'prototypes/ClinicsIndex'],
        //     ['icon' => 'procedure', 'label' => 'Procedures', 'route' => 'prototypes/ProceduresIndex'],
        // ]);

        Request::session()->flash('action-menu', [
            ['icon' => 'wheelchair', 'label' => 'Add OPD case', 'action' => 'add-opd-case'],
            ['icon' => 'ambulance', 'label' => 'Add OPD case without HN (soon... 😤)', 'action' => 'not-ready'],
            ['icon' => 'procedure', 'label' => 'Add IPD case (later... 😅)', 'action' => 'not-ready'],
        ]);

        return Inertia::render('Encounters/Index', ['cases' => Auth::user()->encounters]);
    }

    public function store()
    {
        /*
         * Validtion Refactor later 😂
        */
        if (! Request::has('id') || ! Request::has('type')) {
            Log::info('user '.Auth::user()->id.' send incomplete request');
            abort(400);
        }
        if (Request::input('type') == 'opd' && ! Request::has('dateVisit')) {
            Log::info(Auth::user()->id.' send incomplete request OPD no dateVisit');
            abort(400);
        }

        $subscription = (new SubscriptionManager())->manage(Request::input('id'), Request::input('type'), Request::input('dateVisit', null));

        if (! $subscription) {
            Log::info('user '.Auth::user()->id.' add case with not exists patient ID '.Request::input('id'));
            abort(404);
        }

        return Redirect::back();
    }
}
