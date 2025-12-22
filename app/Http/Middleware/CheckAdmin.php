<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if ($user->role != 'admin' && $user->role != 'super_admin') {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized. Please log in.',
            ], 401);
        }

        return $next($request);
    }
}
