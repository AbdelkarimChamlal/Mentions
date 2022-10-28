<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;



Route::get('/login', function(){
    $url = "https://slack.com/oauth/v2/authorize?client_id=".config('services.slack.client_id')."&scope=channels:history,groups:history,im:history,mpim:history&redirect_uri=".urlencode(config('services.slack.redirect_uri'))."&response_type=code&state=hello";
    return Redirect::to($url);
});



Route::get('/callback', function(Request $request){
    return $request;
});