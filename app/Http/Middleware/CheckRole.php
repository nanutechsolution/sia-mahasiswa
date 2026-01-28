<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // [FIX] Support syntax pipa (|) ala Spatie
        // Menggabungkan semua parameter dan memecahnya berdasarkan '|'
        $allowedRoles = [];
        foreach ($roles as $role) {
            $parts = explode('|', $role);
            $allowedRoles = array_merge($allowedRoles, $parts);
        }

        // Cek apakah role user (kolom native) ada di daftar yang diizinkan
        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        // Opsional: Cek juga via Spatie (jika user pakai assignRole tapi kolom native belum update)
        if (method_exists($user, 'hasRole')) {
            if ($user->hasAnyRole($allowedRoles)) {
                return $next($request);
            }
        }

        abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk halaman ini.');
    }
}