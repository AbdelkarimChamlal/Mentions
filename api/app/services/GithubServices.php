<?php

namespace App\services;

use App\Models\Account;

class GithubServices
{
    private static $github;

    public function __construct(\App\sdks\github\Github $github)
    {
        $this->github = $github;
    }

    public static function updateOrCreateAccount($user, $account_details, $access_data)
    {
        $account = Account::where([
            'user_id' => $user->id,
            'platform' => 'github',
            'platform_id' => $account_details['id']
        ])->first();

        if(!$account){
            $account = new Account();
        }

        $account->user_id = $user->id;
        $account->platform = 'github';
        $account->platform_id = $account_details['id'];
        $account->name = $account_details['name'];
        $account->type = $account_details['type'];
        $account->email = $account_details['email'];
        $account->username = $account_details['username'];
        $account->avatar = $account_details['avatar'];
        $account->profile_link = $account_details['link'];
        $account->access_data = json_encode($access_data);
        $account->access_data_last_updated = time();
        $account->status = "active";
        $account->save();

        return $account;
    }

    public static function refresh_access_data($account)
    {
        $access_data = json_decode($account->access_data, true);
        $refresh_token = $access_data['refresh_token'];
        $response = self::$github->refresh_access_token($refresh_token);

        if($response['error']){
            $account->status = "inactive";
            $account->save();
            return false;
        }

        $access_data = $response['data'];
        $account->access_data = json_encode($access_data);
        $account->access_data_last_updated = time();
        $account->status = "active";
        $account->save();

        return $account;
    }   

    public function handle_webhook($request)
    {
        $payload = $request->getContent();
        $payload = json_decode($payload, true);
        $event = $request->header('X-GitHub-Event');

        switch ($event) {
            case 'issues':
                $this->handle_issue($payload);
                break;
            case 'issue_comment':
                $this->handle_issue_comment($payload);
                break;
            case 'pull_request':
                $this->handle_pull_request($payload);
                break;
            case 'pull_request_review':
                $this->handle_pull_request_review($payload);
                break;
            case 'pull_request_review_comment':
                $this->handle_pull_request_review_comment($payload);
                break;
            case 'push':
                $this->handle_push($payload);
                break;
            case 'commit_comment':
                $this->handle_commit_comment($payload);
                break;
            case 'discussion':
                $this->handle_discussion($payload);
                break;
            default:
                break;
        }
        
    }


    private function handle_issue($payload)
    {

    }

    private function handle_issue_comment($payload)
    {

    }

    private function handle_pull_request($payload)
    {

    }

    private function handle_pull_request_review($payload)
    {

    }

    private function handle_pull_request_review_comment($payload)
    {

    }

    private function handle_push($payload)
    {

    }

    private function handle_commit_comment($payload)
    {

    }

    private function handle_discussion($payload)
    {

    }




}

