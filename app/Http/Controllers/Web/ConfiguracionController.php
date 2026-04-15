<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Configuracion\EditarConfiguracionRequest;
use App\Models\ConfiguracionModel;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configuracion = ConfiguracionModel::first();
        return view('configuracion.index', compact('configuracion'));

    }

    public function modificar(EditarConfiguracionRequest $request)
    {
        $configuracion = ConfiguracionModel::first();
        $configuracion->token_min = $request->input("token_min");
        $configuracion->valor_min = $request->input("valor_min");
        $configuracion->por_comision = $request->input("por_comision");
        $configuracion->fk_last_user = $request->user()->id;
        $configuracion->save();
        return redirect(route('configuracion.index'))->with('message', 'Configuracion actualizada correctamente');
    }
}
