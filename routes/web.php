<?php

use App\Core\Services\UserService;
use App\Models\Opposition;
use App\Models\User;
use App\Notifications\Api\SendCredentialsUserNotification;
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

Route::get('/', static function () {
    //return view('welcome');

    $configUriFrontend = redirect(config('app.url_frontend'));

    return "Hola. Esta es la URL del Frontend: {$configUriFrontend}";
    //return redirect(config('app.url_frontend'));
});

