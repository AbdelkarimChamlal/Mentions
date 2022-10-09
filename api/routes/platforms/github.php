<?php

use App\Models\Account;
use App\sdks\github\Github;
use Illuminate\Http\Request;
use App\services\GithubServices;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->get('/login', function(Github $github)
{
    $url = $github->getAuthUrl();
    return redirect($url);
});

Route::middleware('auth')->get('/callback', function(Github $github, Request $request)
{
    $code = $_GET['code'];
    $user = $request->user();
    $response = $github->exchange_code_for_token($code);

    if($response['error']){
        return redirect('/home')->with('error', "Sorry, we couldn't log you in. Please try again.");
    }

    $access_data = $response['data'];
    $access_token = $response['data']['access_token'];
    $account_details_response = $github->get_user($access_token);

    if($account_details_response['error']){
        return redirect('/dashboard')->with('error', "Sorry, we couldn't log you in. Please try again.");
    }

    $account_details = $account_details_response['data'];
    $account = GithubServices::updateOrCreateAccount($user, $account_details, $access_data);

    return redirect('/dashboard')->with('success', "You have successfully linked your github Account.");
});

Route::get('/sandbox', function(Github $github, Request $request)
{
    $account = Account::find(1);
    $account = GithubServices::refresh_access_data($account);
    return $account;
});
