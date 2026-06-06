<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ep-primary': '#F69239',
                        'ep-dark': '#1E1E2D',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- 404 Number -->
            <h1 class="text-9xl font-bold text-ep-primary mb-4">404</h1>
            
            <!-- Message -->
            <h2 class="text-3xl font-semibold text-gray-800 mb-4">Página no encontrada</h2>
            <p class="text-gray-600 mb-8">
                Lo sentimos, la página que buscas no existe o ha sido movida.
            </p>
            
            <!-- Icon -->
            <div class="mb-8">
                <svg class="w-48 h-48 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/dashboard" 
                   class="px-6 py-3 bg-ep-primary hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors">
                    Ir al Dashboard
                </a>
                <a href="javascript:history.back()" 
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition-colors">
                    Volver Atrás
                </a>
            </div>
            
            <!-- Footer -->
            <p class="mt-8 text-sm text-gray-500">
                EP - Sistema de Checklists Dinámicos
            </p>
        </div>
    </div>
</body>
</html>
