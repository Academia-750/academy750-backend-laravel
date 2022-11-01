<?php

Route::prefix('v1')->group(static function(){
    require __DIR__ . '/routes/json-api-auth.php';

    Route::middleware(['auth:sanctum'])->group(static function () {
        require __DIR__ . '/routes/profile.php';
        require __DIR__ . '/routes/students.routes.php';
    // [EndOfLineMethodRegister]
    });
});
