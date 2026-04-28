@extends('layouts.app')

@section('title', 'Error en la transacción')

@section('content')

<div class="max-w-xl mx-auto bg-white shadow-lg rounded-xl p-6 text-center">

    <div class="mb-4">
        <span class="text-5xl">⚠️</span>
    </div>

    <h2 class="text-2xl font-bold mb-4 text-red-600">
        Ocurrió un error
    </h2>

    <p class="text-gray-600 mb-6">
        {{ $error ?? 'Algo salió mal con la transacción.' }}
    </p>

    <a href="/" 
       class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
        Volver al inicio
    </a>

</div>

@endsection