<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Paises\CrearPaisRequest;
use App\Http\Requests\Web\Paises\EditarPaisRequest;
use App\Models\PaisesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaisesController extends Controller
{
    public function lista()
    {
        $paises = PaisesModel::select('id', 'nombre', 'bandera')->orderBy("nombre", "asc")->get();
        return view('paises.lista', compact('paises'));
    }

    public function mostrarCrear()
    {
        return view('paises.crear');
    }

    public function crear(CrearPaisRequest $request)
    {
        $pais = new PaisesModel();
        $pais->nombre = $request->input("nombre");
        if ($request->has("bandera")) {
            $file = $request->file("bandera");
            $file_name =  "bandera-" . $pais->id. ".jpg";
            $file->move(public_path("storage/paises"), $file_name);
            $pais->bandera = $file_name;
        }
        $pais->save();
        return redirect(route('paises.lista'))->with('message', 'Pais creado correctamente');
    }

    public function mostrarModificar($id)
    {
        $pais = PaisesModel::find($id);
        return view('paises.editar', compact('pais'));
    }

    public function modificar($id, EditarPaisRequest $request)
    {
        $pais = PaisesModel::find($id);
        $pais->nombre = $request->input("nombre");
        if ($request->has("bandera")) {
            if (isset($pais->bandera)) {
                Storage::disk('public')->delete('paises/' . $pais->bandera);
            }
            $file = $request->file("bandera");
            $file_name =  "bandera-" . $pais->id. ".jpg";
            $file->move(public_path("storage/paises"), $file_name);
            $pais->bandera = $file_name;
        }

        $pais->save();
        return redirect(route('paises.lista'))->with('message', 'Pais modificado correctamente');
    }

    public function eliminar($id)
    {
        $pais = PaisesModel::find($id);
        
        //Verificar si tienen bancos y el count de relacion de esos bancos, si los bancos tienen tarotistas que no esten rechazados 
        //Entonces no puede borrar el registro de pais
         if (isset($pais->bandera)) {
            Storage::disk('public')->delete('paises/' . $pais->bandera);
        }
        $pais->delete();
        return redirect(route('paises.lista'))->with('message', 'Pais eliminado correctamente');
    }
}
