@extends('adminlte::page')

@section('title', 'Nuevo paquete')

@section('content_header')
    <h1>Nuevo paquete</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('paquetes.lista') }}">Paquetes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear paquete</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('paquetes.crear') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="nombre">Nombre (*):</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" placeholder="Nombre:" value="{{ old('nombre') }}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="imagen">Imagen (300px &times; 300px):</label>
                            <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                            @error('imagen')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="tokens">Tokens (*):</label>
                            <input type="number" min="1" class="form-control @error('tokens') is-invalid @enderror" id="tokens" name="tokens" placeholder="Tokens:" value="{{ old('tokens') }}">
                            @error('tokens')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="valor">Valor (*):</label>
                            <input type="number" min="1" class="form-control @error('valor') is-invalid @enderror" id="valor" name="valor" placeholder="Valor:" value="{{ old('valor') }}">
                            @error('valor')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>      
            </div>
            <div class="text-right card-footer">
                <input type="submit" class="btn btn-lg btn-primary" value="Crear paquete" />
            </div>
        </form>
    </div>
@stop

@section('js')

@stop