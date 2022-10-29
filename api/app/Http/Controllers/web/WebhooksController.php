<?php

namespace App\Http\Controllers\web;

use App\sdks\github\Github;
use Illuminate\Http\Request;
use App\services\GithubServices;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\services\SlackServices;

class WebhooksController extends Controller
{
    public function githubWebhooks(Request $request,Github $github)
    {
        $github_service = new GithubServices($github);
        $github_service->handle_webhook($request);
        return response()->json(['success' => true]);
    }


    public function slackWebhooks(Request $request)
    {
        SlackServices::handleWebHooks($request);
        $challenge = $request->input('challenge');
        if($challenge){
            return response($challenge, 200)->header('content-type', 'text/plain');
        }
        return response()->json(['success' => true]);
    }
}
