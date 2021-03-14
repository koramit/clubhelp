<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $user->slug = Str::uuid()->toString();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make(Str::random());
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
            'notification_setup' => false,
            'social' => $socialProfile,
        ];
        $user->next_activation_at = now()->addDays($data['password_expires_in_days'] ?? 0);
        $user->save();

        Auth::login($user);

        event(new Registered($user));

        Session::forget('userSocialProfile');

        return redirect('quarantine');
    }
}