<?php

use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/my-profile', [ProfileController::class, 'getDataMyProfile'])->middleware('auth')->name('my-profile');
