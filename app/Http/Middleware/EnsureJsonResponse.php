<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureJsonResponse extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if ($this->shouldAuthenticate($request, $guards)) {
            return $this->unauthenticated($request, $guards);
        }

        return $next($request);
    }

    /**
     * Determine if the user is authenticated for any of the given guards.
     */
    protected function shouldAuthenticate($request, array $guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Unauthenticated'
        ], 401));
    }
}
