<?php

use App\Http\Controllers\Api\v1\UsersController;
use App\Models\ManageUsersInformation;

Route::prefix('v1')->group(callback: static function () {
    require __DIR__ . '/routes/json-api-auth.php';

    Route::post('guest/user/contact-us', [UsersController::class, 'contactsUS'])->name('api.v1.users.home-page.form.contact-us');
    Route::get('guest/user/hello', function () {
        return response()->json([
            'message' => 'Welcome'
        ]);
    });

    Route::post('guest/user/accept-cookies', function (\Illuminate\Http\Request $request) {
        $userIp = ManageUsersInformation::query()
            ->where('ip', $request->ip() ?? $request->getClientIp())
            ->where('user_agent', $request->header('User-Agent'))
            ->first();

        if (!$userIp) {
            ManageUsersInformation::query()->create([
                'has_accept_cookies' => true,
                'user_agent' => $request->header('User-Agent'),
                'ip' => $request->ip() ?? $request->getClientIp()
            ]);
        } else {
            $userIp->update([
                'has_accept_cookies' => true,
                'user_agent' => $request->header('User-Agent'),
                'ip' => $request->ip() ?? $request->getClientIp()
            ]);
        }

        return response()->json([
            'has_accept_cookies' => true,
            'user_agent' => $request->header('User-Agent'),
            'ip' => $request->ip() ?? $request->getClientIp()
        ]);
    })->name('api.v1.users.home-page.form.accept-cookies');

    Route::post('guest/user/has-accept-cookies', function (\Illuminate\Http\Request $request) {
        $userIp = ManageUsersInformation::query()
            ->where('ip', $request->ip() ?? $request->getClientIp())
            ->where('user_agent', $request->header('User-Agent'))
            ->where('has_accept_cookies', true)
            ->first();

        if (!$userIp) {
            return response()->json([
                'has_accept_cookies' => false,
                'user_agent' => $request->header('User-Agent'),
                'ip' => $request->ip() ?? $request->getClientIp()
            ]);
        } else {
            return response()->json([
                'has_accept_cookies' => $userIp->has_accept_cookies,
                'user_agent' => $request->header('User-Agent'),
                'ip' => $request->ip() ?? $request->getClientIp()
            ]);
        }
    })->name('api.v1.users.home-page.form.has-accept-cookies');

    Route::middleware(['auth:sanctum', 'only_users_with_account_enable'])->group(static function () {
        require __DIR__ . '/routes/profile.php';
        require __DIR__ . '/routes/users.routes.php';
        require __DIR__ . '/routes/oppositions.routes.php';
        require __DIR__ . '/routes/topics.routes.php';
        require __DIR__ . '/routes/topics.subtopics.routes.php';
        require __DIR__ . '/routes/topics.subtopics.questions.routes.php';
        require __DIR__ . '/routes/topics.questions.routes.php';
        require __DIR__ . '/routes/topics.oppositions.routes.php';
        /*require __DIR__ . '/routes/subtopics.routes.php';*/
        require __DIR__ . '/routes/topic-groups.routes.php';
        require __DIR__ . '/routes/questions.routes.php';
        require __DIR__ . '/routes/tests.routes.php';
        //require __DIR__ . '/routes/answers.routes.php';
        //require __DIR__ . '/routes/images.routes.php';
        require __DIR__ . '/routes/import-processes.routes.php';
        require __DIR__ . '/routes/groups.routes.php';

        // [EndOfLineMethodRegister]
    });
});