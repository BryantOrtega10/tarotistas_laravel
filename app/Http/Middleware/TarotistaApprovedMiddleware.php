<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TarotistaApprovedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tarotista = $request->attributes->get('tarotista');
        if ($tarotista->estado !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'El tarotista debe estar activado',
                'errors' => [
                    'estado' => 'El tarotista debe estar activado'
                ],
            ], 422);
        }

        return $next($request);
    }
}
