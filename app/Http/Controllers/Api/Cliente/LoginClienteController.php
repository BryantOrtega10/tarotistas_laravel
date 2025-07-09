<?php

namespace App\Http\Controllers\Api\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRedesRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegistroRequest;
use App\Models\ClientesModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LoginClienteController extends Controller
{
    /**
     * Registro para el cliente por email y password
     * 
     * 
     * @param App\Http\Requests\Api\Tarotista\RegistroRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function registroEmail(RegistroRequest $request)
    {
        $user = new User();
        $user->name = $request->input("nombre");
        $user->email = $request->input("email");
        $user->role = "cliente";
        $user->password = Hash::make($request->input("password"));
        $user->save();

        $cliente = new ClientesModel();
        $cliente->nombre = $request->input("nombre");
        $cliente->fecha_nacimiento = $request->input("fecha_nacimiento");
        $cliente->fk_user = $user->id;
        $cliente->save();

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "data" => [
                "token" => $token
            ]

        ]);
    }

    /**
     * Login para el cliente por usuario y password
     * 
     * 
     * @param App\Http\Requests\Api\LoginRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                "success" => false,
                "message" => "Correo o contraseña incorrectos"
            ], 401);
        }

        $user = User::whereEmail($request->input("email"))->first();
        $cliente = ClientesModel::where("fk_user", "=", $user->id)->first();
        if (!isset($cliente)) {
            return response()->json([
                "success" => false,
                "message" => "Cliente no encontrado"
            ], 401);
        }

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "data" => [
                "token" => $token
            ]
        ], 200);
    }


    /**
     * Login para el cliente por un provider Google, Facebook, etc.
     * 
     * 
     * @param App\Http\Requests\Api\LoginRedesRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginRedes(LoginRedesRequest $request)
    {
        //TODO: Validar que el provider ID exista en el provider especifico
        $user = User::whereEmail($request->input("email"))->first();
        if (isset($user)) {
            if ($user->provider != $request->input("provider")) {
                return response()->json([
                    "success" => false,
                    "message" => "Ya has iniciado con otro proveedor, por favor inicia con: " . $user->provider
                ], 401);
            }
            if ($user->provider_id != $request->input("provider_id")) {
                return response()->json([
                    "success" => false,
                    "message" => "El ID de tu cuenta no coincide con los registrados: " . $user->provider
                ], 401);
            }
            $cliente = ClientesModel::where("fk_user", "=", $user->id)->first();
            if (!isset($cliente)) {
                return response()->json([
                    "success" => false,
                    "message" => "Cliente no encontrado"
                ], 401);
            }
        } else {
            $user = new User();
            $user->name = $request->input("nombre");
            $user->email = $request->input("email");
            $user->provider = $request->input("provider");
            $user->provider_id = $request->input("provider_id");
            $user->role = "cliente";
            $user->password = Hash::make(Str::random(8));
            $user->save();

            $cliente = new ClientesModel();
            $cliente->nombre = $request->input("nombre");
            $cliente->fecha_nacimiento = $request->input("fecha_nacimiento");
            $cliente->fk_user = $user->id;
            $cliente->save();
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        
        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "data" => [
                "token" => $token
            ]
        ], 200);
    }
}
