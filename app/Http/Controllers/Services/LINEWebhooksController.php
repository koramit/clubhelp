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
        // $response = Http::withToken(config('services.line.bot_token'))
        //                 ->get('https://api.line.me/v2/bot/profile/'.$event['source']['userId']);
        $response = $this->client->get('https://api.line.me/v2/bot/profile/'.$event['source']['userId']);
        $profile = $response->json();
        $user = User::where('profile->social->id', $event['source']['userId'])->first();

        if (! $user) {
            // $url = url('/');
            // $messages = [[
            //     'type' => 'text',
            //     'text' => str_replace('PLACEHOLDER', $profile['displayName'], config('messages.bot_user_not_registred')),
            // ]];
            //bot_user_not_found
            $this->replyMessage($event['replyToken'], [[
                'type' => 'text',
                'text' => str_replace('PLACEHOLDER', $profile['displayName'], config('messages.bot_user_not_registred')),
            ]]);

            return;
        }

        // reply
        if ($user->getNotificationChannel() === null) {
            $user->setNotificationChannel('line', $event['source']['userId']);
            // $messages[] = [[
            //     'type' => 'text',
            //     'text' => str_replace('PLACEHOLDER', $user->profile['full_name'], config('messages.bot_greeting')),
            // ]];
            // bot_greeting
            $this->replyMessage($event['replyToken'], [[
                'type' => 'text',
                'text' => str_replace('PLACEHOLDER', $user->profile['full_name'], config('messages.bot_greeting')),
            ]]);
        }
        // save or update profile
    }

    protected function unfollow($event)
    {
        $user = User::where('profile->social->id', $event['source']['userId'])->first();
        if ($user) {
            $user->disableNotificationChannel('line');
        } else {
            Log::info('guest '.$event['source']['userId'].' unsubscrbed LINE bot');
        }
    }

    protected function message($event)
    {
        $messages = [[
            'type' => 'text',
            'text' => strrev($event['message']['text']),
        ]];
        $this->replyMessage($event['replyToken'], $messages);
    }

    protected function replyMessage($replyToken, $messages)
    {
        // reply
        // $response = Http::withToken(config('services.line.bot_token'))
        $this->client->post('https://api.line.me/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages' => $messages,
        ]);
    }

    protected function pushMessage($userId, $messages)
    {
        // push
        // $response = Http::withToken(config('services.line.bot_token'))

        $this->client->post('https://api.line.me/v2/bot/message/push', [
            'to' => $userId,
            'messages' => $messages,
        ]);
    }
}
