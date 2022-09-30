<?php
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum','verify.status_account'])->group(function(){
    require __DIR__ . "/routes/profile.php";
});
