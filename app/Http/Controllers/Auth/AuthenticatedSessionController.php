<?php

namespace App\Http\Controllers\Auth;

use App\APIs\LINEAuthUserAPI;
use App\APIs\TelegramAuthUserAPI;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return Inertia::render('Auth/Login', ['configs' => [
            'telegram' => config('services.telegram'),
        ]]);
    }

    public function create($provider)
    {
        if ($provider === 'line') {
            return LINEAuthUserAPI::redirect();
        } elseif ($provider === 'telegram') {
            return TelegramAuthUserAPI::redirect();
        } else {
            return abort(404);
        }
    }

    public function store($provider)
    {
        try {
            if ($provider === 'line') {
                $user = new LINEAuthUserAPI();
            } elseif ($provider === 'telegram') {
                $user = new TelegramAuthUserAPI();
            } else {
                return abort(404);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Redirect::route('login'); // SHOULD return WITH NOTICE USER about ERROR
        }

        $userExist = User::where('profile->social->provider', $provider)
                         ->where('profile->social->id', $user->getId())
                         ->first();

        if ($userExist) {
            // UPDATE USER SOCIAL PROFILE NOT YET IMPLEMENT

            Auth::login($userExist);

            // check if user need quarantine
            if ($userExist->needQuarantine()) {
                return Redirect::route('quarantine');
            }

            return Redirect::intended($userExist->home_page);
        }

        // register user
        Session::put('socialProfile', [
            'provider' => $provider,
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'avatar' => $user->getAvatar(),
            'nickname' => $user->getNickname(),
        ]);

        return Redirect::route('register');
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        Auth::guard('web')->logout();

        Request::session()->invalidate();

        Request::session()->regenerateToken();

        return redirect('/');
    }
}
