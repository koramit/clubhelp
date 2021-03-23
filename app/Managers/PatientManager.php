<?php

namespace App\Managers;

use App\Models\Patient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PatientManager
{
    public function manage($hn)
    {
        // search HN in DB
        $patient = Patient::findByEncryptedKey($hn);
        if (! $patient) {
            $data = $this->callAPI($hn);
            if (! $data['found']) {
                return $data;
            }

            $patient = Patient::create(['hn' => $hn, 'slug' => Str::uuid()->toString(), 'profile' => $this->mapProfile($data)]);
            if ($data['admission']['found']) {
                (new AdmissionManager())->maintain($data['admission'], $patient);
            }

            return [
                'found' => true,
                'patient' => $patient,
            ];
        }

        // determine if update needed
        if ($patient->updated_at->diffInDays(now()) <= 1) {
            return [
                'found' => true,
                'patient' => $patient,
            ];
        }

        $data = $this->callAPI($hn);
        if (! $data['found']) {
            Log::info($hn.' hn cancle or something went wrong');

            return $patient;
        }

        $patient = $this->update($patient, $this->mapProfile($data));
        if ($data['admission']['found']) {
            (new AdmissionManager())->maintain($data['admission'], $patient);
        }

        return [
            'found' => true,
            'patient' => $patient,
        ];
    }

    protected function callAPI($hn)
    {
        $data = app()->make('App\Contracts\PatientAPI')->recentlyAdmission($hn);

        $patient = $data['patient'];
        unset($data['patient']);
        $patient['admission'] = $data;

        return $patient;
    }

    public function update($patient, $profile)
    {
        $oldProfile = $patient->profile;

        if ($oldProfile['title'] !== $profile['title'] || // check if name changed
            $oldProfile['first_name'] !== $profile['first_name'] ||
            $oldProfile['last_name'] !== $profile['last_name']
        ) {
            if (isset($oldProfile['old_names'])) {
                $oldProfile['old_names'] = [];
            }
            $oldProfile['old_names'][] = implode(' ', [$oldProfile['title'], $oldProfile['first_name'], $oldProfile['last_name']]);
        }

        foreach ($profile as $key => $value) {
            if (isset($oldProfile[$key])) {
                $oldProfile[$key] = $value;
            }
        }
        $patient->update(['profile' => $oldProfile]);

        return $patient;
    }

    protected function mapProfile($data)
    {
        return [
            'dob' => $data['dob'],
            'gender' => $data['gender'] == 1 ? 'male' : 'female',
            'title' => $data['title'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'document_id' => $data['document_id'],
            'insurance' => $data['insurance_name'],
            'marital_status' => $data['marital_status_name'],
            'spouse' => $data['spouse'],
            'race' => $data['race'],
            'nation' => $data['nation'],
            'tel_no' => $data['tel_no'],
            'address' => $data['address'],
            'location' => $data['location'],
            'province' => $data['province'],
            'alternative_contact' => $data['alternative_contact'],
        ];
    }
}
