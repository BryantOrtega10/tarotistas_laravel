@extends('adminlte::page')
@section('plugins.Sweetalert2', true)

@section('title', 'Configuración')

@section('content_header')
    <div class="row">
        <div class="col-md-9">
            <h1>Configuración</h1>
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
        <form action="{{ route('configuracion.index') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="token_min">Tokens &times; minuto (*):</label>
                            <input type="number" min="1" step="1" class="form-control @error('token_min') is-invalid @enderror" id="token_min"
                                name="token_min" placeholder="Tokens por minuto" value="{{ old('token_min',$configuracion->token_min) }}">
                            @error('token_min')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="valor_min">Valor &times; minuto (*):</label>
                            <input type="number" min="1" step="1"  class="form-control @error('valor_min') is-invalid @enderror" id="valor_min"
                                name="valor_min" placeholder="Valor por minuto:" value="{{ old('valor_min',$configuracion->valor_min) }}">
                            @error('valor_min')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="por_comision">Porcentaje de comision (*):</label>
                            <input type="number" min="1" max="100" step="0.01" class="form-control @error('por_comision') is-invalid @enderror" id="por_comision"
                                name="por_comision" placeholder="Porcentaje de comision:" value="{{ old('por_comision',$configuracion->por_comision) }}">
                            @error('por_comision')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right card-footer">
                <input type="submit" class="btn btn-lg btn-primary" value="Actualizar configuración" />
            </div>
        </form>
    </div>
@stop

@section('js')

@stop
