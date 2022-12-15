<?php

use App\Http\Controllers\Api\v1\MyProfileAuthController;
use App\Http\Controllers\Api\v1\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/my-profile', [ProfileController::class, 'getDataMyProfile'])->middleware('auth:sanctum')->name('api.v1.my-profile-auth');
Route::get('/my-profile', [MyProfileAuthController::class, 'get_data_my_profile'])->middleware('auth:sanctum')->name('api.v1.my-profile-simple-auth');
Route::post('/auth/update-data-my-profile', [ProfileController::class, 'updateDataMyProfile'])->middleware('auth:sanctum')->name('api.v1.update-data-my-profile-auth');
Route::get('/auth/unsubscribe-from-system', [ProfileController::class, 'unsubscribeFromSystem'])->middleware('auth:sanctum')->name('api.v1.unsubscribe-from-system-auth');
Route::post('/auth/change-password-my-account', [ProfileController::class, 'changePasswordAuth'])->middleware('auth:sanctum')->name('api.v1.change-password-auth');
Route::post('/auth/my-account/update/image', [MyProfileAuthController::class, 'updateImageAccount'])->middleware('auth:sanctum')->name('api.v1.auth.my-account.update-image');

Route::get('/notifications/user/', [ProfileController::class, 'getNotificationsUser'])->middleware('auth:sanctum')->name('api.v1.notifications.user');
Route::post('/read/notification/{notification}/user/', [ProfileController::class, 'read_notification_user'])->middleware('auth:sanctum')->name('api.v1.read.notifications.user');
