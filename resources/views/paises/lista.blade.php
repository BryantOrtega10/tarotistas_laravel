@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.DefaultDatatable', true)

@section('plugins.Sweetalert2', true)

@section('title', 'Paises')

@section('content_header')
    <div class="row">
        <div class="col-md-9">
            <h1>Paises</h1>
        </div>
        <div class="text-right col-md-3">
            <a href="{{ route('paises.crear') }}" class="btn btn-primary"><i class="fas fa-plus"></i>
                Crear pais</a>
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
                        <th>Bandera</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paises as $pais)
                        <tr>
                            <td>{{ $pais->id }}</td>
                            <td>{{ $pais->nombre }}</td>
                            <td>
                                @if(isset($pais->bandera) && $pais->bandera)
                                    <img src="{{ Storage::url('paises/' . $pais->bandera) }}" width="64" />
                                @else
                                    Sin imagen
                                @endisset
                            </td>

                            <td class="text-right">
                                <a href="{{ route('paises.modificar', ['id' => $pais->id]) }}"
                                    class="btn btn-outline-primary"><i class="fas fa-pen"></i> Editar</a>

                                <a href="{{ route('bancos.lista', ['idPais' => $pais->id]) }}"
                                    class="btn btn-outline-secondary"><i class="fas fa-university"></i> Administrar Bancos</a>

                                <a href="{{ route('paises.eliminar', ['id' => $pais->id]) }}"
                                    class="btn btn-outline-danger ask" data-message="Eliminar esta pais?"><i
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
