<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cliente\RegistroClienteRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Utils\Funciones;
use App\Models\ClientesModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{

    /**
     * Registro para el cliente
     * 
     * 
     * @param RegistroClienteRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function registro(RegistroClienteRequest $request)
    {

        $user = new User();
        $user->name = $request->input("nombre");
        $user->email = $request->input("email");
        $user->role = "cliente";

        if ($request->filled("tokenAuth")) {
            $user->token_auth = $request->input("tokenAuth");
            $user->auth_provider = $request->input("authProvider");
        }

        if ($request->filled("password")) {
            $user->password = Hash::make($request->input("password"));
        }
        $user->save();

        $cliente = new ClientesModel();
        $cliente->nombre = $request->input("nombre");
        $cliente->fecha_nacimiento = $request->input("fechaNacimiento");
        $cliente->fk_user = $user->id;
        $cliente->save();

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "Bienvenido!",
            "token" => $token
        ]);
    }


    public function login(LoginRequest $request)
    {

        if ($request->filled("password")) {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    "success" => false,
                    "message" => "Correo o contraseña incorrectos"
                ], 401);
            }
        }
        else{
            
        }
    }
}
