<?php

use App\Http\Controllers\Api\v1\MyProfileAuthController;
use App\Http\Controllers\Api\v1\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/my-profile', [ProfileController::class, 'getDataMyProfile'])->middleware('auth:sanctum')->name('my-profile');
Route::get('/auth/my-profile', MyProfileAuthController::class)->middleware('auth:sanctum')->name('my-profile-auth');
Route::post('/auth/update-data-my-profile', [ProfileController::class, 'updateDataMyProfile'])->middleware('auth:sanctum')->name('update-data-my-profile-auth');
