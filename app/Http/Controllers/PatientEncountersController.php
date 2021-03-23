<?php

namespace App\Http\Controllers;

use App\Managers\StayManager;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PatientEncountersController extends Controller
{
    public function index(Patient $patient)
    {
        $type = Request::input('type', null);
        if ($type) {
            $patient = Patient::withEncountersByType($type)->find($patient->id);
            $user = Auth::user();
            if ($patient->encounters->count() === 0) {
                // create
                if ($type === 'stay') {
                    $case = (new StayManager())->manage($patient, $user);
                } else {
                    abort(400);
                }
                $user->encounters()->syncWithoutDetaching([
                    $case->id => ['status' => 'enlisted', 'as' => 'md'],
                ]);

                return Redirect::route('case.notes', [$case]);
            }
        }

        Request::session()->flash('page-title', 'HN '.$patient->hn.' '.$patient->profile['first_name'].' '.ucwords($type ?? 'All'));

        Request::session()->flash('action-menu', [
            ['icon' => 'wheelchair', 'label' => 'Create Stay case', 'action' => 'create-stay-case'],
            // ['icon' => 'ambulance', 'label' => 'Add Stay case without HN (soon... 😤)', 'action' => 'not-ready'],
            // ['icon' => 'procedure', 'label' => 'Add IPD case (later... 😅)', 'action' => 'not-ready'],
        ]);

        return Inertia::render('Encounters/Patient/Index', [
            'patient' => [
                'hn' => $patient->hn,
                'slug' => $patient->slug,
                'name' => $patient->full_name,
                'gender' => $patient->profile['gender'],
                'age' => $patient->age_in_years ? ($patient->age_in_years.' Yo') : null,
                'insurance' => $patient->profile['insurance'],
            ],
            'encounters' => $patient->encounters->transform(fn ($e) => [
                    'id' => $e->id,
                    'slug' => $e->slug,
                    'type' => ucwords($e->meta['type']),
                    'encountered_at' => $e->encountered_at->tz($user->timezone)->format('d M Y'),
                ]),
            ]);
    }

    public function store(Patient $patient)
    {
        $type = Request::input('type', null);
        $user = Auth::user();

        if ($type === 'stay') {
            $case = (new StayManager())->manage($patient, $user, Request::input('date_visit'));
        } else {
            abort(400);
        }
        $user->encounters()->syncWithoutDetaching([
            $case->id => ['status' => 'enlisted', 'as' => 'md'],
        ]);

        return Redirect::route('case.notes', ['encounter' => $case]);
    }
}
