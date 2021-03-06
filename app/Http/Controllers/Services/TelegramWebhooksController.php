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

        $this->baseEndpoint = config('services.telegram.base_endpoint');

        $this->update = Request::all();

        if (isset($this->update['message'])) {
            $this->user = User::where('profile->social->id', strval($this->update['message']['chat']['id']))->first();
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
            Log::info('guest add telegram bot '.$this->update['message']['chat']['username']);

            return;
        }

        if ($this->user->getNotificationChannel() === null) {
            $this->user->setNotificationChannel('telegram', $this->update['message']['chat']['id']);
            Http::post("{$this->baseEndpoint}sendMessage", [
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => __('reply_messages.bot.greeting', ['PLACEHOLDER' => $this->user->profile['full_name']]),
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
        $user = User::where('profile->social->id', strval($this->update['my_chat_member']['chat']['id']))->first();
        if ($user) {
            $user->disableNotificationChannel('telegram');
        } else {
            Log::info('guest '.$this->update['my_chat_member']['chat']['username'].' unsubscribed Telegram bot');
        }
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
            'text' => __('reply_messages.bot.user_not_registered', ['PLACEHOLDER' => $this->update['message']['chat']['username'], 'STOP' => 'stop', 'RESTART' => 'restart']),
        ]);
    }
}
