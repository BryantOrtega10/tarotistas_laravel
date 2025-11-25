@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('title', 'Tarotistas')

@section('content_header')
    <div class="row">
        <div class="col-md-12">
            <h1>Tarotistas</h1>
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
            <table class="table table-striped datatable min-w-100" data-url="{{ route('tarotistas.datatable') }}">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Tipo Registro</th>
                        <th>Estado</th>
                        <th>Pais</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="/js/tarotista/datatable.js"></script>
@stop
