<?php

namespace App\Http\Controllers;

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

        return Inertia::render('Encounters/Index');
    }

    public function store()
    {
        // if ()
        // 1. check if new visit
        // 2. check if already visit
        // 3. check if already subscribed
    }
}
