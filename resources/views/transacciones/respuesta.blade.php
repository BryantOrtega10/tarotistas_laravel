@extends('layouts.app')

@section('title', 'Estado de la transacción')

@section('content')

    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-6">

        <h2 class="text-2xl font-bold mb-6">Estado de la transacción</h2>

        @if (isset($error))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <p class="font-semibold">{{ $error }}</p>
                @if (isset($details))
                    <pre class="text-xs mt-2 overflow-x-auto">{{ $details }}</pre>
                @endif
            </div>
        @else
            {{-- Estado visual --}}
            @php
                $status = $transaction->status;
                $colors = [
                    0 => 'bg-yellow-100 text-yellow-700',
                    1 => 'bg-blue-100 text-blue-700',
                    2 => 'bg-green-100 text-green-700',
                    3 => 'bg-red-100 text-red-700',
                    4 => 'bg-gray-200 text-gray-700',
                    5 => 'bg-red-200 text-red-800',
                ];
            @endphp

            <div class="p-4 rounded mb-6 {{ $colors[$status] }}">
                <p class="font-semibold text-lg">
                    {{ $transaction->txtStatus }}
                </p>
            </div>

            {{-- Info principal --}}
            <div class="space-y-2 mb-6">

                <div class="flex justify-between">
                    <span class="text-gray-500">Referencia</span>
                    <span class="font-medium">{{ $transaction->uuid }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Tokens comprados</span>
                    <span class="font-medium">{{ number_format($transaction->tokens) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Valor</span>
                    <span class="font-bold text-lg">
                        $ {{ number_format($transaction->valor, 0, ',', '.') }} COP
                    </span>
                </div>

            </div>

            {{-- Datos Wompi --}}
            <div class="border-t pt-4 mt-4">

                <h3 class="font-semibold mb-3">Detalle de pago</h3>

                <div class="space-y-2 text-sm">

                    <div class="flex justify-between">
                        <span class="text-gray-500">ID Wompi</span>
                        <span>{{ $wompiData['id'] }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Estado Wompi</span>
                        <span>{{ $wompiData['status'] }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Método de pago</span>
                        <span>
                            {{ $wompiData['payment_method_type'] ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Fecha</span>
                        <span>
                            {{ \Carbon\Carbon::parse($wompiData['created_at'])->format('d/m/Y H:i') }}
                        </span>
                    </div>

                </div>

            </div>

            {{-- Botones --}}
            <div class="mt-6 flex justify-between">

                <a href="/" class="text-gray-600 hover:underline">
                    Volver al inicio
                </a>

                @if ($transaction->status == 2)
                    <span class="text-green-600 font-semibold">
                        &checkmark; Pago aprobado
                    </span>
                @elseif($transaction->status == 3)
                    <span class="text-red-600 font-semibold">
                        &times; Pago rechazado
                    </span>
                @endif

            </div>

        @endif

    </div>

@endsection
