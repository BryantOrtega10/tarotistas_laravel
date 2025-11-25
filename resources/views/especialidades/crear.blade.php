@extends('adminlte::page')

@section('title', 'Nueva especialidad')

@section('content_header')
    <h1>Nueva especialidad</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('especialidades.lista') }}">Especialidades</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear especialidad</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('especialidades.crear') }}" method="post">
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
                </div>      
            </div>
            <div class="text-right card-footer">
                <input type="submit" class="btn btn-lg btn-primary" value="Crear especialidad" />
            </div>
        </form>
    </div>
@stop

@section('js')

@stop