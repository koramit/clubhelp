<?php

namespace App\APIs;

use App\Contracts\AuthenticationAPI;
use App\Contracts\PatientAPI;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ToothpasteAPI implements PatientAPI, AuthenticationAPI
{
    public function authenticate($login, $password)
    {
        $password = str_replace('+', 'BuAgSiGn', $password);
        $password = str_replace('%', 'PeRcEnTsIgN', $password);
        $password = str_replace('&', 'LaEsIgN', $password);
        $password = str_replace('=', 'TaOkUbSiGn', $password);

        $data = $this->brushing($this->pasteLoad('authenticate', ['login' => $login, 'password' => $password]));
        if (! $data['found']) {
            $data['message'] = __('auth.failed');
            unset($data['UserInfo']);
            unset($data['body']);

            return $data;
        }

        return [
            'ok' => $data['ok'],
            'found' => $data['found'],
            'username' => $data['login'],
            'name' => $data['full_name'],
            'name_en' => $data['full_name_en'],
            'email' => $data['email'],
            'org_id' => $data['org_id'],
            'tel_no' => $data['tel_no'] ?? null,
            'document_id' => null,
            'org_division_name' => $data['division_name'],
            'org_position_title' => $data['position_name'],
            'remark' => $data['remark'],
            'password_expires_in_days' => $data['password_expires_in_days'],
        ];
    }

    public function getPatient($hn)
    {
        $data = $this->brushing($this->pasteLoad('patient', ['hn' => $hn]));
        if (! $data['found']) {
            $data['message'] = __('reply_messages.frontend_api.item_not_found', ['item' => 'patient']);
            unset($data['body']);

            return $data;
        }

        return $data;
    }

    public function recentlyAdmission($hn)
    {
        $data = $this->brushing($this->pasteLoad('recently_admit', ['hn' => $hn]));

        if ($data['found']) {
            $data['patient']['found'] = true;
            $data['attending_name'] = $data['attending'];
            $data['discharge_type_name'] = $data['discharge_type'];
            $data['discharge_status_name'] = $data['discharge_status'];
            $data['encountered_at'] = $data['admitted_at'] ? Carbon::parse($data['admitted_at'], 'asia/bangkok')->tz('UTC') : null;
            $data['dismissed_at'] = $data['discharged_at'] ? Carbon::parse($data['discharged_at'], 'asia/bangkok')->tz('UTC') : null;
            $data['patient']['marital_status_name'] = $data['patient']['marital_status'];
            $data['patient']['location'] = $data['patient']['postcode'];

            return $data;
        }

        $data['message'] = __('reply_messages.frontend_api.item_not_found', ['item' => 'admission']);
        if ($data['patient']['found']) {
            $data['patient']['marital_status_name'] = $data['patient']['marital_status'];
            $data['patient']['location'] = $data['patient']['postcode'];

            return $data;
        }

        $data['patient']['message'] = __('reply_messages.frontend_api.item_not_found', ['item' => 'patient']);

        return $data;
    }

    public function venti($id)
    {
        return $this->brushing($this->pasteLoad('venti', ['id' => $id]));
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
            'message' => $response->json()['message'] ?? 'not available',
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

        return json_encode($data);
    }
}
