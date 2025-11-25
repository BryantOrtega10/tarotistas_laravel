<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Tarotista\AprobarRequest;
use App\Http\Requests\Web\Tarotista\EditarRequest;
use App\Models\EspecialidadesModel;
use App\Models\EspecialidadesTatoristaModel;
use App\Models\PaisesModel;
use App\Models\TarotistasModel;
use Illuminate\Http\Request;

class TarotistaController extends Controller
{
    public function lista()
    {
        return view('tarotistas.lista');
    }

    public function datatableAjax(Request $request)
    {
        $tarotistas = TarotistasModel::select("tarotistas.*", "users.provider", "users.email", "paises.nombre as pais_n")
            ->join("users", "users.id", "=", "tarotistas.fk_user")
            ->join("paises", "paises.id", "=", "tarotistas.fk_pais")
            ->where("tarotistas.estado", ">=", 2);

        if ($request->has('search') && $request->input('search')['value']) {
            $searchTxt = $request->input('search')['value'];
            $tarotistas->where(function ($query) use ($searchTxt) {
                $query->where("tarotistas.nombre", "like", "%{$searchTxt}%")
                    ->orWhere("users.email", "like", "%{$searchTxt}%")
                    ->orWhere("users.provider", "like", "%{$searchTxt}%")
                    ->orWhere("paises.nombre", "like", "%{$searchTxt}%")
                    ->orWhereRaw("CASE 
                                    WHEN tarotistas.estado = 1 THEN 'En Registro' 
                                    WHEN tarotistas.estado = 2 THEN 'Esperando aprobación' 
                                    WHEN tarotistas.estado = 3 THEN 'Activado' 
                                    WHEN tarotistas.estado = 4 THEN 'Rechazado' 
                                    ELSE 'Desconocido'
                                END LIKE '%{$searchTxt}%'");
            });
        }

        if ($request->has('order')) {
            $column = $request->input('order')[0]['column'];
            $direction = $request->input('order')[0]['dir'];
            switch ($column) {
                case '0':
                    $tarotistas->orderBy("tarotistas.nombre", $direction);
                    break;
                case '1':
                    $tarotistas->orderBy("users.email", $direction);
                    break;
                case '2':
                    $tarotistas->orderBy("users.provider", $direction);
                    break;
                case '3':
                    $tarotistas->orderBy("tarotistas.estado", $direction);
                    break;
                case '4':
                    $tarotistas->orderBy("tarotistas.fk_pais", $direction);
                    break;
            }
        } else {
            $tarotistas->orderBy("tarotistas.estado", "ASC");
        }

        $totalRecords = $tarotistas->count();
        $tarotistas = $tarotistas->skip($request->input('start'))
            ->take($request->input('length'))
            ->get();

        $filteredRecords = array();

        foreach ($tarotistas as $tarotista) {
            $filteredRecord = array();
            $filteredRecord["nombre"] = $tarotista->nombre;
            $filteredRecord["email"] = $tarotista->user->email;
            $filteredRecord["provider"] = $tarotista->user->provider;
            $filteredRecord["estado"] = $tarotista->txt_estado;
            $filteredRecord["pais"] = $tarotista->pais?->nombre ?? "";
            if($tarotista->estado === 2){
                $filteredRecord["accion"]["href"] = route('tarotistas.aprobar',['id' => $tarotista->id]);
                $filteredRecord["accion"]["text"] = "Aprobar/Rechazar";
            }
            else{
                $filteredRecord["accion"]["href"] = route('tarotistas.editar',['id' => $tarotista->id]);
                $filteredRecord["accion"]["text"] = "Editar";
            }


            

            array_push($filteredRecords, $filteredRecord);
        }


        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $filteredRecords
        ]);
    }

    public function mostrarAprobar($id)
    {
        $paises = PaisesModel::orderBy("nombre", "asc")->get();
        $especialidades = EspecialidadesModel::select('id','nombre')->orderBy("nombre", "asc")->get();
        $tarotista = TarotistasModel::find($id);
        return view('tarotistas.aprobar', compact('paises', 'especialidades', 'tarotista'));
    }

    public function aprobar($id, AprobarRequest $request)
    {
        $tarotista = TarotistasModel::find($id);
        $tarotista->nombre = $request->input("nombre");
        $tarotista->descripcion_corta = $request->input("descripcion_corta");
        if ($request->filled("horarioInicio") && $request->filled("horarioFin")) {
            $horaInicioTxt = date("h:i a", strtotime($request->input("horarioInicio")));
            $horaFinTxt = date("h:i a", strtotime($request->input("horarioFin")));
            $tarotista->horario = $horaInicioTxt . " - " . $horaFinTxt;
        }
        $tarotista->anios_exp = $request->input("anios_exp");
        $tarotista->estado = 3;
        $tarotista->save();

        $arrEspecialidadesRel = [];
        foreach ($request->input("especialidadIDs",[]) as $relID) {
            $arrEspecialidadesRel[] = $relID;
        }
        EspecialidadesTatoristaModel::whereNotIn("id", $arrEspecialidadesRel)->delete();

        foreach ($request->input("especialidad",[]) as $row => $especialidad) {            
            if(isset($request->input("especialidadIDs")[$row])){
                $idEspecialidad = $request->input("especialidadIDs")[$row];
                EspecialidadesTatoristaModel::where("id", "=", $idEspecialidad)->update(["fk_especialidad" => $especialidad]);
            }
            else{
                EspecialidadesTatoristaModel::create([
                    "fk_especialidad" => $especialidad,
                    "fk_tarotista" => $tarotista->id
                ]);
            }           
        }

        return redirect(route('tarotistas.lista'))->with('message', 'El tarotista fue aprobado correctamente');

    }

    public function rechazar($id)
    {
        $tarotista = TarotistasModel::find($id);
        $tarotista->estado = 4;
        $tarotista->save();
        return redirect(route('tarotistas.lista'))->with('message', 'El tarotista fue rechazado correctamente');
    }


    public function mostrarEditar($id)
    {
        $paises = PaisesModel::orderBy("nombre", "asc")->get();
        $especialidades = EspecialidadesModel::select('id','nombre')->orderBy("nombre", "asc")->get();
        $tarotista = TarotistasModel::find($id);
        return view('tarotistas.editar', compact('paises', 'especialidades', 'tarotista'));
    }

    public function editar($id, EditarRequest $request)
    {
        $tarotista = TarotistasModel::find($id);
        $tarotista->nombre = $request->input("nombre");
        $tarotista->descripcion_corta = $request->input("descripcion_corta");
        if ($request->filled("horarioInicio") && $request->filled("horarioFin")) {
            $horaInicioTxt = date("h:i a", strtotime($request->input("horarioInicio")));
            $horaFinTxt = date("h:i a", strtotime($request->input("horarioFin")));
            $tarotista->horario = $horaInicioTxt . " - " . $horaFinTxt;
        }
        $tarotista->anios_exp = $request->input("anios_exp");
        //$tarotista->fk_pais = $request->input("pais");
        $tarotista->save();

        $arrEspecialidadesRel = [];
        foreach ($request->input("especialidadIDs",[]) as $relID) {
            $arrEspecialidadesRel[] = $relID;
        }
        EspecialidadesTatoristaModel::whereNotIn("id", $arrEspecialidadesRel)->delete();

        foreach ($request->input("especialidad",[]) as $row => $especialidad) {            
            if(isset($request->input("especialidadIDs")[$row])){
                $idEspecialidad = $request->input("especialidadIDs")[$row];
                EspecialidadesTatoristaModel::where("id", "=", $idEspecialidad)->update(["fk_especialidad" => $especialidad]);
            }
            else{
                EspecialidadesTatoristaModel::create([
                    "fk_especialidad" => $especialidad,
                    "fk_tarotista" => $tarotista->id
                ]);
            }           
        }

        return redirect(route('tarotistas.lista'))->with('message', 'El tarotista fue modificado correctamente');

    }
}
