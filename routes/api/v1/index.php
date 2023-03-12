<?php

use App\Http\Controllers\Api\v1\UsersController;

Route::prefix('v1')->group(static function(){
    require __DIR__ . '/routes/json-api-auth.php';
    Route::post('/test/errors-validation/manually', static function (\Illuminate\Http\Request $request) {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|max:10',
            'age' => 'required|integer|max:100',
        ]);

        $errors = [];

        if ($validator->fails()) {
            $errors[] = $validator->errors();
        }

        return response()->json([
            'request' => $request->all(),
            'errors' => $errors,
            'fails' => $validator->fails()
        ]);
    });

    Route::post('guest/user/contact-us', [UsersController::class, 'contactsUS'])->name('api.v1.users.home-page.form.contact-us');
    Route::get('guest/user/hello', function () {
        return response()->json([
            'message' => 'Welcome'
        ]);
    });

    Route::middleware(['auth:sanctum', 'only_users_with_account_enable'])->group(static function () {
        require __DIR__ . '/routes/profile.php';
        require __DIR__ . '/routes/users.routes.php';
        require __DIR__ . '/routes/oppositions.routes.php';
        require __DIR__ . '/routes/topics.routes.php';
        /*require __DIR__ . '/routes/subtopics.routes.php';*/
        require __DIR__ . '/routes/topic-groups.routes.php';
        require __DIR__ . '/routes/questions.routes.php';
        require __DIR__ . '/routes/tests.routes.php';
        //require __DIR__ . '/routes/answers.routes.php';
        //require __DIR__ . '/routes/images.routes.php';
        require __DIR__ . '/routes/import-processes.routes.php';
    // [EndOfLineMethodRegister]
    });
});
