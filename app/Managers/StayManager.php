<?php

namespace App\Managers;

use App\Models\Encounter;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StayManager
{
    public function manage($patient, $user, $dateVisit = null)
    {
        if (! $dateVisit) {
            $dateVisit = now();
        } else {
            // parse to carbon befor continue
            $dateVisit = Carbon::parse($dateVisit.' '.now()->format('H:i'))->tz('UTC');
        }

        $keyNo = $dateVisit->tz($user->timezone)->format('ymd').$patient->hn;
        $case = Encounter::whereKeyNo($keyNo)->where('meta->type', 'stay')->first();
        if ($case) {
            return $case;
        }

        return $patient->encounters()->create([
            'slug' => Str::uuid()->toString(),
            'key_no' => $keyNo,
            'meta' => [
                'type' => 'stay',
                'hn' => $patient->hn,
                'patient_first_name' => $patient->profile['first_name'],
                'creator' => $user->id,
            ],
            'encountered_at' => $dateVisit,
        ]);
    }
}
