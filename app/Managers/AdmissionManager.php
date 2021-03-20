<?php

namespace App\Managers;

use App\Models\Encounter;
use Illuminate\Support\Str;

class AdmissionManager
{
    public function maintain($admission, $patient = null)
    {
        if (! $patient) {
            // find or new patient
        }

        $admissionRow = Encounter::whereKeyNo($admission['an'])->where('meta->type', 'admission')->first();
        if ($admissionRow) {
            $meta = $admissionRow->meta;
            $meta['discharge_type'] = $admission['discharge_type_name'] ?? null;
            $meta['discharge_status'] = $admission['discharge_status_name'] ?? null;
            $admissionRow->update([
                'dismissed_at' => $admission['dismissed_at'],
                'meta' => $meta,
            ]);
        } else {
            $admissionRow = $patient->encounters()->create([
                                'slug' => Str::uuid()->toString(),
                                'key_no' => $admission['an'],
                                'meta' => [
                                    'type' => 'admission',
                                    'place_name' => $admission['ward_name'] ?? null,
                                    'place_name_short' => $admission['ward_name_short'] ?? null,
                                    'attending' => $admission['attending_name'] ?? null,
                                    'discharge_type' => $admission['discharge_type_name'] ?? null,
                                    'discharge_status' => $admission['discharge_status_name'] ?? null,
                                ],
                                'encountered_at' => $admission['encountered_at'],
                                'dismissed_at' => $admission['dismissed_at'],
                            ]);
        }
    }
}
