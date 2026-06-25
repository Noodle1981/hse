<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="XamaSafe es la plataforma inteligente para la gestión de higiene y seguridad industrial v2.0. Digitaliza checklists dinámicos aplicables a cualquier política de control, visitas de campo e informes maestros con trazabilidad real.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'XamaSafe') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/ico.ico') }}">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @routes
    @inertiaHead
</head>
<body class="bg-[#11111d] antialiased">
    @inertia
</body>
</html>
