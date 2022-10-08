<?php

use App\sdks\github\Github;
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
