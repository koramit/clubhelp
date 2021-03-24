<?php

namespace App\APIs;

use App\Contracts\AuthenticationAPI;
use App\Contracts\PatientAPI;
use Illuminate\Support\Facades\Http;

class ToothpasteAPI implements PatientAPI, AuthenticationAPI
{
    public function authenticate($login, $password)
    {
        return $this->brushing($this->pasteLoad('authenticate', ['login' => $login, 'password' => $password]));
    }

    public function getPatient($hn)
    {
        return $this->brushing($this->pasteLoad('patient', ['hn' => $hn]));
    }

    public function recentlyAdmission($hn)
    {
        return $this->brushing($this->pasteLoad('recently_admit', ['hn' => $hn]));
    }

    protected function brushing($data)
    {
        $response = Http::timeout(5)
                    ->withOptions(['verify' => false])
                    ->asForm()
                    ->post(config('services.toothpaste.url'), ['payload' => $data]);

        if ($response->ok()) {
            return $response->json();
        }

        return [
            'ok' => false,
            'found' => false,
            'message' => $response->json()['message'],
        ];
    }

    protected function pasteLoad($service, $data)
    {
        $config = config('services.toothpaste')[$service];
        $data = [
            'endpoint' => $config['endpoint'],
            'data' => $data,
            'callgate_token' => config('services.toothpaste.token'),
        ];

        if ($config['auth'] === 'token_secret') {
            $data['api_app'] = $config['app'];
            $data['api_token'] = $config['token'];
        } else {
            $data['bearer'] = $config['bearer'];
        }

        return json_encode($data)
    }
}
