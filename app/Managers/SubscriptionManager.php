<?php

namespace App\Managers;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class SubscriptionManager
{
    public function manage($id, $type, $dateVisit)
    {
        if ($type === 'opd') {
            return $this->subscribeOPDCase($id, $dateVisit);
        } elseif ($type === 'admission') {
            return $this->subscribeIPDCase($id);
        } else {
            return false;
        }
    }

    protected function subscribeOPDCase($id, $dateVisit)
    {
        $patient = Patient::withLastVisit()->find($id);
        if (! $patient) {
            return false;
        }
        $visit = (new VisitManager())->manage($patient, $dateVisit);

        return Auth::user()->encounters()->syncWithoutDetaching([$visit->id => ['status' => 'active']]); // return summary array
    }

    protected function subscribeIPDCase($id)
    {
        // return false;
        abort(404);
    }
}
