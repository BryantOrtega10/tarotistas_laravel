@extends('layouts.app')

@section('title', 'Compra Wompi')

@section('content')

    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-6">

        <h2 class="text-2xl font-bold mb-6">Resumen de compra</h2>

        {{-- Paquete --}}
        <div class="flex items-center gap-4 border-b pb-4 mb-4">

            {{-- Imagen --}}
            @if (isset($paquete->imagen) && $paquete->imagen)
                <img src="{{ Storage::url('paquetes/' . $paquete->imagen) }}" alt="{{ $paquete->nombre }}"
                    class="w-20 h-20 object-cover rounded-lg" />
            @else
                Sin imagen
            @endisset

            {{-- Info --}}
            <div class="flex-1">
                <h3 class="text-lg font-semibold">{{ $paquete->nombre }}</h3>
                <p class="text-gray-500">
                    Tokens: {{ $paquete->tokens }}
                </p>
            </div>

            {{-- Precio --}}
            <div class="text-right">
                <p class="font-bold text-lg">
                    $ {{ number_format($paquete->valor, 0, ',', '.') }} {{ $wompiFormData['currency'] }}
                </p>
            </div>
    </div>

    {{-- Cantidad --}}
    <div class="flex justify-between mb-2">
        <span class="text-gray-600">Cantidad</span>
        <span class="font-medium">1</span>
    </div>

    {{-- Subtotal --}}
    <div class="flex justify-between mb-2">
        <span class="text-gray-600">Subtotal</span>
        <span>{{ number_format($paquete->valor, 0, ',', '.') }} {{ $wompiFormData['currency'] }}</span>
    </div>

    {{-- Total --}}
    <div class="flex justify-between border-t pt-4 mt-4">
        <span class="text-lg font-semibold">Total</span>
        <span class="text-xl font-bold text-green-600">
            {{ number_format($paquete->valor, 0, ',', '.') }} {{ $wompiFormData['currency'] }}
        </span>
    </div>

    {{-- Botón --}}
    <div class="mt-6 text-right">
        <form action="https://checkout.wompi.co/p/" method="GET">
            <!-- OBLIGATORIOS -->
            <input type="hidden" name="public-key" value="{{ $wompiFormData['public-key'] }}" />
            <input type="hidden" name="currency" value="{{ $wompiFormData['currency'] }}" />
            <input type="hidden" name="amount-in-cents" value="{{ $wompiFormData['amount-in-cents'] }}" />
            <input type="hidden" name="reference" value="{{ $wompiFormData['reference'] }}" />
            <input type="hidden" name="signature:integrity" value="{{ $wompiFormData['signature'] }}" />
            <!-- OPCIONALES -->
            <input type="hidden" name="redirect-url" value="{{ $wompiFormData['redirect-url'] }}" />
            <input type="hidden" name="customer-data:email" value="{{ $wompiFormData['customer-data']['email'] }}" />
            <input type="hidden" name="customer-data:full-name" value="{{ $wompiFormData['customer-data']['full-name'] }}" />
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Pagar con
                Wompi</button>
        </form>

    </div>

</div>

@endsection
