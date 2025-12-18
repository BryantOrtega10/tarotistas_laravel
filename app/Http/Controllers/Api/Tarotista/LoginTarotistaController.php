<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRedesRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegistroRequest;
use App\Models\TarotistasModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class LoginTarotistaController extends Controller
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
        $user->token_push = $request->input("tokenPush");
        if ($request->has("image")) {
            if (isset($user->photo)) {
                Storage::disk('public')->delete('users/' . $user->photo);
            }
            $file = $request->file("image");
            $file_name =  time() . "_" . $file->getClientOriginalName();
            $file->move(public_path("storage/users"), $file_name);
            $user->photo = $file_name;
        }

        $user->role = "tarotista";
        $user->password = Hash::make($request->input("password"));
        $user->save();

        $tarotista = new TarotistasModel();
        $tarotista->nombre = $request->input("nombre");
        $tarotista->estado = 1;
        $tarotista->fk_user = $user->id;
        $tarotista->save();

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "data" => [
                "token" => $token,
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "photo" => $user->photo,
                    "status" => $tarotista->estado,
                ]
            ]

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
            ], 403);
        }

        $user = User::whereEmail($request->input("email"))->first();
        $tarotista = TarotistasModel::where("fk_user", "=", $user->id)->first();
        if (!isset($tarotista)) {
            return response()->json([
                "success" => false,
                "message" => "Tarotista no encontrado"
            ], 403);
        }

        if ($tarotista->estado === 2) {
            return response()->json([
                "success" => false,
                "message" => "Se esta validando tu información, vuelve a intentar ingresar mas tarde"
            ], 403);
        }
        if ($tarotista->estado === 4) {
            return response()->json([
                "success" => false,
                "message" => "Tu usuario ha sido bloqueado, contacta con el administrador para saber la razón de tu bloqueo"
            ], 403);
        }

        $user->token_push = $request->input("tokenPush");
        $user->save();

        $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "data" => [
                "token" => $token,
                "status" => $tarotista->estado,
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "photo" => $user->photo,
                    "status" => $tarotista->estado,
                ]
            ]
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
        if (isset($user)) {
            if ($user->provider != $request->input("provider")) {
                return response()->json([
                    "success" => false,
                    "message" => "Ya has iniciado con otro proveedor, por favor inicia con: " . $user->provider
                ], 400);
            }
            if ($user->provider_id != $request->input("provider_id")) {
                return response()->json([
                    "success" => false,
                    "message" => "El ID de tu cuenta no coincide con los registrados: " . $user->provider
                ], 400);
            }
            $tarotista = TarotistasModel::where("fk_user", "=", $user->id)->first();
            if (!isset($tarotista)) {
                return response()->json([
                    "success" => false,
                    "message" => "Tarotista no encontrado"
                ], 400);
            }
        } else {
            $user = new User();
            $user->name = $request->input("name");
            $user->email = $request->input("email");
            $user->provider = $request->input("provider");
            $user->provider_id = $request->input("provider_id");
            $user->role = "tarotista";
            $user->password = Hash::make(Str::random(8));
            $user->save();

            $tarotista = new TarotistasModel();
            $tarotista->nombre = $request->input("name");
            $tarotista->estado = 1;
            $tarotista->fk_user = $user->id;
            $tarotista->save();
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        $user->token_push = $request->input("tokenPush");
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "Bienvenido " . $user->name,
            "data" => [
                "token" => $token,
                "status" => $tarotista->estado,
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "photo" => $user->photo,
                    "status" => $tarotista->estado,
                ]
            ]
        ], 200);
    }
}
