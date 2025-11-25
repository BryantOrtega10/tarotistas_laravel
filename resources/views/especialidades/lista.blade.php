@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.DefaultDatatable', true)

@section('plugins.Sweetalert2', true)

@section('title', 'Especialidades')

@section('content_header')
    <div class="row">
        <div class="col-md-9">
            <h1>Especialidades</h1>
        </div>
        <div class="text-right col-md-3">
            <a href="{{ route('especialidades.crear') }}" class="btn btn-primary"><i class="fas fa-plus"></i>
                Crear nueva</a>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($especialidades as $especialidad)
                        <tr>
                            <td>{{ $especialidad->id }}</td>
                            <td>{{ $especialidad->nombre }}</td>
                            <td class="text-right">
                                <a href="{{ route('especialidades.modificar', ['id' => $especialidad->id]) }}"
                                    class="btn btn-outline-primary"><i class="fas fa-pen"></i> Editar</a>
                                <a href="{{ route('especialidades.eliminar', ['id' => $especialidad->id]) }}"
                                    class="btn btn-outline-danger ask"
                                    data-message="Eliminar esta especialidad?"><i class="fas fa-trash"></i> Delete</a>
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
