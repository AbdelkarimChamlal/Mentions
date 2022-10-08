<?php

namespace App\sdks\github;

use App\sdks\github\Requests;


class Github
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $scope;
    private $requests;

    public function __construct(String $client_id = null, String $client_secret = null, String $redirect_uri = null, array $scopes = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        $this->scope = $scopes;
        $this->requests = new Requests();
    }

    public function getAuthUrl()
    {
        $url = "https://github.com/login/oauth/authorize?client_id=" . $this->client_id . "&redirect_uri=" . $this->redirect_uri . "&scope=" . implode(" ", $this->scope);
        return $url;
    }


    public function exchange_code_for_token($code)
    {
        $response = $this->requests->exchange_code_for_token_request($this->client_id, $this->client_secret, $code, $this->redirect_uri);
        if($response['http_code'] >= 400){
            return [
                'error' => true,
                'data' => $this->format_error($response['body'])
            ];
        }

        $body = $response['body'];
        $body_items = explode("&", $body);


        $access_data = [];
        foreach($body_items as $data){
            $data = explode("=", $data);
            $access_data[$data[0]] = $data[1];
        }

        return [
            'error' => false,
            'data' => $access_data
        ];
    }


    public function refresh_access_token($refresh_token)
    {
        $response = $this->requests->refresh_access_token_request($this->client_id, $this->client_secret, $refresh_token);
        if($response['http_code'] >= 400){
            return [
                'error' => true,
                'data' => $this->format_error($response['body'])
            ];
        }

        $body = json_decode($response['body'], true);
        
        return [
            'error' => false,
            'data' => [
                "access_token" => $body['access_token'],
                "token_type" => $body['token_type'],
                "expires_in" => $body['expires_in'],
                "scope" => $body['scope'],
                "refresh_token" => $body['refresh_token'],
                "refresh_token_expires_in" => $body['refresh_token_expires_in']
            ]
        ];
    }

    public function get_user($access_token)
    {
        $response = $this->requests->get_user_request($access_token);
        dd($response);
        if($response['http_code'] >= 400){
            return [
                'error' => true,
                'data' => $this->format_error($response['body'])
            ];
        }

        $body = json_decode($response['body'], true);
        
        return [
            'error' => false,
            'data' => [
                "username" => $body['login'],
                "id" => $body['id'],
                "avatar" => $body['avatar_url'],
                "link" => $body['html_url'],
                "type" => $body['type'],
                "name" => $body['name'],
                "email" => $body['email'],
            ]
        ];
    }



    /**
     * Used to format all github api responses in a uniform way
     * 
     * @param String $response_body the http request's response body
     * @return array
     */
    private function format_error(String $response_body)
    {
        $error_body = json_decode($response_body, true);
        $message = $error_body['message'] ?? 'Unknown error';
        $errors = $error_body['errors'] ?? [];
        return [
            'message' => $message,
            'errors' => $errors
        ];
    }
}
