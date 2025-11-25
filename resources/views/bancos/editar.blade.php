@extends('adminlte::page')

@section('title', 'Editar banco')

@section('content_header')
    <h1>Editar banco</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('paises.lista') }}">Paises</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bancos.lista', ['idPais' => $banco->fk_pais]) }}">Bancos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar banco</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('bancos.modificar', ['id' => $banco->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="nombre">Nombre (*):</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" placeholder="Nombre:" value="{{ old('nombre', $banco->nombre) }}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="aplica_cuenta">Aplica cuenta de ahorros:</label>
                            <select id="aplica_cuenta"  name="aplica_cuenta" class="form-control @error('aplica_cuenta') is-invalid @enderror">
                                <option value="1" @if(old('aplica_cuenta',$banco->ap_tipo_cuenta) == 1) selected @endif>SI</option>
                                <option value="0" @if(old('aplica_cuenta',$banco->ap_tipo_cuenta) == 0) selected @endif>NO</option>
                            </select>
                            @error('aplica_cuenta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>      
            </div>
            <div class="text-right card-footer">
                <input type="submit" class="btn btn-lg btn-primary" value="Editar banco" />
            </div>
        </form>
    </div>
@stop

@section('js')

@stop