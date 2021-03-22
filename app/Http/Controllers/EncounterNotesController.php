<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class EncounterNotesController extends Controller
{
    public function index(Encounter $encounter)
    {
        Request::session()->flash('main-menu-links', [
            ['icon' => 'clipboard-list', 'label' => 'My Cases', 'route' => 'cases'],
            // ['icon' => 'clinic', 'label' => 'Clinics', 'route' => 'prototypes/ClinicsIndex'],
            // ['icon' => 'procedure', 'label' => 'Procedures', 'route' => 'prototypes/ProceduresIndex'],
        ]);

        Request::session()->flash('action-menu', [
            // ['icon' => 'wheelchair', 'label' => 'Add Stay case', 'action' => 'add-stay-case'],
            // ['icon' => 'ambulance', 'label' => 'Add Stay case without HN (soon... 😤)', 'action' => 'not-ready'],
            // ['icon' => 'procedure', 'label' => 'Add IPD case (later... 😅)', 'action' => 'not-ready'],
        ]);

        Request::session()->flash('page-title', $encounter->page_title_short);

        $encounter->load(['notes' => fn ($q) => $q->select(['encounter_id', 'content', 'slug', 'user_id', 'created_at'])]);
        $encounter->notes->transform(fn ($n) => [
            'content' => $n->content,
            'user_id' => $n->user_id,
            'slug' => $n->slug,
            'created_at' => $n->created_at->longRelativeToNowDiffForHumans(),
        ]);

        $encounter = [
            'id' => $encounter->id,
            'meta' => $encounter->meta,
            'encountered_at' => $encounter->encountered_at->tz(Auth::user()->timezone)->format('d M Y'),
            'notes' => $encounter->notes,
        ];

        return Inertia::render('Encounters/Show', ['encounter' => $encounter]);
    }
}
