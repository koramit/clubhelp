<?php

namespace App\Http\Controllers;

use App\Managers\PatientManager;
use Carbon\Carbon;

class PatientDataAPIController extends Controller
{
    public function __invoke($hn)
    {
        $data = (new PatientManager())->manage($hn);

        if (! $data['found']) {
            return $data;
        }

        return [
            'found' => true,
            'hn' => $hn,
            'name' => $data['patient']->full_name,
            'gender' => $data['patient']->profile['gender'],
            'age' => $data['patient']->age_in_years ? ($data['patient']->age_in_years.' Yo') : null,
            'insurance' => $data['patient']->profile['insurance'],
            'admission' => 'not implement yet',
            // 'admission' => isset($data['datetime_admit']) ?
            //         'Admitted at Siriraj '.Carbon::parse($data['datetime_admit'])->longRelativeToNowDiffForHumans() :
            //         'Never admitted at Siriraj',
        ];
    }
}
