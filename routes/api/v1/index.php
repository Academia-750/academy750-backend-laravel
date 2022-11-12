<?php

Route::prefix('v1')->group(static function(){
    require __DIR__ . '/routes/json-api-auth.php';

    Route::middleware(['auth:sanctum'])->group(static function () {
        require __DIR__ . '/routes/profile.php';
        require __DIR__ . '/routes/users.routes.php';
    require __DIR__ . '/routes/oppositions.routes.php';
    require __DIR__ . '/routes/topics.routes.php';
    require __DIR__ . '/routes/subtopics.routes.php';
    require __DIR__ . '/routes/topic-groups.routes.php';
    // [EndOfLineMethodRegister]
    });
});
