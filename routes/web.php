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

    /*$instanceEloquentCollection = new \Illuminate\Database\Eloquent\Collection;

    $instanceEloquentCollection->add(
        new User(
            User::query()->find('106dd718-6141-4aa0-9cff-419d39a83517')?->toArray()
        )
    );

    $instanceEloquentCollection->add(
        new User(
            User::query()->find('106dd718-6141-4aa0-9cff-419d39a83517')?->toArray()
        )
    );

    $instanceEloquentCollection->add(
        new User(
            User::query()->find('12244763-0937-4709-916b-d85c344e198a')?->toArray()
        )
    );*/

    //return $instanceEloquentCollection;

    //dd(User::query()->limit(6)->paginate(2));
});

Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

