<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $chatHistory = Message::where('user_id', Auth::id())
                    ->orderBy('created_at')
                    ->get();
            } else {
                $chatHistory = session()->get('chatHistory', []);
            }

            $view->with('chatHistory', $chatHistory);
        });
    }
}
