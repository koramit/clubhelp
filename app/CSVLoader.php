<?php

namespace App;

use App\Traits\CSVReadable;

class CSVLoader
{
    use CSVReadable;

    public function attending()
    {
        $attendings = collect($this->loadCSV(storage_path('app/seeders/med_attendings_div.csv')));
        $idAttendings = collect($this->loadCSV(storage_path('app/seeders/med_attendings.csv')));

        return $attendings->filter(function ($attending) {
            return $attending['department'] === 'Medicine';
        })->map(function ($attending) use ($idAttendings) {
            $index = $idAttendings->search(function ($id) use ($attending) {
                return $id['license_no'] === $attending['license_no'];
            });
            if ($index !== false) {
                $attending['org_id'] = $idAttendings[$index]['org_id'];
            } else {
                $attending['org_id'] = null;
            }

            return [
                'org_id' => $attending['org_id'],
                'division' =>  $attending['division'],
                'role' => 'attending',
            ];
        })->filter(function ($attending) {
            return $attending['org_id'] !== null;
        })->values();

        // 10026018 tassanee GI
    }

    public function md()
    {
        return collect($this->loadCSV(storage_path('app/seeders/med_rf.csv')))->values();
    }

    public function med()
    {
        $merge = $this->attending()->toArray() + $this->md()->toArray();

        return json_encode($merge);
    }
}
