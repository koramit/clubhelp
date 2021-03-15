<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class TelegramWebhooksController extends Controller
{
    protected $update;
    protected $baseEndpoint;

    public function __invoke($token)
    {
        if ($token !== config('services.telegram.bot_token')) {
            abort(404);
        }

        $this->baseEndpoint = 'https://api.telegram.org/bot'.config('services.telegram.bot_token').'/';

        $this->update = Request::all();
        Log::info($this->update);

        if (isset($this->update['message'])) { // message and add bot
            if (isset($this->update['message']['text'])) { // text message
                if ($this->update['message']['text'] === '/start' ||
                    $this->update['message']['text'] === '/restart'
                ) {
                    $this->handleSubscribe();
                }
                $this->handleTextMessage();
            }
        } elseif (isset($this->update['my_chat_member'])) { // blocked/unblock
            $this->handleUnsubscribe();
        }
    }

    protected function handleSubscribe()
    {
        $user = User::where('profile->social->id', $this->update['message']['chat']['id'])->first();

        if (! $user) {
            $response = Http::post("{$this->baseEndpoint}sendMessage", [
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => str_replace('PLACEHOLDER', $this->update['message']['chat']['username'], config('messages.bot_user_not_registred')),
            ]);
        }

        if ($user->getNotificationChannel() === null) {
            $user->setNotificationChannel('telegram', $this->update['message']['chat']['id']);
            $response = Http::post("{$this->baseEndpoint}sendMessage", [
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => str_replace('PLACEHOLDER', $user->profile['full_name'], config('messages.bot_greeting')),
            ]);
        }
    }

    protected function handleUnsubscribe()
    {
        $user = User::where('profile->social->id', $this->update['my_chat_member']['chat']['id'])->first();

        if (isset($this->update['my_chat_member']['old_chat_member']) &&
            $this->update['my_chat_member']['old_chat_member']['status'] === 'kicked'
        ) {
            Log::info($this->update['my_chat_member']['chat']['username'].' unsubscrbed');
        }
    }

    protected function handleTextMessage()
    {
        $response = Http::post("{$this->baseEndpoint}sendMessage", [
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => strrev($this->update['message']['text']),
        ]);
    }
}
