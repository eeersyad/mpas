<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerananMiddleware
{
    public function handle(Request $request, Closure $next, string ...$peranan): Response
    {
        $user = $request->user();

        if (! $user || ! $user->mempunyaiPeranan($peranan)) {
            return response()->view('ralat.terhad', [], 403);
        }

        return $next($request);
    }
}