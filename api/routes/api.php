<?php

use App\sdks\github\Github;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/log', [App\Http\Controllers\WebhookController::class, 'log']);
Route::post('/log', function(Request $request, Github $github){
    $github_service = new \App\services\GithubServices($github);
    $github_service->handle_webhook($request);
    return response()->json(['success' => true]);
});


Route::middleware('auth.basic')->get('/mentions', [App\Http\Controllers\api\MentionsController::class, 'index']);
Route::middleware('auth.basic')->put('/mentions/{id}', [App\Http\Controllers\api\MentionsController::class, 'update']);
Route::middleware('auth.basic')->delete('/mentions/{id}', [App\Http\Controllers\api\MentionsController::class, 'delete']);

Route::middleware('auth.basic')->get('/accounts', [App\Http\Controllers\api\AccountsController::class, 'index']);
Route::middleware('auth.basic')->delete('/accounts/{id}', [App\Http\Controllers\api\AccountsController::class, 'delete']);