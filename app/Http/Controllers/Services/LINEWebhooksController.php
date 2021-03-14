<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
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
        Log::info(['app' => 'LINE', 'payload' => Request::all()]);

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
        $response = Http::withToken(config('services.line.bot_token'))
                        ->get('https://api.line.me/v2/bot/profile/'.$event['source']['userId']);

        Log::info('LINE user profile');
        Log::info($response->body());

        $profile = $response->json();

        // reply
        $messages = [];
        $messages[] = [
            'type' => 'text',
            'text' => "สวัสดี {$profile['displayName']} 😃\nขอบคุณที่เป็นเพื่อนกับ Wordplease 🙏\n\nโปรดลงทะเบียนโดยการพิมพ์ verification code ส่งมาที่นี่เลย\n\n✌️",
        ];
        $this->replyMessage($event['replyToken'], $messages);

        // save or update profile
    }

    protected function unfollow($event)
    {
        Log::info($event['source']['userId'].' unfollow');
    }

    protected function message($event)
    {
        $messages = [];
        $messages[] = [
            'type' => 'text',
            'text' => strrev($event['message']['text']),
        ];
        $this->replyMessage($event['replyToken'], $messages);
    }

    protected function replyMessage($replyToken, $messageObjects)
    {
        // reply
        $response = Http::withToken(config('services.line.bot_token'))
                        ->post('https://api.line.me/v2/bot/message/reply', [
                            'replyToken' => $replyToken,
                            'messages' => $messageObjects,
                        ]);
    }

    protected function pushMessage($userId, $messageObjects)
    {
        // push
        $response = Http::withToken(config('services.line.bot_token'))
                        ->post('https://api.line.me/v2/bot/message/push', [
                            'to' => $userId,
                            'messages' => $messageObjects,
                        ]);
    }
}
