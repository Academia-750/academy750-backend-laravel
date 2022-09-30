<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/json-api-auth.php';

require __DIR__ . '/api/v1/index.php';

Route::get('/check-status-server', function () {
    return response()->json([
        'status' => 'OK'
    ]);
});

/*
|--------------------------------------------------------------------------
| An example of how to use the verified email feature with api endpoints
|--------------------------------------------------------------------------
|
| Here examples of a route using Sanctum middleware and verified middleware.
| And another route using Passport middleware and verified middleware.
| You can install and use one of this official packages.
|
*/

//Route::get('/verified-middleware-example', function () {
//    return response()->json([
//        'message' => 'the email account is already confirmed now you are able to see this message...',
//    ]);
//})->middleware('auth:sanctum', 'verified');

//Route::get('/verified-middleware-example', function () {
//    return response()->json([
//        'message' => 'the email account is already confirmed now you are able to see this message...',
//    ]);
//})->middleware('auth:api', 'verified');
