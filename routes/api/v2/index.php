<?php

Route::prefix('v2')->middleware(['auth:sanctum','verify.status_account'])->group(static function(){

    // [EndOfLineMethodRegister]
});
