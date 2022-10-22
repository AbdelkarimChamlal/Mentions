<?php

use App\sdks\github\Github;
use Illuminate\Http\Client\Request;
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


Route::get('/api/log', [App\Http\Controllers\WebhookController::class, 'log']);
Route::post('/api/log', function(Request $request, Github $github){
    $github_service = new \App\services\GithubServices($github);
    $github_service->handle_webhook($request);
    return response()->json(['success' => true]);
});


Route::middleware('auth')->get('/api/mentions', [App\Http\Controllers\api\MentionsController::class, 'index']);
Route::middleware('auth')->put('/api/mentions/{id}', [App\Http\Controllers\api\MentionsController::class, 'update']);
Route::middleware('auth')->delete('/api/mentions/{id}', [App\Http\Controllers\api\MentionsController::class, 'delete']);

Route::middleware('auth')->get('/api/accounts', [App\Http\Controllers\api\AccountsController::class, 'index']);
Route::middleware('auth')->delete('/api/accounts/{id}', [App\Http\Controllers\api\AccountsController::class, 'delete']);