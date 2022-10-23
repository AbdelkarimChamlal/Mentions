<?php

use App\sdks\github\Github;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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


Route::get('/slack/webhooks', function(Request $request){
    Log::info($request->all());
    return response()->json(['success' => true]);
});

Route::post('/slack/webhooks', function(Request $request){
    Log::info($request->all());
    return response()->json(['success' => true]);
});
