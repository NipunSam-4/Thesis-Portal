<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user() ?? Auth::guard('admin')->user();

        if ($user && isset($user->is_active) && !$user->is_active) {
            if (Auth::guard('admin')->check()) {
                Auth::guard('admin')->logout();
            } else {
                Auth::guard('web')->logout();
            }

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account has been deactivated.'], 403);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact the Academic Office.',
            ]);
        }

        return $next($request);
    }
}
