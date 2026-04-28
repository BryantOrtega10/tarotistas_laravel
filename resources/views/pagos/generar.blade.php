@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('title', 'Pagos')

@section('content_header')
    <div class="row">
        <div class="col-md-12">
            <h1>Generar pago para {{ $tarotista->nombre }}</h1>
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

    <form method="post" action="{{ route('pagos.generar', ['idTarotista' => $tarotista->id]) }}"
        enctype="multipart/form-data">
        @csrf
        <div class="card card-light mb-5">
            <div class="card-header">
                Generar nuevo pago
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="valor">Valor:</label>
                            <input type="number" min="1" class="form-control @error('valor') is-invalid @enderror"
                                id="valor" name="valor" value="{{ old('valor') }}">
                            @error('valor')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="valor">Saldo actual:</label>
                            <input type="number" min="1" class="form-control @error('valor') is-invalid @enderror"
                                readonly value="{{ $tarotista->saldo }}">
                            @error('valor')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="banco">Banco:</label>
                            <select disabled
                                class="form-control @error('banco') is-invalid @enderror">
                                <option value=""></option>
                                @foreach ($bancos as $banco)
                                    <option value="{{ $banco->id }}" @if (old('banco', $tarotista->fk_banco) == $banco->id) selected @endif>
                                        {{ $banco->nombre }} </option>
                                @endforeach
                            </select>
                            @error('banco')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12 @if ($tarotista->banco?->ap_tipo_cuenta == 0) d-none @endif">
                        <div class="form-group">
                            <label for="tipo_cuenta">Tipo Cuenta:</label>
                            <input type="text" class="form-control @error('tipo_cuenta') is-invalid @enderror" readonly
                                
                                @if ($tarotista->tipo_cuenta == 1) value="Ahorros" @elseif($tarotista->tipo_cuenta == 2) value="Corriente" @else value="No Aplica" @endif>
                            @error('tipo_cuenta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="tipo_cuenta">Cuenta:</label>
                            <input type="text" class="form-control @error('tipo_cuenta') is-invalid @enderror" readonly
                                 value="{{ $tarotista->cuenta }}">
                            @error('tipo_cuenta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12 col-12">
                        <div class="form-group">
                            <label for="descripcion">Descripcion:</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" rows="5" id="descripcion"
                                name="descripcion">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-right">
                    <input type="submit" class="btn btn-lg btn-primary" value="Agregar pago" />
                </div>
            </div>
        </div>
    </form>


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
