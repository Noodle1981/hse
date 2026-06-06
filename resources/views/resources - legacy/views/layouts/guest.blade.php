<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'XamaSafe' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ep-primary': '#F69239',
                        'ep-primary-dark': '#E07D1E',
                        'ep-dark': '#1E1E2D',
                    }
                }
            }
        }
    </script>
    
    @livewireStyles
</head>
<body class="bg-gray-100">
    {{ $slot }}

    @livewireScripts
</body>
</html>
