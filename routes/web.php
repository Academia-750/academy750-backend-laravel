<?php

use App\Models\Opposition;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', static function () {
    //return view('welcome');
    $opposition = Opposition::first();

    $subtopics = $opposition->subtopics->filter(static function ($subtopic) use ($opposition) {
        return $subtopic->oppositions()->contains($opposition->getRouteKey());
    });

    $subtopics_id = $subtopics->map(static function ($item) {
        return $item->getRouteKey();
    });

    return Opposition::with([
        'topics' => [
            'subtopics'/* => static function ($query) use ($opposition) {
                $query->with('oppositions')->where('oppositions.id', '=', $opposition->getRouteKey());
            }*/
        ]
    ])->first();
    /*return Opposition::query()->with([
        'topics' => static function ($item) {

        }
    ])->get();*/
});
