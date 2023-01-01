<?php

/*
 * En caso de que la Aplicación se encuentre en producción y ya esté siendo usada por Usuarios reales
 * Este archivo servirá para hacer modificaciones que no afectarán directamente al funcionamiento del sistema FrontEnd
 * Servirá para hacer pruebas antes de sacar las nuevas funcionalidades al mercado o al sistema real.
 *
 * */
Route::prefix('v2')->middleware(['auth:sanctum','verify.status_account'])->group(static function(){

    // [EndOfLineMethodRegister]
});
