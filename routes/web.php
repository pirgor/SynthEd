<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpeechController;
use App\Http\Controllers\TtsSettingsController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentQuizController;
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
    return view('auth.login');
});

Auth::routes();

// -------------------- STUDENT ROUTES --------------------
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        // List all quizzes for student
        Route::get('quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');

        // Take a quiz
        Route::get('quizzes/{quiz}/take', [StudentQuizController::class, 'take'])->name('quizzes.take');

        // Submit quiz answers
        Route::post('quizzes/{quiz}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');

        // View results of a specific attempt
        Route::get('quizzes/{quiz}/results/{attempt}', [StudentQuizController::class, 'results'])->name('quizzes.results');

        // View history of attempts
        Route::get('quizzes/{quiz}/attempts', [StudentQuizController::class, 'attempts'])->name('quizzes.attempts');

        // Grades
        Route::get('grades', [StudentQuizController::class, 'grades'])->name('grades');
    });

// -------------------- INSTRUCTOR ROUTES --------------------
Route::middleware(['auth', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {

        // Instructor Home (Dashboard)

        Route::get('/home', [App\Http\Controllers\Instructor\HomeController::class, 'index'])
            ->name('home');

        // CRUD for quizzes
        Route::resource('quizzes', QuizController::class);

        // Quiz-specific routes
        Route::prefix('quizzes/{quiz}')->as('quizzes.')->group(function () {
            // CRUD for quiz questions
            Route::resource('questions', QuestionController::class);

            // Generate AI questions
            Route::get('generate', [QuizController::class, 'showGenerateForm'])->name('generate');
            Route::post('generate', [QuizController::class, 'generateQuestions'])->name('generate.post');
        });
    });

Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('auth.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('auth.update');

Route::get('/speech-test', [SpeechController::class, 'index'])->name('speech.test');
Route::post('/speech-generate', [SpeechController::class, 'generate'])->name('speech.generate');
Route::get('/settings/tts', [TtsSettingsController::class, 'edit'])->name('tts.settings.edit');
Route::put('/settings/tts', [TtsSettingsController::class, 'update'])->name('tts.settings.update');

Route::post('/send', [ChatController::class, 'send']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
