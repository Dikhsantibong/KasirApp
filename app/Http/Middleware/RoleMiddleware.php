<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        // Owner can access everything.
        if (auth()->user()->role === 'Owner') {
            return $next($request);
        }

        // Check if the user's role is in the allowed roles array
        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak: Anda tidak memiliki izin ke halaman ini.');
    }
}
