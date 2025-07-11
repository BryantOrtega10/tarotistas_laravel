<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cliente\Perfil\ActualizarPerfilClienteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilClienteController extends Controller
{
    /**
     * Sirve para obtener los datos básicos del cliente
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerMiPerfil(Request $request)
    {
        $cliente = $request->attributes->get('cliente');

        return response()->json([
            "success" => true,
            "message" => "Datos de perfil consultados correctamente",
            "data" => [
                "nombre" => $cliente->nombre,
                "photo" => $cliente->user->photo,
                "email" => $cliente->user->email,
                "provider" => $cliente->user->provider,
                "fecha_nacimiento" => $cliente->fecha_nacimiento,
            ]
        ]);
    }

    /**
     * Sirve para actualizar los datos básicos del tarotista
     * 
     * @param App\Http\Requests\Api\Cliente\Perfil\ActualizarPerfilClienteRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarMiPerfil(ActualizarPerfilClienteRequest $request)
    {
        $cliente = $request->attributes->get('cliente');
        
        $cliente->nombre = $request->input("nombre");
        $cliente->fecha_nacimiento = $request->input("fecha_nacimiento");
        
        if($cliente->user->provider === "Correo"){
            $cliente->user->email = $request->input("email");
            if($request->filled("password")){
                $cliente->user->password = Hash::make($request->input("password"));
            }
            $cliente->user->save();
        }
        
        $cliente->save();

       
        return response()->json([
            "success" => true,
            "message" => "Datos básicos actualizados correctamente",
            "data" => []
        ]);
    }

    /**
     * Sirve para obtener los datos del medio de pago del cliente
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerMedioPago(Request $request)
    {
        $cliente = $request->attributes->get('cliente');
        //TODO: Conexion con SDK Braintree Paypal para obtener el detalle de un medio de pago
        $medioPago = $cliente->token_payu !== null;

        return response()->json([
            "success" => true,
            "message" => "Datos del medio de pago consultados correctamente",
            "data" => [
                "tieneMedioPago" => $medioPago
            ]
        ]);
    }
    
}
