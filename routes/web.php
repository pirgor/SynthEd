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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\LessonController;

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

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // TTS Settings Routes
    Route::get('/settings/tts', [TtsSettingsController::class, 'edit'])->name('tts.settings.edit');
    Route::put('/settings/tts', [TtsSettingsController::class, 'update'])->name('tts.settings.update');

    // Speech Test Routes
    Route::get('/speech-test', [SpeechController::class, 'index'])->name('speech.test');
    Route::post('/speech-generate', [SpeechController::class, 'generate'])->name('speech.generate');

    // Chat Routes
    Route::post('/send', [ChatController::class, 'send']);

    // Home Route
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

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

        // Student course page
        Route::get('course', [LessonController::class, 'index'])->name('stud.lessons');

        // Practice quizzes for lessons
        Route::get('lessons/{lesson}/practice', [QuizController::class, 'showPrac'])
            ->name('lessons.practice');
        Route::post('lessons/{lesson}/practice', [QuizController::class, 'generatePrac'])
            ->name('lessons.practice.generate');
        Route::post('lessons/{lesson}/practice/generate', [QuizController::class, 'generatePrac'])
            ->name('lessons.practice.generate');
    });

// -------------------- INSTRUCTOR ROUTES --------------------
Route::middleware(['auth', 'role:instructor'])
    ->prefix('instructor')
    ->name('instructor.')
    ->group(function () {


        // Instructor dashboard page
        Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
        // Content management routes (CRUD for uploaded content)
        Route::resource('content', ContentController::class);
        // Download uploaded content file
        Route::get('content/{id}/download', [ContentController::class, 'download'])->name('content.download');

        // Quiz management routes (CRUD for quizzes)
        Route::resource('quizzes', QuizController::class);

        // Lesson management routes (CRUD for lessons)
        Route::resource('lessons', LessonController::class);

        // Quiz-specific routes (nested under quizzes)
        Route::prefix('quizzes/{quiz}')->as('quizzes.')->group(function () {
            // Question management routes (CRUD for quiz questions)
            Route::resource('questions', QuestionController::class);

            // Show AI quiz generation form
            Route::get('generate', [QuizController::class, 'showGenerateForm'])->name('generate');

            // Process AI quiz generation
            Route::post('generate', [QuizController::class, 'generateQuestions'])->name('generate.post');
        });

        // Assessment management routes
        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/{quiz}', [AssessmentController::class, 'show'])->name('assessments.show');
        Route::get('/assessments/{quiz}/results', [AssessmentController::class, 'results'])->name('assessments.results');

        // Analytics dashboard route
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        // Student progress tracking routes
        Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
        Route::get('/progress/{user}', [ProgressController::class, 'show'])->name('progress.show');
        Route::get('/progress/quiz/{quiz}', [ProgressController::class, 'quizReport'])->name('progress.quiz');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });

// -------------------- PROFILE ROUTES --------------------
// Profile editing and updating routes (available to all authenticated users)
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('auth.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('auth.update');

// -------------------- TTS (TEXT-TO-SPEECH) ROUTES --------------------
// Routes for text-to-speech functionality
Route::get('/speech-test', [SpeechController::class, 'index'])->name('speech.test');
Route::post('/speech-generate', [SpeechController::class, 'generate'])->name('speech.generate');

// -------------------- TTS SETTINGS ROUTES --------------------
// Routes for managing TTS settings
Route::get('/settings/tts', [TtsSettingsController::class, 'edit'])->name('tts.settings.edit');
Route::put('/settings/tts', [TtsSettingsController::class, 'update'])->name('tts.settings.update');

// -------------------- CHATBOT ROUTES --------------------
// Route for sending messages to the chatbot
Route::post('/send', [ChatController::class, 'send']);

// -------------------- HOME ROUTE --------------------
// Default home route after login
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Lesson upload view route
Route::get('uploads/{upload}/view', [LessonController::class, 'viewUpload'])
    ->name('uploads.view');
