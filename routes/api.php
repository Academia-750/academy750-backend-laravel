<?php

/*use App\Actions\JsonApiAuth\AuthKit;*/
use App\Http\Controllers\MyProfileAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/json-api-auth.php';

Route::get('/auth/my-profile', MyProfileAuthController::class)->middleware('auth:sanctum')->name('my-profile-auth');
