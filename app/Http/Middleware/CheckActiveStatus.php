<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_active != 1) {
            Auth::logout();  // Logout the user
            return redirect('/login')->with('error', 'Your account is inactive.');
        }

        return $next($request);
    }
}
