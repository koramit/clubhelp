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
        $note = (new NoteManager())->create($encounter, Auth::user(), Request::input('content'), Request::input('type'));

        return Redirect::back();
    }
}
