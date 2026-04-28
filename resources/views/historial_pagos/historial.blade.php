@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('title', 'Pagos')

@section('content_header')
    <div class="row">
        <div class="col-md-12">
            <h1>Historial de pagos para {{ $tarotista->nombre }}</h1>
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
            <table class="table table-striped datatable min-w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Valor</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pagos as $pago)
                        <tr>
                            <td>{{ $pago->id }}</td>
                            <td>${{ number_format($pago->valor, 0, '.', '.') }}</td>
                            <td>{{ strlen($pago->descripcion) > 50 ? substr($pago->descripcion, 0, 50) . '...' : $pago->descripcion }}
                            </td>
                            <td>{{ $pago->entry_user->name }}</td>
                            <td>{{ date('d/m/Y H:i', strtotime($pago->created_at)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" align="center">No hay pagos registrados para este tarotista</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')

@stop
