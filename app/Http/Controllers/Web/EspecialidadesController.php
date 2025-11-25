<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Especialidades\CrearEspecialidadRequest;
use App\Http\Requests\Web\Especialidades\EditarEspecialidadRequest;
use App\Models\EspecialidadesModel;
use App\Models\EspecialidadesTatoristaModel;
use Illuminate\Http\Request;

class EspecialidadesController extends Controller
{
    public function lista(){
        $especialidades = EspecialidadesModel::select('id','nombre')->orderBy("nombre", "asc")->get();
        return view('especialidades.lista', compact('especialidades'));
    }

    public function mostrarCrear(){
        return view('especialidades.crear');
    }

    public function crear(CrearEspecialidadRequest $request){
        $especialidad = new EspecialidadesModel();
        $especialidad->nombre = $request->input("nombre");
        $especialidad->save();
        return redirect(route('especialidades.lista'))->with('message', 'Especialidad creada correctamente');
    }

    public function mostrarModificar($id){
        $especialidad = EspecialidadesModel::find($id);
        return view('especialidades.editar',compact('especialidad'));
    }
    
    public function modificar($id, EditarEspecialidadRequest $request){
        $especialidad = EspecialidadesModel::find($id);
        $especialidad->nombre = $request->input("nombre");
        $especialidad->save();
        return redirect(route('especialidades.lista'))->with('message', 'Especialidad modificada correctamente');
    }

    public function eliminar($id){
        $especialidad = EspecialidadesModel::find($id);
        EspecialidadesTatoristaModel::where("fk_especialidad","=",$id)->delete();
        $especialidad->delete();
        return redirect(route('especialidades.lista'))->with('message', 'Especialidad eliminada correctamente');

    }
}
