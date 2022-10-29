<?php

namespace App\sdks\slack;

use App\sdks\slack\Requests;

class Slack
{
    public static function getUserInfo($userId, $token)
    {
        $response = Requests::getUserInfoRequest($userId, $token);
        if($response['http_code'] != 200){
           return [
                'error' => true,
                'data' => $response['body']
              ];
        }

        $response = json_decode($response['body'], true);

        if($response['ok'] == false){
            return [
                'error' => true,
                'data' => $response['error']
            ];
        }

        return [
            'error' => false,
            'user' => [
                'id' => $response['user']['id'] ?? null,
                'name' => $response['user']['name'] ?? null,
                'real_name' => $response['user']['real_name'] ?? null,
                'image' => $response['user']['profile']['image_512'] ?? $response['user']['profile']['image_original'] ?? null,
                'profile_link' => "https://slack.com/app_redirect?channel=".$response['user']['id']
            ]
        ];
    }



    public static function getPermalink($channel, $ts, $token)
    {
        $response = Requests::getPermalinkRequest($channel, $ts, $token);
        if($response['http_code'] != 200){
           return [
                'error' => true,
                'data' => $response['body']
            ];
        }

        $response = json_decode($response['body'], true);

        if($response['ok'] == false){
            return [
                'error' => true,
                'data' => $response['error']
            ];
        }

        return [
            'error' => false,
            'permalink' => $response['permalink']
        ];
    }


    public static function getChannel($channel, $token)
    {
        $response = Requests::getChannelRequest($channel, $token);
        if($response['http_code'] != 200){
           return [
                'error' => true,
                'data' => $response['body']
            ];
        }

        $response = json_decode($response['body'], true);

        if($response['ok'] == false){
            return [
                'error' => true,
                'data' => $response['error']
            ];
        }

        return [
            'error' => false,
            'channel' => $response['channel']
        ];
    }
}