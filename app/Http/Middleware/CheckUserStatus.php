<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->status === 'disabled') {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been disabled. Please contact the administrator.',
            ]);
        }

        return $next($request);
    }
}
