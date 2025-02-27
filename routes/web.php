<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YandexMusicParserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Define a route for parsing artist data
Route::get('/parse-artist/{artistId}', [YandexMusicParserController::class, 'parseArtist']);