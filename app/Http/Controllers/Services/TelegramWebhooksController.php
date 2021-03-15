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
    protected $user;

    public function __invoke($token)
    {
        if ($token !== config('services.telegram.bot_token')) {
            abort(404);
        }

        $this->baseEndpoint = 'https://api.telegram.org/bot'.config('services.telegram.bot_token').'/';

        $this->update = Request::all();

        if (isset($this->update['message'])) {
            $this->user = User::where('profile->social->id', $this->update['message']['chat']['id'])->first();
            if (isset($this->update['message']['text'])) { // text message
                if ($this->update['message']['text'] === '/start' || // start bot
                    $this->update['message']['text'] === '/restart' // restart bot
                ) {
                    return $this->handleSubscribe();
                }

                return $this->handleTextMessage();
            }
        } elseif (isset($this->update['my_chat_member'])) { // blocked/unblock
            return $this->handleUnsubscribe();
        }
    }

    protected function handleSubscribe()
    {
        if (! $this->user) {
            $this->replyUnauthorized();
            Log::info('guest add telegram bot.'.$this->update['message']['chat']['username']);

            return;
        }

        if ($this->user->getNotificationChannel() === null) {
            $this->user->setNotificationChannel('telegram', $this->update['message']['chat']['id']);
            $response = Http::post("{$this->baseEndpoint}sendMessage", [
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => str_replace('PLACEHOLDER', $this->user->profile['full_name'], config('messages.bot_greeting')),
            ]);
        }
    }

    protected function handleUnsubscribe()
    {
        if (! isset($this->update['my_chat_member']['new_chat_member']) ||
            $this->update['my_chat_member']['new_chat_member']['status'] !== 'kicked'
        ) {
            // do nothing
            return;
        }
        $user = User::where('profile->social->id', $this->update['my_chat_member']['chat']['id'])->first();
        if (! $user) {
            Log::info('guest '.$this->update['my_chat_member']['chat']['username'].' unsubscrbed Telegram bot');

            return;
        }
        $user->disableNotificationChannel('telegram');
        Log::info('user '.$user->profile['full_name'].' unsubscrbed Telegram bot');
    }

    protected function handleTextMessage()
    {
        if (! $this->user) {
            $this->replyUnauthorized();

            return;
        }

        $response = Http::post("{$this->baseEndpoint}sendMessage", [
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => $this->update['message']['text'],
        ]);
    }

    protected function replyUnauthorized()
    {
        Http::post("{$this->baseEndpoint}sendMessage", [
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => str_replace('PLACEHOLDER', $this->update['message']['chat']['username'], config('messages.bot_user_not_registred')),
        ]);
    }
}
