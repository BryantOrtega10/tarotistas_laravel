@extends('adminlte::page')

@section('title', 'Editar pais')

@section('content_header')
    <h1>Editar pais</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('paises.lista') }}">Paises</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar pais</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('paises.modificar',['id' => $pais->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="nombre">Nombre (*):</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" placeholder="Nombre:" value="{{ old('nombre',$pais->nombre) }}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                     <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label>Bandera Actual:</label><br>
                             @if(isset($pais->bandera) && $pais->bandera)
                                {{ Storage::url('paises/' . $pais->bandera) }}
                            @else
                                Sin imagen
                            @endisset
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="bandera">Cambiar Bandera (64px*64px):</label>
                            <input type="file" class="@error('bandera') is-invalid @enderror" id="bandera" name="bandera" accept="image/*">
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
                <input type="submit" class="btn btn-lg btn-primary" value="Editar pais" />
            </div>
        </form>
    </div>
@stop

@section('js')

@stop