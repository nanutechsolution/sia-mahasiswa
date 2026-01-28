<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPmbToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil Token dari Header
        $token = $request->header('X-PMB-KEY');

        // 2. Ambil Kunci Rahasia dari .env
        $validToken = config('services.pmb.secret');

        // 3. Cek Validitas
        if (!$token || $token !== $validToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized Access: Invalid or missing API Key.'
            ], 401);
        }

        // 4. (Opsional) Cek IP Address Whitelist
        // Hanya izinkan request dari IP Server PMB (misal: 123.456.78.9)
        // $allowedIps = ['127.0.0.1', '192.168.1.50', config('services.pmb.ip')];
        // if (!in_array($request->ip(), $allowedIps)) {
        //     return response()->json(['message' => 'Unauthorized IP Address'], 403);
        // }

        return $next($request);
    }
}
