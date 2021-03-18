<?php

namespace App\Http\Controllers;

use App\Managers\PatientManager;
use App\Models\Patient;

class PatientDataAPIController extends Controller
{
    public function __invoke($hn)
    {
        $data = (new PatientManager())->manage($hn);

        if (! $data['found']) {
            return $data;
        }

        $patient = Patient::withLastAdmission()->find($data['patient']->id);

        return [
            'found' => true,
            'id' => $patient->id,
            'hn' => $hn,
            'name' => $patient->full_name,
            'gender' => $patient->profile['gender'],
            'age' => $patient->age_in_years ? ($patient->age_in_years.' Yo') : null,
            'insurance' => $patient->profile['insurance'],
            'admission' => $patient->lastAdmission ?
                    'Admitted at Siriraj '.$patient->lastAdmission->encounteredAtRelativeToNow() :
                    'Never admitted at Siriraj',
        ];
    }
}
