<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Login staff के route पर redirect
        }

        if (Auth::user()->user_type !== 'staff') {
            abort(403, 'Unauthorized access. Staff privileges required.');
        }

        return $next($request);
    }
}
