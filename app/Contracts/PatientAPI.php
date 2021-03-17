<?php

namespace App\Contracts;

interface PatientAPI
{
    public function getPatient($hn);

    public function recentlyAdmission($hn);
}
