<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Bancos\CrearBancoRequest;
use App\Http\Requests\Web\Bancos\EditarBancoRequest;
use App\Models\BancosModel;
use Illuminate\Http\Request;

class BancosController extends Controller
{
    public function lista($idPais)
    {
        $bancos = BancosModel::select('id', 'nombre', 'ap_tipo_cuenta')
            ->where('fk_pais', "=", $idPais)
            ->orderBy("nombre", "asc")
            ->get();

        return view('bancos.lista', compact('bancos','idPais'));
    }

    public function mostrarCrear($idPais)
    {
        return view('bancos.crear',compact('idPais'));
    }

    public function crear($idPais, CrearBancoRequest $request)
    {
        $banco = new BancosModel();
        $banco->nombre = $request->input("nombre");
        $banco->ap_tipo_cuenta = $request->input("aplica_cuenta");
        $banco->fk_pais = $idPais;
        $banco->save();
        return redirect(route('bancos.lista',['idPais' => $idPais]))->with('message', 'Banco creado correctamente');
    }

    public function mostrarModificar($id)
    {
        $banco = BancosModel::find($id);
        return view('bancos.editar', compact('banco'));
    }

    public function modificar($id, EditarBancoRequest $request)
    {
        $banco = BancosModel::find($id);
        $banco->nombre = $request->input("nombre");
        $banco->ap_tipo_cuenta = $request->input("aplica_cuenta");
        $banco->save();
        return redirect(route('bancos.lista',['idPais' => $banco->fk_pais]))->with('message', 'Banco modificado correctamente');
    }

    public function eliminar($id)
    {
        $banco = BancosModel::find($id);
        //Verificar si tiene tarotistas que no esten rechazados
        //Entonces no puede borrar el registro de pais
        $idPais = $banco->fk_pais;
        $banco->delete();
        return redirect(route('bancos.lista',['idPais' => $idPais]))->with('message', 'Banco eliminado correctamente');
    }
}
