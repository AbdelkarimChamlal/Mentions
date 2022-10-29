<?php

namespace App\services;

use App\Models\User;
use App\Models\Column;
use App\Models\Account;
use App\Models\Mention;
use App\Events\ResourceUpdateEvent;
use App\sdks\slack\Slack;
use Illuminate\Support\Facades\Log;

class SlackServices
{
    public static function handleWebHooks($request)
    {
        $event = $request->input('event');

        if($event['type'] != 'message') return;
        
        $text = $event['text'];

        $mentions = self::extractMentions($text);

        foreach($mentions as $mention){
            $mention = str_replace('@', '', $mention);
            $mention = str_replace('<', '', $mention);
            $mention = str_replace('>', '', $mention);
            
            $account = Account::where([
                'platform' => 'slack',
                'platform_id' => $mention
            ])->first();

            if(!$account) continue;

            $text = str_replace("<@$mention>", '@'.$account->username, $text);

            $new_mentions_column = Column::where([
                'user_id' => $account->user_id,
                'type' => 'new_mentions'
            ])->first();

            $max_order = Mention::where([
                'account_id' => $account->id,
                'platform' => 'github',
                'column_id' =>  $new_mentions_column->id
            ])->max('order') ?? 0;


            $sender = $event['user'];
            $access_token = json_decode($account->access_data)->access_token;
            
            $sender_info = Slack::getUserInfo($sender, $access_token);

            $db_mention = new Mention();
            $db_mention->user_id = $account->user_id;
            $db_mention->account_id = $account->id;
            $db_mention->platform = 'slack';
            $db_mention->platform_id = $mention;
            $db_mention->content = $text;
            $db_mention->type = 'message';

            $db_mention->url = $event['permalink'] ?? null;
            $db_mention->sender_name = $sender_info['user']['name'] ?? null;
            $db_mention->sender_username = $sender_info['user']['real_name'] ?? null;
            $db_mention->sender_avatar = $sender_info['user']['image'] ?? null;
            $db_mention->sender_url = $sender_info['user']['profile_link'] ?? null;
            $db_mention->status = 'unread';
            
            $db_mention->column_id = $new_mentions_column->id;

            $db_mention->order = $max_order + 1;
            $db_mention->save();

            ResourceUpdateEvent::dispatch(User::find($account->user_id), 'mentions', 'added', $db_mention->id);
        }
    }















    private static function extractMentions($body)
    {
        $mentions = [];
        $pattern = '/<@([a-zA-Z0-9_]+)>/';
        preg_match_all($pattern, $body, $matches);
        if($matches){
            $mentions = $matches[1];
        }
        return $mentions;
    }
}