<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

    public function users()
    {
        return $this->belongsToMany(User::class);
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

    public function getPageTitleAttribute()
    {
        return implode(' ', [
            'HN',
            $this->patient->hn,
            $this->patient->profile['first_name'],
            ucwords($this->meta['type']),
            $this->encountered_at->tz(Auth::user() ? Auth::user()->timezone : 'UTC')->format('d M Y'),
        ]);
    }

    public function getPageTitleShortAttribute()
    {
        return implode(' ', [
            'HN',
            $this->patient->hn,
            $this->patient->profile['first_name'],
        ]);
    }

    public function encounteredAtRelativeToNow()
    {
        if (! $this->encountered_at) {
            return null;
        }

        return $this->encountered_at->longRelativeToNowDiffForHumans();
    }
}
