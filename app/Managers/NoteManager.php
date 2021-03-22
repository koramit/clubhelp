<?php

namespace App\Managers;

use App\Models\Encounter;
use App\Models\User;
use Illuminate\Support\Str;

class NoteManager
{
    public function create(Encounter $encounter, User $user, $content, $type)
    {
        $note = $encounter->notes()->create([
            'slug' => Str::uuid()->toString(),
            'user_id' => $user->id,
            'content' => $content,
            'type' => $type,
        ]);

        // create event

        return $note;
    }
}
