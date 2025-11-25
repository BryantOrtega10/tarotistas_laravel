@extends('adminlte::page')

@section('title', 'Nuevo pais')

@section('content_header')
    <h1>Nuevo pais</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('paises.lista') }}">Paises</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear pais</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('paises.crear') }}" method="post" enctype="multipart/form-data">
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
                            <label for="bandera">Bandera (64px*64px):</label>
                            <input type="file" class="form-control" id="bandera" name="bandera" accept="image/*">
                            @error('bandera')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>      
            </div>
            <div class="text-right card-footer">
                <input type="submit" class="btn btn-lg btn-primary" value="Crear pais" />
            </div>
        </form>
    </div>
@stop

@section('js')

@stop