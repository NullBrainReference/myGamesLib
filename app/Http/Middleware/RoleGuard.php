<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class RoleGuard
{
    public function handle(Request $request, Closure $next, string $requiredRole)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();

        if (!UserRole::tryFrom($requiredRole)) {
            abort(500, "Unknown role: $requiredRole");
        }

        if ($user->role !== $requiredRole) {
            return redirect()->route('index')->with('error', 'You have no permission');
        }

        return $next($request);
    }
}
