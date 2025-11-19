<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;

class SopirMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'sopir') {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
