<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bomberos750:clear', function () {
    $this->call('cache:clear');
    $this->call('cache:clear');
    $this->call('config:clear');
    $this->call('event:clear');
    $this->call('route:clear');
    $this->call('view:clear');
    //$this->call('queue:clear');
    $this->call('optimize:clear');


    $this->call('config:cache');
    $this->call('view:cache');
    $this->call('route:cache');
    $this->call('optimize');
})->purpose('Optimiza la caché de la APP');

Artisan::command('bomberos750:install', function () {
    $this->call('key:generate');
    $this->call('storage:link');
    $this->call('bomberos750:migrate');
})->purpose('Hace las configuraciones necesarias cuando se descarga el proyecto');

Artisan::command('bomberos750:test', function () {
    $this->call('bomberos750:clear');
    $this->call('cache:clear');
    $this->call('cache:clear');
    $this->call('test', ['--stop-on-failure' => true]);
    //Artisan::call('test', ['--stop-on-failure']);
})->purpose('Ejecuta los tests del proyecto de forma eficiente');

Artisan::command('bomberos750:migrate', function () {
    $this->call('bomberos750:clear');
    $this->call('migrate:fresh', ['--force' => true]);
    $this->call('db:seed', ['--force' => true]);
    $this->call('bomberos750:clear');
})->purpose('Borra tablas, realiza la migracion, ejecuta los seeders y optimiza la cache de la app');

Artisan::command('bomberos750:no-seeder:migrate', function () {
    $this->call('bomberos750:clear');
    $this->call('migrate:fresh');
    //$this->call('db:seed');
    $this->call('bomberos750:clear');
})->purpose('Borra tablas, realiza la migracion, ejecuta los seeders y optimiza la cache de la app');

Artisan::command('bm', function () {
    $this->call('bomberos750:migrate');
})->purpose('Borra tablas, realiza la migracion, ejecuta los seeders y optimiza la cache de la app');

Artisan::command('bc', function () {
    $this->call('bomberos750:clear');
})->purpose('Optimiza la caché de la APP');

Artisan::command('check:connection', function () {
    $this->call('bomberos750:clear');
    if (DB::select('SELECT 1')) {
        $this->info('Conexión exitosa');
        //$this->info(DB::getSchemaBuilder()->getConnection());
    } else {
        $this->error('No hay conexión segura a la base de datos.');
    }
})->purpose('Comprobar conexion a base de datos');
