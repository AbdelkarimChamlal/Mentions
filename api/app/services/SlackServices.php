<?php

namespace App\services;

use App\Models\User;
use App\Models\Column;
use App\Models\Account;
use App\Models\Mention;
use App\Events\ResourceUpdateEvent;
use Illuminate\Support\Facades\Log;

class SlackServices
{
    public static function handleWebHooks($request)
    {
        Log::info('Slack Webhook Received');
        Log::info($request->all());
        $events = $request->input('event');
        if($events){
            foreach($events as $event){
                if(isset($event['type']) && $event['type'] != 'message') continue;
                
                $text = $event['text'] ?? null;

                $mentions = self::extract_mentions($text);

                Log::info(json_encode($event));

                Log::info($mentions);

                Log::info($text);

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

    
                    $db_mention = new Mention();
                    $db_mention->user_id = $account->user_id;
                    $db_mention->account_id = $account->id;
                    $db_mention->platform = 'slack';
                    $db_mention->platform_id = $mention;
                    $db_mention->content = $text;
                    $db_mention->type = 'message';

                    $db_mention->url = $event['permalink'] ?? null;
                    $db_mention->sender_name = $event['user'] ?? null;
                    $db_mention->sender_username = $event['user'] ?? null;
                    $db_mention->sender_avatar = $event['user'] ?? null;
                    $db_mention->sender_url = $event['user'] ?? null;
                    $db_mention->status = 'unread';
                    
                    $db_mention->column_id = $new_mentions_column->id;
    
                    $db_mention->order = $max_order + 1;
                    $db_mention->save();

                    ResourceUpdateEvent::dispatch(User::find($account->user_id), 'mentions', 'added', $db_mention->id);
                }
            }
        }
    }















    private static function extract_mentions($body)
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