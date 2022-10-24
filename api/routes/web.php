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



Route::get('/redirect', function(Github $github){
    $url = $github->getAuthUrl();
    return redirect($url);
});

Route::get('/github/callback', function(Github $github){
    $code = $_GET['code'];
    $response = $github->exchange_code_for_token($code);
    return $response;
});

Route::prefix('github')->group(base_path('routes/platforms/github.php'));
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::post('/slack/webhooks', function(Request $request){
    Log::info($request->all());
    $challenge = $request->input('challenge');
    if($challenge){
        // response in plain text
        return response($challenge, 200)->header('Content-Type', 'text/plain');
    }
    // challenge is not present, so this is a normal event
    return response()->json(['success' => true]);
});


Route::get('/slack/login', function(){
    $login_url = "https://slack.com/openid/connect/authorize?openid/connect/authorize?response_type=code&scope=openid%20profile%20email&client_id=".config('services.slack.client_id')."&redirect_uri=".urlencode(config('services.slack.redirect_uri'));

    return Redirect::to($login_url);
});


Route::get('/slack/callback', function(Request $request){
    return $request;
});