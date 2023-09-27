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

Route::get('/', function () {
    return redirect(config('app.url_frontend'));
});


Route::get('/debug-sentry', function () {
    // Real scenario of how we handle issues
    try {
        throw new Exception('My Laravel Sentry error!');
    } catch (\Exception $e) {
        // Dont use ABORT, use the LOG and the response object to handle request
        // Phase 1 APIS use abort and will be replace
        abort(500, $e);


    }
});