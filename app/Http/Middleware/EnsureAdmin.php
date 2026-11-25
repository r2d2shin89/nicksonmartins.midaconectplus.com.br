<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['error' => 'Não autorizado.'], Response::HTTP_UNAUTHORIZED);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
