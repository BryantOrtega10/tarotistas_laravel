<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRedesRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\Tarotista\RegistroRedesRequest;
use App\Http\Requests\Api\Tarotista\RegistroRequest;
use App\Http\Utils\Funciones;
use App\Models\TarotistasModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TarotistaController extends Controller
{
    /**
     * Registro para el tarotista por email y password
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
        $user->role = "tarotista";
        $user->password = Hash::make($request->input("password"));
        $user->save();

        $tarotista = new TarotistasModel();
        $tarotista->nombre = $request->input("nombre");
        $tarotista->fk_user = $user->id;
        $tarotista->save();

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "Bienvenido ". $user->name,
            "token" => $token
        ]);
    }

    /**
     * Login para el tarotista por usuario y password
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
        $tarotista = TarotistasModel::where("fk_user", "=", $user->id)->first();
        if (!isset($tarotista)) {
            return response()->json([
                "success" => false,
                "message" => "Tarotista no encontrado"
            ], 401);
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "token" => $token
        ], 200);
    }


    /**
     * Login para el tarotista por un provider Google, Facebook, etc.
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
        if(isset($user)){
            if($user->provider != $request->input("provider")){
                return response()->json([
                    "success" => false,
                    "message" => "Ya has iniciado con otro proveedor, por favor inicia con: ".$user->provider
                ], 401);
            }
            if($user->provider_id != $request->input("provider_id")){
                return response()->json([
                    "success" => false,
                    "message" => "El ID de tu cuenta no coincide con los registrados: ".$user->provider
                ], 401);
            }
            $tarotista = TarotistasModel::where("fk_user", "=", $user->id)->first();
            if (!isset($tarotista)) {
                return response()->json([
                    "success" => false,
                    "message" => "Tarotista no encontrado"
                ], 401);
            }
        }
        else{
            $user = new User();
            $user->name = $request->input("nombre");
            $user->email = $request->input("email");
            $user->role = "tarotista";
            $user->password = Hash::make($request->input("password"));
            $user->save();

            $tarotista = new TarotistasModel();
            $tarotista->nombre = $request->input("nombre");
            $tarotista->fk_user = $user->id;
            $tarotista->save();
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "token" => $token
        ], 200);
    }


}
