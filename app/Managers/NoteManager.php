<?php

namespace App\Managers;

use App\Models\Encounter;
use App\Models\User;
use App\Notifications\PlainText;
use Illuminate\Support\Str;

class NoteManager
{
    public function create(Encounter $encounter, User $user, $data)
    {
        if ($data['tags']) {
            $data['tags'] = collect($data['tags'])->map(fn ($t) => strtolower($t))->toArray();
        }
        $note = $encounter->notes()->create([
            'slug' => Str::uuid()->toString(),
            'user_id' => $user->id,
            'content' => $data['content'],
            'type' => $data['type'],
            'tags' => $data['tags'],
        ]);

        // create event
        $koko = User::find(1);
        if ($data['type'] === 'consult') {
            $message = str_replace('<br>', "\n", strip_tags($data['content'], '<br>'));
            $users = User::where(function ($query) use ($data) {
                foreach ($data['tags'] as $tag) {
                    $query->orWhereJsonContains('profile->divisions', $tag);
                }
            })->get()
            ->each(fn ($u) => $u->notify(new PlainText($message)));

            // foreach ($users as $user) {
            //     $user->notify(new PlainText($data['content'])); // around 9 seconds  😅
            //         // User::find(1)->notify(new PlainText($data['content'])); // around 6 seconds
            // }
        }

        return $note;
    }
}
