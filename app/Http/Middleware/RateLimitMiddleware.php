<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $request->ip() . '|' . $request->path();

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
} 