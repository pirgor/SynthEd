<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\ProgressTracking;
use App\Models\Lesson;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $unreadNotifications = $user->unreadNotifications()->count();

        $userId = $user->id;
        $totalActivities = Lesson::count() + Quiz::count();

        $completedActivities = ProgressTracking::where('user_id', $userId)
            ->where('completed', true)
            ->count();

        $progressPercent = $totalActivities > 0
            ? round(($completedActivities / $totalActivities) * 100)
            : 0;

        // Fetch upcoming quizzes with deadlines
        $upcomingDeadlines = Quiz::whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->take(5) // show only next 5 deadlines
            ->get();

        return view('home', compact('unreadNotifications', 'progressPercent', 'upcomingDeadlines'));
    }
}
