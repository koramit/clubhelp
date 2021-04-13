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
                $socialUser = new LINEAuthUserAPI();
            } elseif ($provider === 'telegram') {
                $socialUser = new TelegramAuthUserAPI();
            } else {
                return abort(404);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return Redirect::route('login'); // SHOULD return WITH NOTICE USER about ERROR
        }

        // for register user
        Session::put('socialProfile', [
            'provider' => $provider,
            'id' => $socialUser->getId(),
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'nickname' => $socialUser->getNickname(),
        ]);

        $user = User::where('profile->social->provider', $provider)
                    ->where('profile->social->id', $socialUser->getId())
                    ->first();

        if ($user) {
            // UPDATE USER SOCIAL PROFILE NOT YET IMPLEMENT
            Auth::login($user);

            return Redirect::intended($user->home_page);
        }

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
