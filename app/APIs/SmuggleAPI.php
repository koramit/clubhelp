<?php

namespace App\APIs;

use Illuminate\Support\Facades\Http;

class SmuggleAPI
{
    public function authenticate($login, $password)
    {
        $data = [
            'function' => 'authenticate',
            'org_id'   => $login,
            'password' => $password,
        ];

        $data = $this->makePost($data);
        $data['found'] = $data['reply_code'] === 0;
        unset($data['reply_code']);
        unset($data['reply_text']);
        if (! $data['found']) {
            $data['message'] = __('auth.failed');

            return $data;
        }
        $data['password_expires_in_days'] = $data['password_days_left'];
        unset($data['password_days_left']);

        return $data;
    }

    public function getPatient($hn)
    {
        $data = [
            'function' => 'patient',
            'hn'   => $hn,
        ];
        $data = $this->makePost($data);

        $data['found'] = $data['reply_code'] == 0;
        unset($data['reply_code']);
        unset($data['reply_text']);
        if (! $data['found']) {
            $data['message'] = __('reply_messages.frontend_api.item_not_found', ['item' => 'patient']);

            return $data;
        }

        return $data;
    }

    public function recentlyAdmission($hn)
    {
        $data = [
            'function' => 'recently-admit',
            'hn'   => $hn,
        ];
        $data = $this->makePost($data);

        $data['found'] = $data['reply_code'] == 0;
        unset($data['reply_code']);
        unset($data['reply_text']);
        if ($data['found']) {
            unset($data['patient']['reply_code']);
            unset($data['patient']['reply_text']);
            $data['patient']['found'] = true;

            return $data;
        }

        $data['message'] = __('reply_messages.frontend_api.item_not_found', ['item' => 'admission']);
        $data['patient']['found'] = $data['patient']['reply_code'] == 0;
        unset($data['patient']['reply_code']);
        unset($data['patient']['reply_text']);
        if ($data['patient']['found']) {
            return $data;
        }

        $data['patient']['message'] = __('reply_messages.frontend_api.item_not_found', ['item' => 'patient']);

        return $data;
    }

    protected function makePost($data)
    {
        $response = Http::withHeaders([
                            'Accept' => 'application/json',
                            'token'  => config('services.data_api_token'),
                            'secret' => config('services.data_api_secret'),
                        ])
                        ->timeout(8)
                        ->post('https://sakid.co/smuggle/accio', $data);

        if (! $response->ok()) {
            // handle log
            return ['ok' => false, 'status' => $response->status(), 'message' => $response->body()];
        }

        return $response->json() + ['ok' => true];
    }
}
