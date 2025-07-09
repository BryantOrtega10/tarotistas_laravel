<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tarotista\Perfil\ActualizarPerfilTarotista;
use App\Http\Requests\Api\Tarotista\Perfil\CompletarCuentaTarotistaRequest;
use App\Http\Requests\Api\Tarotista\Perfil\CompletarPerfilTarotistaRequest;
use App\Http\Utils\Funciones;
use App\Models\EspecialidadesModel;
use App\Models\EspecialidadesTatoristaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class PerfilTarotistaController extends Controller
{
    /**
     * Sirve para agregar lo campos del usuario, puede usarse multiples veces si se tienen varios pasos para este registro.
     * Como los campos son opcionales solo cuando terminarRegistro este en 1 cambiara el estado del tarotista
     * 
     * 
     * @param App\Http\Requests\Api\Tarotista\Perfil\CompletarPerfilTarotistaRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function completarPerfil(CompletarPerfilTarotistaRequest $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        if ($request->filled("descripcionCorta")) {
            $tarotista->descripcion_corta = $request->input("descripcionCorta");
        }
        if ($request->filled("horarioInicio") && $request->filled("horarioFin")) {
            $horaInicioTxt = date("h:i a", strtotime($request->input("horarioInicio")));
            $horaFinTxt = date("h:i a", strtotime($request->input("horarioFin")));
            $tarotista->horario = $horaInicioTxt . " - " . $horaFinTxt;
        }

        if ($request->filled("aniosExp")) {
            $tarotista->anios_exp = $request->input("aniosExp");
        }
        if ($request->filled("pais")) {
            $tarotista->fk_pais = $request->input("pais");
        }
        if ($request->filled("especialidades")) {

            EspecialidadesTatoristaModel::where("fk_tarotista", "=", $tarotista->id)->delete();
            foreach ($request->input("especialidades") as $especialidad) {
                if ($especialidad["id"] !== null) {
                    EspecialidadesTatoristaModel::create([
                        "fk_especialidad" => $especialidad["id"],
                        "fk_tarotista" => $tarotista->id
                    ]);
                } else {
                    $especialidadBd = EspecialidadesModel::create([
                        'nombre' => $especialidad["nombre"]
                    ]);

                    EspecialidadesTatoristaModel::create([
                        "fk_especialidad" => $especialidadBd->id,
                        "fk_tarotista" => $tarotista->id
                    ]);
                }
            }
        }
        if ($request->has("terminarRegistro") && $request->input("terminarRegistro") == 1) {
            $tarotista->estado = 2;
        }

        $tarotista->save();

        return response()->json([
            "success" => true,
            "message" => "Perfil actualizado correctamente",
            "data" => [
                "status" => $tarotista->estado,
            ]

        ]);
    }

    /**
     * Sirve para modificar los datos de la cuenta a la que se le va a pagar al tarotista.
     * 
     * @param App\Http\Requests\Api\Tarotista\Perfil\CompletarCuentaTarotistaRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function completarCuenta(CompletarCuentaTarotistaRequest $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        if ($request->filled("tipoCuenta")) {
            $tarotista->tipo_cuenta = $request->input("tipoCuenta");
        } else {
            $tarotista->tipo_cuenta = null;
        }
        $tarotista->cuenta = $request->input("cuenta");
        $tarotista->fk_banco = $request->input("banco");
        $tarotista->estado = 2;
        $tarotista->save();

        return response()->json([
            "success" => true,
            "message" => "Perfil actualizado correctamente",
            "data" => [
                "status" => $tarotista->estado,
            ]

        ]);
    }

    /**
     * Sirve para modificar los datos de la cuenta a la que se le va a pagar al tarotista.
     * 
     * @param int $status
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function estadoConexion(int $status, Request $request)
    {
        $validStatus = [1, 3];
        if (!in_array($status, $validStatus)) {
            return  response()->json([
                "success" => false,
                "message" => "Estado no valido",
                "errors" => [
                    "conexion_status" => 'Estado no valido',
                ]
            ], 422);
        }

        $tarotista = $request->attributes->get('tarotista');
        $tarotista->estado_conexion = $status;
        $tarotista->save();

        return response()->json([
            "success" => true,
            "message" => "Estado de conexión actualizado correctamente",
            "data" => [
                "conexion_status" => $tarotista->estado_conexion,
            ]
        ]);
    }


    /**
     * Sirve para obtener los datos de cuenta registrados del tarotista
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerMiCuenta(Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        return response()->json([
            "success" => true,
            "message" => "Datos de cuenta consultados correctamente",
            "data" => [
                "tipo_cuenta" => $tarotista->tipo_cuenta,
                "cuenta" => $tarotista->cuenta,
                "banco_id" => $tarotista->fk_banco,
            ]

        ]);
    }

    /**
     * Sirve para actualizar los datos de cuenta registrados del tarotista
     * 
     * @param App\Http\Requests\Api\Tarotista\Perfil\CompletarCuentaTarotistaRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function modificarMiCuenta(CompletarCuentaTarotistaRequest $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        if ($request->filled("tipoCuenta")) {
            $tarotista->tipo_cuenta = $request->input("tipoCuenta");
        } else {
            $tarotista->tipo_cuenta = null;
        }
        $tarotista->cuenta = $request->input("cuenta");
        $tarotista->fk_banco = $request->input("banco");
        $tarotista->save();

        return response()->json([
            "success" => true,
            "message" => "Datos de cuenta actualizados correctamente",
            "data" => []
        ]);
    }

    /**
     * Sirve para obtener los datos básicos del tarotista
     * 
     * @param Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerMiPerfil(Request $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        [$horario_inicio, $horario_fin] = array_pad(explode(" - ", $tarotista->horario ?? ""), 2, "");

        return response()->json([
            "success" => true,
            "message" => "Datos de perfil consultados correctamente",
            "data" => [
                "nombre" => $tarotista->nombre,
                "photo" => $tarotista->user->photo,
                "descripcion_corta" => $tarotista->descripcion_corta,
                "anios_exp" => $tarotista->anios_exp,
                "pais_id" => $tarotista->fk_pais,
                "horario_inicio" => $horario_inicio,
                "horario_fin" => $horario_fin,
                "especialidades" => $tarotista->especialidades
            ]
        ]);
    }

    /**
     * Sirve para actualizar los datos básicos del tarotista
     * 
     * @param App\Http\Requests\Api\Tarotista\Perfil\ActualizarPerfilTarotista $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarMiPerfil(ActualizarPerfilTarotista $request)
    {
        $tarotista = $request->attributes->get('tarotista');

        if ($request->filled("nombre")) {
            $tarotista->user->name = $request->input("nombre");
            $tarotista->nombre = $request->input("nombre");
            $tarotista->user->save();
        }
        if ($request->filled("photo")) {
            $folder = "users/";
            $file_name =  uniqid() . "_user.png";
            Funciones::imagenBase64($request->filled("photo"), $folder.$file_name);
            $tarotista->user->photo = $file_name;
            $tarotista->user->save();
        }

        if ($request->filled("descripcionCorta")) {
            $tarotista->descripcion_corta = $request->input("descripcionCorta");
        }
        if ($request->filled("horarioInicio") && $request->filled("horarioFin")) {
            $horaInicioTxt = date("h:i a", strtotime($request->input("horarioInicio")));
            $horaFinTxt = date("h:i a", strtotime($request->input("horarioFin")));
            $tarotista->horario = $horaInicioTxt . " - " . $horaFinTxt;
        }

        if ($request->filled("aniosExp")) {
            $tarotista->anios_exp = $request->input("aniosExp");
        }
        if ($request->filled("pais")) {
            $tarotista->fk_pais = $request->input("pais");
        }
        if ($request->filled("especialidades")) {

            $arrNuevasEspecialidades = [];
            foreach ($request->input("especialidades") as $especialidad) {
                if ($especialidad["id"] !== null) {
                    $arrNuevasEspecialidades[] = $especialidad["id"];
                }
            }
            EspecialidadesTatoristaModel::where("fk_tarotista", "=", $tarotista->id)->whereNotIn("fk_especialidad",$arrNuevasEspecialidades)->delete();

            foreach ($request->input("especialidades") as $especialidad) {
                if ($especialidad["id"] !== null) {
                    EspecialidadesTatoristaModel::create([
                        "fk_especialidad" => $especialidad["id"],
                        "fk_tarotista" => $tarotista->id
                    ]);
                } else {
                    $especialidadBd = EspecialidadesModel::create([
                        'nombre' => $especialidad["nombre"]
                    ]);

                    EspecialidadesTatoristaModel::create([
                        "fk_especialidad" => $especialidadBd->id,
                        "fk_tarotista" => $tarotista->id
                    ]);
                }
            }
        }
        
        $tarotista->save();

        return response()->json([
            "success" => true,
            "message" => "Datos básicos actualizados correctamente",
            "data" => []
        ]);
    }
}
