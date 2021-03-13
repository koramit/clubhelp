<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'app' => [
                'baseUrl' => url(''),
                // 'photoPath' => config('app.PHOTO_PATH'),
                // 'session_lifetime' => ((int) Config::get('session.lifetime') * 60), // in seconds
            ],
            // 'title' => Session::get('page-title', 'super page'),
            'flash' => function () use ($request) {
                return [
                    // 'success' => Session::get('success'),
                    // 'error' => Session::get('error'),
                    // 'data' => Session::get('data'),
                    'title' => $request->session()->get('page-title', 'MISSING'),
                ];
            },
            'user' => function () {
                // if (! Auth::user()) {
                //     return;
                // }
                // return [
                //     'id' => Auth::id(),
                //     'name' => Auth::user()->name,
                //     'configs' => [
                //         'hideRichTextTools' => true,
                //     ],
                //     'abilities' => Auth::user()->abilities(),
                // ];
                return [
                    'id' => 1,
                    'name' => 'username',
                    'configs' => [
                        // 'hideRichTextTools' => true,
                    ],
                    'mainMenuLinks' => [
                        ['icon' => 'patient', 'label' => 'Patients', 'route' => 'prototypes/PatientsIndex'],
                        ['icon' => 'clinic', 'label' => 'Clinics', 'route' => 'prototypes/ClinicsIndex'],
                        ['icon' => 'procedure', 'label' => 'Procedures', 'route' => 'prototypes/ProceduresIndex'],
                    ],
                    // 'abilities' => Auth::user()->abilities(),
                ];
            },
        ]);
    }
}
