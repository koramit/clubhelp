<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (! Session::has('socialProfile')) {
            return Redirect::route('login');
        }

        return Inertia::render('Auth/Register', ['socialProfile' => Session::get('socialProfile', [])]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['string', 'min:4', 'max:255', 'unique:users'],
            'email' => ['email'],
            'tel_no' => ['digits_between:9,10'],
            'agreement_accepted' => ['required'],
            'org_id' => [
                function ($attribute, $value, $fail) {
                    $userExist = User::where('profile->org_id', $value)->first();
                    if ($userExist) {
                        $fail('This login has already been registered');
                    }
                },
            ],
        ]);
        $data = $request->all();
        $socialProfile = Session::get('socialProfile');
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $secret = Str::random(64);
        $user->password = Hash::make($secret);
        if ($socialProfile['email']) {
            $user->email_verified_at = now();
        }
        unset($socialProfile['email']);
        $user->profile = [
            'login' => $data['login'],
            'full_name' => $data['full_name'],
            'full_name_en' => $data['full_name_en'],
            'tel_no' => $data['tel_no'],
            'org_id' => $data['org_id'],
            'remark' => $data['remark'],
            'divisions' => ['medicine'],
            'notification_channels' => [],
            'logout_other_devices_token' => $secret,
            'social' => $socialProfile,
        ];
        $user->next_activation_at = now()->addDays($data['password_expires_in_days'] ?? 0);
        $user->save();

        Auth::login($user);

        event(new Registered($user));

        Session::forget('socialProfile');

        return Redirect::route('quarantine');
    }
}
