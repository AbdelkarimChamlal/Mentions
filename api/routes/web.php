<?php

use App\sdks\github\Github;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('github')->group(base_path('routes/platforms/github.php'));
Route::prefix('slack')->group(base_path('routes/platforms/slack.php'));

Route::post('/slack/webhooks', [\App\Http\Controllers\web\WebhooksController::class, 'slackWebhooks']);
Route::post('/github/webhooks', [\App\Http\Controllers\web\WebhooksController::class, 'githubWebhooks']);

