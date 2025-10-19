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
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Admin\UserManagementController;
// -------------------- ROOT ROUTE --------------------
// Redirects to login page when accessing the root URL
Route::get('/', [HomeController::class, 'index'])->name('home');

// -------------------- AUTHENTICATION ROUTES --------------------
// Provides routes for login, registration, password reset, etc.
Auth::routes();

// -------------------- STUDENT ROUTES --------------------
// All routes in this group require authentication and student role
Route::middleware(['auth', 'role:student'])
    ->prefix('student') // Adds /student prefix to all routes in this group
    ->name('student.') // Adds 'student.' prefix to all route names in this group
    ->group(function () {
        // Student quiz listing page
        Route::get('quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');

        // Page for taking a specific quiz
        Route::get('quizzes/{quiz}/take', [StudentQuizController::class, 'take'])->name('quizzes.take');

        // Submit quiz answers
        Route::post('quizzes/{quiz}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');

        // View results of a specific quiz attempt
        Route::get('quizzes/{quiz}/results/{attempt}', [StudentQuizController::class, 'results'])->name('quizzes.results');

        // View history of attempts for a specific quiz
        Route::get('quizzes/{quiz}/attempts', [StudentQuizController::class, 'attempts'])->name('quizzes.attempts');

        // View all grades for the student
        Route::get('grades', [StudentQuizController::class, 'grades'])->name('grades');

        Route::get('course', [LessonController::class, 'index'])->name('stud.lessons');

        Route::get('lessons/{lesson}/practice', [QuizController::class, 'showPrac'])
            ->name('lessons.practice');
        Route::post('lessons/{lesson}/practice', [QuizController::class, 'generatePrac'])
            ->name('lessons.practice.generate');
        Route::post('/lessons/{lesson}/mark-read', [LessonController::class, 'markRead'])
            ->name('student.lessons.markRead');
        Route::post('/summary/generate', [QuizController::class, 'generateSummary'])->name('summary.generate');
    });

// -------------------- INSTRUCTOR ROUTES --------------------
// All routes in this group require authentication and instructor role
Route::middleware(['auth', 'role:instructor'])
    ->prefix('instructor') // Adds /instructor prefix to all routes in this group
    ->name('instructor.') // Adds 'instructor.' prefix to all route names in this group
    ->group(function () {
        // Instructor dashboard page
        Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');

        // Announcements
        Route::resource('announcements', AnnouncementController::class);

        // Content management routes (CRUD for uploaded content)
        Route::resource('content', ContentController::class);
        // Download uploaded content file
        Route::get('content/{id}/download', [ContentController::class, 'download'])->name('content.download');

        // Quiz management routes (CRUD for quizzes)
        Route::resource('quizzes', QuizController::class);
        // Lessons
        Route::resource('lessons', LessonController::class);
        Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
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
Route::get('uploads/{upload}/view', [LessonController::class, 'viewUpload'])
    ->name('uploads.view');

// Notifications
Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

Route::get('quizzes/{quiz}/results/{attempt}', [StudentQuizController::class, 'results'])->name('quizzes.results');




Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle', [UserManagementController::class, 'toggleStatus'])->name('users.toggle');
});
