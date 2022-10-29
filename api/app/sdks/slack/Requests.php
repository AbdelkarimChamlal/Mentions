<?php

namespace App\sdks\slack;

class Requests
{
    public static function getUserInfoRequest($userId, $token)
    {
        $user_info_endpoint = "https://slack.com/api/users.info?user=".$userId;

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $user_info_endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token
            ),
        ));
    
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        curl_close($curl);

        return [
            'body' => $response,
            'http_code' => $http_code
        ];
    }

    public static function getPermalinkRequest($channel, $ts, $token)
    {
        $url = "https://slack.com/api/chat.getPermalink?channel=$channel&message_ts=$ts";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$token
            ),
        ));
    
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        curl_close($curl);

        return [
            'body' => $response,
            'http_code' => $http_code
        ];
    }
}