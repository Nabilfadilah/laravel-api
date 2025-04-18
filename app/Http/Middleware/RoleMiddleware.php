<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // if ($request->user()->role !== $role) {
        //     return response()->json([
        //         'message' => 'Unauthorized - only ' . $role . ' can access this route.'
        //     ], 403);
        // }

        if (!$request->user() || $request->user()->role !== $role) {
            // return response()->json(['message' => 'Unauthorized'], 403);
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Hanya ' . $role . ' yang dapat mengakses route ini.'
            ], 403);
        }

        return $next($request);
    }
}
