<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SpeechController;
use App\Http\Controllers\TtsSettingsController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
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

    Route::resource('quizzes', QuizController::class);

    Route::prefix('quizzes/{quiz}')->as('quizzes.')->group(function () {
        Route::resource('questions', QuestionController::class);
        Route::get('generate', [QuizController::class, 'showGenerateForm'])->name('quizzes.generate');
        Route::post('generate', [QuizController::class, 'generateQuestions'])->name('quizzes.generate.post');
    });
});

Route::post('/send', [ChatController::class, 'send']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// routes/web.php
Route::get('/check-env', function () {
    return [
        'env' => env('GEMINI_API_KEY', 'not found'),
    ];
});
