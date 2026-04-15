@extends('adminlte::page')

@section('title', 'Editar paquete')

@section('content_header')
    <h1>Editar paquete</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('paquetes.lista') }}">Paquetes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar paquete</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="card">
        <form action="{{ route('paquetes.modificar', ['id' => $paquete->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="nombre">Nombre (*):</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre"
                                name="nombre" placeholder="Nombre:" value="{{ old('nombre', $paquete->nombre) }}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label>Imagen Actual:</label><br>
                            @if (isset($paquete->imagen) && $paquete->imagen)
                                <img src="{{ Storage::url('paquetes/' . $paquete->imagen) }}" width="100" />
                            @else
                                Sin imagen
                            @endisset
                    </div>
                </div>
                <div class="col-md-3 col-12">
                    <div class="form-group">
                        <label for="imagen">Cambiar Imagen (300px &times; 300px):</label>
                        <input type="file" class="@error('imagen') is-invalid @enderror" id="imagen"
                            name="imagen" accept="image/*">
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
                        <input type="number" min="1" class="form-control @error('tokens') is-invalid @enderror"
                            id="tokens" name="tokens" placeholder="Tokens:"
                            value="{{ old('tokens', $paquete->tokens) }}">
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
                        <input type="number" min="1" class="form-control @error('valor') is-invalid @enderror"
                            id="valor" name="valor" placeholder="Valor:" value="{{ old('valor', $paquete->valor) }}">
                        @error('valor')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3 col-12">
                    <div class="form-group">
                        <label for="estado">Estado (*):</label>
                        <select class="form-control @error('estado') is-invalid @enderror" id="estado"
                            name="estado">
                            <option value="1" @if (old('estado', $paquete->estado) == '1') selected @endif>Visible</option>
                            <option value="0" @if (old('estado', $paquete->estado) == '0') selected @endif>Oculto</option>
                        </select>
                        @error('estado')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
        <div class="text-right card-footer">
            <input type="submit" class="btn btn-lg btn-primary" value="Editar paquete" />
        </div>
    </form>
</div>
@stop

@section('js')

@stop
