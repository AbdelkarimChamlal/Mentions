<?php

namespace App\sdks\github;

class Requests
{


    public function exchange_code_for_token_request($client_id, $client_secret, $code, $redirect_uri)
    {
        $url = "https://github.com/login/oauth/access_token";
        $data = [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $code,
            'redirect_uri' => $redirect_uri
        ];

        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        return $this->curl_prepare_response($curl);
    }


    /**
     * formats the curl reponse in a uniform way so that all the responses are in the same format
     * 
     * @param $curl
     * @return array
     */
    private function curl_prepare_response($curl)
    {
        $body = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $http_response_header = curl_getinfo($curl, CURLINFO_HEADER_OUT);
        curl_close($curl);
        return [
            'body' => $body,
            'err' => $err,
            'http_code' => $http_code,
            'headers' => $http_response_header
        ];
    }
}
