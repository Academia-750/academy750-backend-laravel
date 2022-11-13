<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ImagesController;

// Rutas del recurso Images

Route::get('images', [ImagesController::class, 'index'])->name('api.v1.images.index');
Route::get('images/{image}', [ImagesController::class, 'read'])->name('api.v1.images.read');
Route::post('images/create', [ImagesController::class, 'create'])->name('api.v1.images.create');
Route::patch('images/update/{image}', [ImagesController::class, 'update'])->name('api.v1.images.update');
Route::delete('images/delete/{image}', [ImagesController::class, 'delete'])->name('api.v1.images.soft-delete');
Route::post('images/actions-on-multiple-records', [ImagesController::class, 'action_for_multiple_records'])->name('api.v1.images.actions-on-multiple-records');
/*
Route::post('images/export', [ImagesController::class, 'export_records'])->name('api.v1.images.export');
Route::post('images/import', [ImagesController::class, 'import_records'])->name('api.v1.images.import');
Route::get('images/import/template', [ImagesController::class, 'download_template_import_records'])->name('api.v1.images.import.template');
*/
