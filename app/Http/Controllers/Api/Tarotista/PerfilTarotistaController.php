<?php

namespace App\Http\Controllers\Api\Tarotista;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tarotista\Perfil\CompletarCuentaTarotistaRequest;
use App\Http\Requests\Api\Tarotista\Perfil\CompletarPerfilTarotistaRequest;
use App\Models\EspecialidadesModel;
use App\Models\EspecialidadesTatoristaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        }
        else{
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
}
