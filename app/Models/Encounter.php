<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encounter extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
        'encountered_at' => 'datetime',
        'dismissed_at' => 'datetime',
    ];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function getPatientAgeAtEncounterAttribute()
    {
        $ageInMonths = $this->encountered_at->diffInMonths($this->patient->dob);
        if ($ageInMonths < 12) {
            return $ageInMonths;
        }

        return $this->encountered_at->diffInYears($this->patient->dob);
    }

    public function getPatientAgeAtEncounterUnitAttribute()
    {
        $ageInYears = $this->encountered_at->diffInYears($this->patient->dob);
        if ($ageInYears >= 1) {
            return 'YO';
        }

        return 'MO';
    }
}
