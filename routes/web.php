<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SpeechController;
use App\Http\Controllers\TtsSettingsController;
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

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/speech-test', [SpeechController::class, 'index'])->name('speech.test');
    Route::post('/speech-generate', [SpeechController::class, 'generate'])->name('speech.generate');

    Route::get('/settings/tts', [TtsSettingsController::class, 'edit'])->name('tts.settings.edit');
    Route::put('/settings/tts', [TtsSettingsController::class, 'update'])->name('tts.settings.update');
});



Route::post('/send', [ChatController::class, 'send']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
