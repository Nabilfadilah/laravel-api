<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // middleware untuk membatasi akses berdasarkan role
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // if ($request->user()->role !== $role) {
        //     return response()->json([
        //         'message' => 'Unauthorized - only ' . $role . ' can access this route.'
        //     ], 403);
        // }

        // cek apakah user login dan role-nya sesuai
        if (!$request->user() || $request->user()->role !== $role) {
            // return response()->json(['message' => 'Unauthorized'], 403);
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Hanya ' . $role . ' yang dapat mengakses route ini.'
            ], 403);
        }

        return $next($request); // lanjut ke proses selanjutnya
    }
}
