<?php

use App\Http\Controllers\Api\v1\StudentLessonsController;
use App\Http\Controllers\Api\v1\UsersController;
use App\Models\ManageUsersInformation;


/**
 * Download or redirect to a URL.
 * The URL is not exposed to the user and is validate Via Cookies and user tokens
 */
Route::get('resource/{code}', [StudentLessonsController::class, 'downloadFile']);


/**
 * @group Guest
 * Contact Us
 */
Route::post('guest/user/contact-us', [UsersController::class, 'contactsUS'])->name('api.v1.users.home-page.form.contact-us');
/**
 * @group Guest
 * Hello World
 */
Route::get('guest/user/hello', function () {
    return response()->json([
        'message' => 'Welcome'
    ]);
});

/**
 * @group Guest
 * Accept Cookies
 */
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

/**
 * @group Guest
 * Has Accept Cookies
 */
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
