<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Login doctor के route पर redirect
        }

        if (Auth::user()->user_type !== 'doctor') {
            abort(403, 'Unauthorized access. Doctor privileges required.');
        }

        return $next($request);
    }
}
