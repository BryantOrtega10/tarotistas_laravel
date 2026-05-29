@extends('layouts.app')

@section('title', 'Términos y Condiciones')

@section('content')

    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-6">

        <h2 class="text-2xl font-bold mb-6">Términos y Condiciones de Uso</h2>
        <p class="text-gray-600 mb-6">Aplicación: <strong>Tarot de Sábila</strong></p>

        {{-- 1 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">1. Aceptación de los términos</h3>
            <p class="text-gray-600 mt-1">
                Al descargar, registrarse o utilizar la aplicación Tarot de Sábila, el usuario acepta estos Términos y
                Condiciones.
                Si el usuario no está de acuerdo, debe abstenerse de utilizar la aplicación.
            </p>
        </div>

        {{-- 2 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">2. Descripción del servicio</h3>
            <p class="text-gray-600 mt-1">
                Tarot de Sábila es una aplicación que ofrece servicios de orientación espiritual y entretenimiento mediante
                consultas de tarot en línea, a través de interacción con tarotistas.
            </p>
        </div>

        {{-- 3 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">3. Registro del usuario</h3>
            <p class="text-gray-600 mt-1">
                Para utilizar la aplicación, el usuario debe registrarse proporcionando:
            </p>
            <ul class="list-disc ml-6 text-gray-600">
                <li>Correo electrónico</li>
                <li>Número de teléfono</li>
            </ul>
            <p class="text-gray-600 mt-1">
                El usuario se compromete a proporcionar información veraz y mantenerla actualizada.
            </p>
        </div>

        {{-- 4 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">4. Uso adecuado de la aplicación</h3>
            <ul class="list-disc ml-6 text-gray-600">
                <li>No utilizar la aplicación para actividades ilegales</li>
                <li>No realizar comportamientos ofensivos, abusivos o irrespetuosos</li>
                <li>No intentar vulnerar la seguridad de la plataforma</li>
            </ul>
            <p class="text-gray-600 mt-1">
                La aplicación podrá suspender cuentas que incumplan estas condiciones.
            </p>
        </div>

        {{-- 5 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">5. Pagos y compras dentro de la app</h3>
            <ul class="list-disc ml-6 text-gray-600">
                <li>Las compras dentro de la aplicación se realizan a través de plataformas externas como Google Play y
                    Apple</li>
                <li>Los pagos son procesados directamente por dichas plataformas</li>
                <li>Tarot de Sábila no almacena información bancaria</li>
                <li>No se realizan reembolsos, salvo que la plataforma de pago lo determine</li>
            </ul>
        </div>

        {{-- 6 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">6. Naturaleza del servicio</h3>
            <ul class="list-disc ml-6 text-gray-600">
                <li>Las consultas de tarot tienen fines de entretenimiento y orientación personal</li>
                <li>No sustituyen asesoramiento profesional (médico, legal, financiero o psicológico)</li>
            </ul>
        </div>

        {{-- 7 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">7. Comunicación dentro de la app</h3>
            <p class="text-gray-600 mt-1">
                La aplicación permite comunicación en tiempo real entre usuarios y tarotistas.
                Tarot de Sábila no graba ni almacena las conversaciones realizadas dentro de la plataforma.
            </p>
        </div>

        {{-- 8 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">8. Responsabilidad</h3>
            <p class="text-gray-600 mt-1">
                Tarot de Sábila no se hace responsable por decisiones tomadas por los usuarios basadas en las consultas
                realizadas dentro de la aplicación.
            </p>
        </div>

        {{-- 9 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">9. Suspensión o cancelación</h3>
            <p class="text-gray-600 mt-1">
                La aplicación se reserva el derecho de suspender o cancelar cuentas que incumplan estos términos sin previo
                aviso.
            </p>
        </div>

        {{-- 10 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">10. Modificaciones</h3>
            <p class="text-gray-600 mt-1">
                Nos reservamos el derecho de modificar estos Términos y Condiciones en cualquier momento.
                Los cambios serán informados a través de la aplicación.
            </p>
        </div>

        {{-- 11 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">11. Legislación aplicable</h3>
            <p class="text-gray-600 mt-1">
                Estos términos se rigen por las leyes aplicables en el país de operación del servicio.
            </p>
        </div>

        {{-- 12 --}}
        <div class="mb-4">
            <h3 class="font-semibold text-lg">12. Contacto</h3>
            <p class="text-gray-600 mt-1">
                Para cualquier consulta, el usuario puede comunicarse al correo:
            </p>
            <p class="mt-2 font-medium text-blue-600">soporte@tarotdesabila.online</p>
        </div>

        {{-- 13 --}}
        <div class="mb-4" id="eliminacion">
            <h3 class="font-semibold text-lg">13. Eliminación de cuentas</h3>
            <p class="text-gray-600 mt-1">
                El usuario puede comunicarse al correo: <span class="font-medium text-blue-600">soporte@tarotdesabila.online</span> para solicitar la eliminación de su cuenta y los datos asociados a la misma

            </p>

        </div>


    </div>

@endsection
