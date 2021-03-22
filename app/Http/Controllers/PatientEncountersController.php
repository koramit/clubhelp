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
                    $case->id => ['status' => 'enlisted', 'as' => 'personal'],
                ]);

                return Redirect::route('case.notes', [$case]);
            }
        }

        Request::session()->flash('page-title', $patient->profile['first_name'].' '.$type ?? 'All'.' cases');

        return Inertia::render('Encounters/Patient/Index', ['cases' => $patient->encounters]);
    }
}
