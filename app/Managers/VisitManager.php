<?php

namespace App\Managers;

use App\Models\Encounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VisitManager
{
    public function manage($patient, $dateVisit)
    {
        // check if visit aready exist
        $dateVisit = Carbon::parse($dateVisit);
        $vn = $dateVisit->format('ymd').$patient->hn;
        $visit = Encounter::where('key_no', $vn)->where('meta->type', 'visit')->first();
        if ($visit) {
            return $visit;
        }

        // return new visit
        return $patient->encounters()->create([
            'key_no' => $dateVisit->format('ymd').$patient->hn,
            'meta' => [
                'type' => 'visit',
                'creator' => Auth::user()->id,
            ],
            'encountered_at' => Carbon::parse($dateVisit->format('Y-m-d').now()->tz(Auth::user()->timezone)->format(' H:i'), Auth::user()->timezone)->tz('UTC'),
        ]);
    }
}
