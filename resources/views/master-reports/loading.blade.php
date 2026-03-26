<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generando Informe Maestro - XamaSafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .loader {
            border-top-color: #f97316;
            -webkit-animation: spinner 1.5s linear infinite;
            animation: spinner 1.5s linear infinite;
        }
        @-webkit-keyframes spinner {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full px-6 text-center">
        <!-- Logo / Brand -->
        <div class="mb-8 flex justify-center">
            <div class="bg-orange-100 p-4 rounded-2xl shadow-inner">
                <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-3xl shadow-xl p-8 border border-slate-100">
            <h1 class="text-2xl font-extrabold text-slate-900 mb-2">Generando Informe</h1>
            <p class="text-slate-500 mb-8 font-medium">
                Estamos procesando los datos de <span class="text-orange-600 font-bold">{{ $report->sede->nombre_sede }}</span> 
                para el período <span class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($report->periodo)->translatedFormat('F Y') }}</span>.
            </p>

            <!-- Spinner -->
            <div class="relative flex justify-center mb-8">
                <div class="loader ease-linear rounded-full border-4 border-t-4 border-slate-100 h-16 w-16"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <span class="flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                    </span>
                </div>
            </div>

            <div class="space-y-3">
                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-orange-500 rounded-full animate-[loading_3s_ease-in-out_infinite]" style="width: 30%"></div>
                </div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Iniciando motor de renderizado PDF...</p>
            </div>
        </div>

        <!-- Footer Footer Footer Footer Footer Footer Footer Footer Footer -->
        <p class="mt-8 text-xs text-slate-400 font-medium">XamaSafe &copy; {{ date('Y') }} - Gestión Inteligente de Seguridad</p>
    </div>

    <script>
        // Pequeño delay de 800ms para asegurar que el usuario vea la animación antes de que el servidor empiece a procesar el PDF pesado
        setTimeout(() => {
            const pdfUrl = "{{ route(auth()->user()->isAdmin() ? 'gestor.master-reports.pdf' : 'tecnico.master-reports.pdf', $report->id) }}";
            window.location.href = pdfUrl;
        }, 800);
    </script>

    <style>
        @keyframes loading {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }
    </style>
</body>
</html>
