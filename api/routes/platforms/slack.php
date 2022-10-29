<?php

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;



Route::middleware('auth')->get('/login', function(){
    $url = "https://slack.com/oauth/v2/authorize?client_id=".config('services.slack.client_id')."&scope=&user_scope=channels:history,channels:read,groups:history,groups:read,identify,im:history,im:read,links:read,mpim:history,reactions:read,team:read,users.profile:read,users:read";
    return Redirect::to($url);
});


Route::middleware('auth')->get('/callback', function(Request $request){

    if($request->has('error') || !$request->has('code')){
        return redirect('/dashboard')->with('error', "Sorry, we couldn't log you in. Please try again.");
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://slack.com/api/oauth.v2.access',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'code='.$request->code.'&redirect_uri='.urlencode(config('services.slack.redirect_uri')).'&grant_type=authorization_code&client_id='.config('services.slack.client_id').'&client_secret='.config('services.slack.client_secret'),
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if($http_code != 200){
        return redirect('/dashboard')->with('error', "Sorry, we couldn't log you in. Please try again.");
    }
 
    $response = json_decode($response, true);

    if(isset($response['ok']) && $response['ok'] == false){
        return redirect('/dashboard')->with('error', "Sorry, we couldn't log you in. Please try again.");
    }

    $slack = $response['authed_user'];

    $user_info_endpoint = "https://slack.com/api/users.info?user=".$slack['id'];


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
            'Authorization: Bearer '.$slack['access_token']
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if($http_code != 200){
        return redirect('/dashboard')->with('error', "Sorry, we couldn't log you in. Please try again.");
    }

    $response = json_decode($response, true);

    if(isset($response['ok']) && $response['ok'] == false){
        return redirect('/dashboard')->with('error', "Sorry, we couldn't log you in. Please try again.");
    }

    $user = $request->user();
    $slack_user = $response['user'];

    $account = Account::where([
                'user_id' => $user->id,
                'platform' => 'slack',
                'platform_id' => $slack_user['id']
        ])->first();

    if(!$account){
        $account = new Account;
    }


    $account->user_id = $user->id;
    $account->platform = 'slack';
    $account->platform_id = $slack_user['id'];
    $account->username = $slack_user['name'];
    $account->name = $slack_user['real_name'];
    $account->type = 'user';
    $account->status = 'active';
    $account->avatar = $slack_user['profile']['image_512'] ?? $slack_user['profile']['image_original'] ?? null; // TODO lets have an asset with a default image
    $account->profile_link = "https://slack.com/app_redirect?channel=".$slack_user['id'];
    $account->access_data = json_encode($slack);
    $account->save();

    return redirect('/dashboard')->with('success', "You have successfully linked your slack Account.");
});