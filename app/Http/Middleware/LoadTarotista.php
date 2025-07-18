<?php

namespace App\Http\Middleware;

use App\Models\TarotistasModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadTarotista
{
    /**
     * Handle an incoming request. 
     * Agregar tarotista al request
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $tarotista = TarotistasModel::where("fk_user", "=", $user->id)->first();
            if (!isset($tarotista)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no es un tarotista',
                    'errors' => [
                        'estado' => 'El usuario no es un tarotista'
                    ],
                ], 422);
            }
            $request->attributes->set('tarotista', $tarotista);
        }

        return $next($request);
    }
}
