@extends('adminlte::page')
@section('title', 'Editar Tarotista')

@section('content_header')
    <h1>Editar Tarotista</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('tarotistas.lista') }}">Tarotistas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>
@stop

@section('content')
    <form method="post" action="{{route('tarotistas.editar',['id' => $tarotista->id])}}">
        @csrf

        <div class="card card-light">
            <div class="card-header">
                Detalles
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                id="nombre" name="nombre" value="{{ old('nombre', $tarotista->nombre) }}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="horarioInicio">Horario Inicio:</label>
                            <input type="time" class="form-control @error('horarioInicio') is-invalid @enderror"
                                id="horarioInicio" name="horarioInicio" value="{{ old('horarioInicio', $tarotista->txt_horario_inicio) }}">
                            @error('horarioInicio')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="horarioFin">Horario Fin:</label>
                            <input type="time" class="form-control @error('horarioFin') is-invalid @enderror"
                                id="horarioFin" name="horarioFin" value="{{ old('horarioFin', $tarotista->txt_horario_fin) }}">
                            @error('horarioFin')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="anios_exp">Años de experiencia:</label>
                            <input type="text" class="form-control @error('anios_exp') is-invalid @enderror"
                                id="anios_exp" name="anios_exp" value="{{ old('anios_exp', $tarotista->anios_exp) }}">
                            @error('anios_exp')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>                    
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="pais">Pais:</label>
                            <select id="pais" name="pais" disabled
                                class="form-control @error('pais') is-invalid @enderror">
                                <option value=""></option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais->id }}" @if (old('pais',$tarotista->fk_pais) == $pais->id) selected @endif>{{ $pais->nombre }} </option>
                                @endforeach
                            </select>
                            @error('pais')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" readonly
                                id="email" name="email" value="{{ old('email', $tarotista->user->email) }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>    
                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label for="provider">Tipo Registro:</label>
                            <input type="text" class="form-control @error('provider') is-invalid @enderror" readonly
                                id="provider" name="provider" value="{{ old('provider', $tarotista->user->provider) }}">
                            @error('provider')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>    

                    <div class="col-md-12 col-12">
                        <div class="form-group">
                            <label for="descripcion_corta">Descripcion Corta:</label>
                            <textarea class="form-control @error('descripcion_corta') is-invalid @enderror"
                                id="descripcion_corta" name="descripcion_corta">{{ old('descripcion_corta', $tarotista->descripcion_corta) }}</textarea>
                            @error('descripcion_corta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <input type="hidden" id="especialidadesJson" value="{{json_encode($especialidades)}}" />
                <h5 class="d-inline-block text-primary">Especialidades</h5> <button class="btn btn-outline-success d-inline-block agregar-especialidad" type="button">Agregar</button>
                <div class="especialidades-cont">
                    @foreach (old("especialidad",$tarotista->especialidades) as $index => $especialidad_item)
                        <div class="row align-items-end especialidad-item" data-id="{{$index}}">
                            <div class="col-md-6 col-8">
                                <div class="form-group">
                                    <label for="especialidad_{{$index}}" class="lb-especialidad">Especialidad {{$index + 1}}:</label>
                                    <select id="especialidad_{{$index}}" name="especialidad[]"
                                        class="form-control @error("especialidad.".$index) is-invalid @enderror">
                                        @foreach ($especialidades as $especialidad)
                                            <option value="{{ $especialidad->id }}" 
                                                @if (old("especialidad.".$index, isset($tarotista->especialidades[$index]) ? $tarotista->especialidades[$index]->id : null) == $especialidad->id) 
                                                    selected 
                                                @endif>{{ $especialidad->nombre }} </option>
                                        @endforeach
                                    </select>
                                    @error("especialidad.".$index)
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @isset($tarotista->especialidades[$index])
                                        <input type="hidden" name="especialidadIDs[]" value="{{ $tarotista->especialidades[$index]?->pivot->id }}">
                                    @endisset
                                </div>
                            </div>
                            <div class="col-md-6 col-4">
                                <button type="button" class="btn btn-outline-danger quitar-especialidad mb-3" data-id="{{$index}}">Quitar</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                
            </div>
        </div>
        <div class="text-right pb-5">
            <input type="submit" class="btn btn-lg btn-primary" value="Modificar" />
        </div>
    </form>

@stop

@section('js')
    <script src="/js/tarotista/especialidades.js"></script>
@stop
