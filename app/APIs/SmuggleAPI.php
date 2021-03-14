<?php

namespace App\APIs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class SmuggleAPI
{
    public function authenticate($login, $password)
    {
        $data = [
            'function' => 'authenticate',
            'org_id'   => Request::input('login'),
            'password' => Request::input('password'),
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

    protected function makePost($data)
    {
        $response = Http::withHeaders([
                            'Accept' => 'application/json',
                            'token'  => 'NJwjgOKTuHYdggzcwupCKqZT53ce6ODfx0mO8p3KXfGyAJ0dXBVMBi926QRlC6CK',
                            'secret' => 'secret',
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
