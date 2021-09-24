<?php

namespace App\Services;

class FireBase
{
    public static function pushToUser($token, $payload)
    {
        $client = new \GuzzleHttp\Client();
        $payload['to'] = $token;
//        $payload['icon'] = public_path('icon.png');

        $request = $client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'key='.config('globals.firebase_web_token'),
            ],
            'json' => $payload
        ]);
        $json = $request->getBody()->getContents();
        return $json;
    }

}