@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.DefaultDatatable', true)

@section('plugins.Sweetalert2', true)

@section('title', 'Bancos')

@section('content_header')
    <div class="row">
        <div class="col-md-9">
            <h1>Bancos</h1>
        </div>
        <div class="text-right col-md-3">
            <a href="{{ route('bancos.crear', ['idPais' => $idPais]) }}" class="btn btn-primary"><i class="fas fa-plus"></i>
                Crear banco</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('paises.lista') }}">Paises</a></li>
            <li class="breadcrumb-item active"><a href="{{ route('bancos.lista', ['idPais' => $idPais]) }}">Bancos</a></li>
            
        </ol>
    </nav>
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
                        <th>Aplica tipo de cuenta</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bancos as $banco)
                        <tr>
                            <td>{{ $banco->id }}</td>
                            <td>{{ $banco->nombre }}</td>
                            <td>
                                @if ($banco->ap_tipo_cuenta)
                                    <span class="p-0 px-3 alert alert-success">Aplica</span>
                                @else
                                    <span class="p-0 px-3 alert alert-secondary">No Aplica</span>
                                @endif
                            </td>

                            <td class="text-right">
                                <a href="{{ route('bancos.modificar', ['id' => $banco->id]) }}"
                                    class="btn btn-outline-primary"><i class="fas fa-pen"></i> Editar</a>

                                <a href="{{ route('bancos.eliminar', ['id' => $banco->id]) }}"
                                    class="btn btn-outline-danger ask" data-message="Eliminar esta banco?"><i
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
