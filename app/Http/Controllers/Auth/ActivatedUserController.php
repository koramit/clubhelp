<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\AuthenticationAPI;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class ActivatedUserController extends Controller
{
    public function __invoke(AuthenticationAPI $api)
    {
        $data = $api->authenticate(Request::input('login'), Request::input('password'));
        if (! $data['found']) {
            return $data;
        }

        $userExist = User::where('profile->org_id', $data['org_id'])->first();
        $login = Request::input('login');
        if (! $userExist) {
            $data['login'] = $login;

            return $data;
        }

        $login = Request::input('login');
        $data['found'] = false;
        $data['message'] = 'This login has already been registered';

        return $data;
    }
}
