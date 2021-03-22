<?php

namespace App\Http\Controllers;

use App\Managers\NoteManager;
use App\Models\Encounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class NotesController extends Controller
{
    public function store(Encounter $encounter)
    {
        // check type note|consult|service
        if (count(Request::input('consult', [])) > 0) {
            $type = 'consult';
        } else {
            $type = 'note';
        }
        $note = (new NoteManager())->create($encounter, Auth::user(), Request::input('content'), $type);

        return Redirect::back();
    }
}
