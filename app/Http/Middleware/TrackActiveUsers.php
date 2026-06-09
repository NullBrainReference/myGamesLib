<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Singletones\UserSessionTracker;

class TrackActiveUsers
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // 💡 Explicit GoF Singleton pattern execution
            $tracker = UserSessionTracker::getInstance();
            $tracker->track(Auth::id());

            // Also update the database timestamp to keep our count accurate
            Auth::user()->touch();
        }

        return $next($request);
    }
}
