<?php

namespace App\Http\Middleware;

use App\Models\ClientesModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadClienteMiddleware
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
            $cliente = ClientesModel::where("fk_user","=",$user->id)->first();
            if (!isset($cliente)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no es un cliente',
                    'errors' => [
                        'estado' => 'El usuario no es un cliente'
                    ],
                ], 422);
            }
            $request->attributes->set('cliente', $cliente);
        }

        return $next($request);
    }
}
