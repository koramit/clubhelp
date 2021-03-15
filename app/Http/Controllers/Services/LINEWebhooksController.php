<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class LINEWebhooksController extends Controller
{
    protected $client;
    protected $user;

    /**
     * LINE request limit as of 2021-03-14
     * https://developers.line.biz/en/reference/messaging-api/#rate-limits
     * Other API endpoints 2,000 requests per second*.
     */
    public function __invoke()
    {
        $this->client = Http::withToken(config('services.line.bot_token'));

        if (! Request::has('events')) { // this should never happend
            Log::error('LINE bad response');

            return abort(400);
        }

        foreach (Request::input('events') as $event) {
            $this->user = User::where('profile->social->id', $event['source']['userId'])->first();
            if ($event['type'] == 'follow') {
                $this->follow($event);
            } elseif ($event['type'] == 'unfollow') {
                $this->unfollow($event);
            } elseif ($event['type'] == 'message') {
                $this->message($event);
            } elseif ($event['type'] == 'unsend') {
                //
            } else {
                // unhandle type
            }
        }
    }

    protected function follow($event)
    {
        // get profile
        $profile = $this->getProfile($event['source']['userId']);

        if (! $this->user) {
            //bot_user_not_found
            $this->replyUnauthorized($event['replyToken'], $profile['displayName']);

            return;
        }

        // reply
        if ($this->user->getNotificationChannel() === null) {
            $this->user->setNotificationChannel('line', $event['source']['userId']);
            $this->replyMessage($event['replyToken'], [[
                'type' => 'text',
                'text' => str_replace('PLACEHOLDER', $this->user->profile['full_name'], config('messages.bot_greeting')),
            ]]);
        }

        // need save or update profile
    }

    protected function unfollow($event)
    {
        if ($this->user) {
            $this->user->disableNotificationChannel('line');
        } else {
            Log::info('guest '.$event['source']['userId'].' unsubscrbed LINE bot');
        }
    }

    protected function message($event)
    {
        if (! $this->user) {
            $profile = $this->getProfile($event['source']['userId']);
            $this->replyUnauthorized($event['replyToken'], $profile['displayName']);
        }
        $messages = [[
            'type' => 'text',
            'text' => $event['message']['text'],
        ]];
        $this->replyMessage($event['replyToken'], $messages);
    }

    protected function replyMessage($replyToken, $messages)
    {
        $this->client->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => $messages,
        ]);
    }

    protected function pushMessage($userId, $messages)
    {
        $this->client->post('https://api.line.me/v2/bot/message/push', [
            'to' => $userId,
            'messages' => $messages,
        ]);
    }

    protected function getProfile($userId)
    {
        $response = $this->client->get('https://api.line.me/v2/bot/profile/'.$userId);

        return $response->json();
    }

    protected function replyUnauthorized($token, $username)
    {
        $this->replyMessage($token, [[
            'type' => 'text',
            'text' => str_replace('PLACEHOLDER', $username, config('messages.bot_user_not_registred'))."\n\n เมื่อทำการลงทะเบียนแล้วอย่าลืม block และ unblock bot ด้วยน๊า 🤗",
        ]]);
    }
}
