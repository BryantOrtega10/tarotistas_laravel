<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tarot de sabila')</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    {{-- Contenido --}}
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 py-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white text-center py-4 border-t">
        <p class="text-sm text-gray-500">
            © {{ date('Y') }} Tarot de sabila
        </p>
    </footer>

</body>

</html>
