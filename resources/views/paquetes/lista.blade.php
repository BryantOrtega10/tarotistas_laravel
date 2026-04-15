@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.DefaultDatatable', true)

@section('plugins.Sweetalert2', true)

@section('title', 'Paquetes')

@section('content_header')
    <div class="row">
        <div class="col-md-9">
            <h1>Paquetes</h1>
        </div>
        <div class="text-right col-md-3">
            <a href="{{ route('paquetes.crear') }}" class="btn btn-primary"><i class="fas fa-plus"></i>
                Crear paquete</a>
        </div>
    </div>
@stop

@section('content')
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <table class="table table-striped datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Imagen</th>
                        <th>Tokens</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Fecha &uacute;ltima actualizaci&oacute;n</th>
                        <th>Usuario &uacute;ltima actualizaci&oacute;n</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paquetes as $paquete)
                        <tr>
                            <td>{{ $paquete->id }}</td>
                            <td>{{ $paquete->nombre }}</td>
                            <td>
                                @if(isset($paquete->imagen) && $paquete->imagen)
                                    <img src="{{ Storage::url('paquetes/' . $paquete->imagen) }}" width="100" />
                                @else
                                    Sin imagen
                                @endisset
                            </td>
                            <td>{{ $paquete->tokens }}</td>
                            <td>${{ number_format($paquete->valor,0) }}</td>
                            <td>{{ $paquete->txt_estado }}</td>
                            <td>{{ date("d/m/Y H:i",strtotime($paquete->updated_at)) }}</td>
                            <td>{{ $paquete->last_user->name }}</td>
                            <td class="text-right">
                                <a href="{{ route('paquetes.modificar', ['id' => $paquete->id]) }}"
                                    class="btn btn-outline-primary"><i class="fas fa-pen"></i> Editar</a>

                                <a href="{{ route('paquetes.eliminar', ['id' => $paquete->id]) }}"
                                    class="btn btn-outline-danger ask" data-message="Eliminar esta paquete?"><i
                                        class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')

@stop
