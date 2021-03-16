<?php

namespace App\Http\Controllers;

use App\APIs\SmuggleAPI;
use Carbon\Carbon;

class PatientDataAPIController extends Controller
{
    public function __invoke($hn)
    {
        $api = new SmuggleAPI();

        $data = $api->recentlyAdmission($hn);
        if (! $data['patient']['found']) {
            return [
                'found' => false,
                'message' => $data['patient']['message'],
            ];
        }

        return [
            'found' => true,
            'hn' => $hn,
            'name' => $data['patient']['patient_name'],
            'gender' => $data['patient']['gender'] == 0 ? 'female' : 'male',
            'age' => now()->diffInYears(Carbon::parse($data['patient']['dob'])).' Yo',
            'insurance' => $data['patient']['insurance_name'],
            'admission' => isset($data['datetime_admit']) ?
                    'Admitted at Siriraj '.Carbon::parse($data['datetime_admit'])->longRelativeToNowDiffForHumans() :
                    'Never admitted at Siriraj',
        ];
    }
}
