<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;

class PenumpangMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'penumpang') {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
