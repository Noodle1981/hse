<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- 403 Number -->
            <h1 class="text-9xl font-bold text-red-500 mb-4">403</h1>
            
            <!-- Message -->
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">Acceso Denegado</h2>
            <p class="text-gray-600 mb-8">
                {{ $exception->getMessage() ?: 'No tienes permiso para acceder a esta página.' }}
            </p>
            
            <!-- Icon -->
            <div class="mb-8">
                <svg class="w-48 h-48 mx-auto text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            
            <!-- Button -->
            <a href="/dashboard" 
               class="inline-block px-6 py-3 bg-ep-primary hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors">
                Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>
