<?php

Route::prefix('v1')->group(static function(){
    require __DIR__ . '/routes/json-api-auth.php';

    Route::middleware(['auth:sanctum', 'only_users_with_account_enable'])->group(static function () {
        require __DIR__ . '/routes/profile.php';
        require __DIR__ . '/routes/users.routes.php';
        require __DIR__ . '/routes/oppositions.routes.php';
        require __DIR__ . '/routes/topics.routes.php';
        require __DIR__ . '/routes/subtopics.routes.php';
        require __DIR__ . '/routes/topic-groups.routes.php';
        require __DIR__ . '/routes/questions.routes.php';
        require __DIR__ . '/routes/tests.routes.php';
        require __DIR__ . '/routes/answers.routes.php';
        require __DIR__ . '/routes/test-types.routes.php';
        require __DIR__ . '/routes/images.routes.php';
    // [EndOfLineMethodRegister]
    });
});
