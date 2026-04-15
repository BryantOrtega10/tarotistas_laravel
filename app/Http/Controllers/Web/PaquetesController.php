<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Paquetes\CrearPaquetesRequest;
use App\Http\Requests\Web\Paquetes\EditarPaquetesRequest;
use App\Http\Utils\Funciones;
use App\Models\PaquetesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaquetesController extends Controller
{
    public function lista()
    {
        $paquetes = PaquetesModel::where("estado", "<>", "2")->orderBy("tokens", "asc")->get();
        return view('paquetes.lista', compact('paquetes'));
    }

    public function mostrarCrear()
    {
        return view('paquetes.crear');
    }

    public function crear(CrearPaquetesRequest $request)
    {
        $paquete = new PaquetesModel();
        $paquete->nombre = $request->input("nombre");
        $paquete->tokens = $request->input("tokens");
        $paquete->valor = $request->input("valor");
        if ($request->has("imagen")) {
            $file = $request->file("imagen");
            $file_name =  "paquete-tokens-" . time() . ".jpg";
            $file->move(public_path("storage/paquetes"), $file_name);
            $path = explode($file_name, Storage::disk("public")->path("paquetes/" . $file_name));
            $pathFinal = Funciones::resizeImage($path[0], $file_name, "paquete", 300, 300);
            $pathFinal = explode("/", $pathFinal);
            $finalFileName = last($pathFinal);

            $paquete->imagen = $finalFileName;
        }
        $paquete->estado = 1;
        $paquete->fk_last_user = $request->user()->id;
        $paquete->save();
        
        return redirect(route('paquetes.lista'))->with('message', 'Paquete creado correctamente');
    }

    public function mostrarModificar($id)
    {
        $paquete = PaquetesModel::find($id);
        return view('paquetes.editar', compact('paquete'));
    }

    public function modificar($id, EditarPaquetesRequest $request)
    {
        $paquete = PaquetesModel::find($id);
        $paquete->nombre = $request->input("nombre");
        $paquete->tokens = $request->input("tokens");
        $paquete->valor = $request->input("valor");
        if ($request->has("imagen")) {
            if (isset($paquete->imagen)) {
                Storage::disk('public')->delete('paquetes/' . $paquete->imagen);
            }
            $file = $request->file("imagen");
            $file_name =  "paquete-tokens-" . time() . ".jpg";
            $file->move(public_path("storage/paquetes"), $file_name);

            $path = explode($file_name, Storage::disk("public")->path("paquetes/" . $file_name));
            $pathFinal = Funciones::resizeImage($path[0], $file_name, "paquete", 300, 300);
            $pathFinal = explode("/", $pathFinal);
            $finalFileName = last($pathFinal);
            $paquete->imagen = $finalFileName;
        }
        $paquete->estado = $request->input("estado");
        $paquete->fk_last_user = $request->user()->id;
        $paquete->save();
        return redirect(route('paquetes.lista'))->with('message', 'Paquete modificado correctamente');
    }

    public function eliminar($id)
    {
        $paquete = PaquetesModel::find($id);
        $paquete->estado = 2;
        $paquete->save();

        return redirect(route('paquetes.lista'))->with('message', 'Paquete eliminado correctamente');
    }
}
