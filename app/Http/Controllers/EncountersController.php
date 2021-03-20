<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class EncountersController extends Controller
{
    public function show(Encounter $encounter)
    {
        Request::session()->flash('main-menu-links', [
            ['icon' => 'clipboard-list', 'label' => 'My Cases', 'route' => 'cases'],
            // ['icon' => 'clinic', 'label' => 'Clinics', 'route' => 'prototypes/ClinicsIndex'],
            // ['icon' => 'procedure', 'label' => 'Procedures', 'route' => 'prototypes/ProceduresIndex'],
        ]);

        Request::session()->flash('action-menu', [
            // ['icon' => 'wheelchair', 'label' => 'Add Stay case', 'action' => 'add-stay-case'],
            // ['icon' => 'ambulance', 'label' => 'Add Stay case without HN (soon... 😤)', 'action' => 'not-ready'],
            // ['icon' => 'procedure', 'label' => 'Add IPD case (later... 😅)', 'action' => 'not-ready'],
        ]);

        Request::session()->flash('page-title', $encounter->page_title_short);

        return Inertia::render('Encounters/Show', ['encounter' => $encounter]);
    }

    public function store()
    {
        // $patient = Patient::withEncountersByType(Request::input('type'))->find(Request::input('patient_id'));
        // $subscription = (new SubscriptionManager())->manage($patient, Request::input('type'), Request::input('dateVisit', null));
    }
}
