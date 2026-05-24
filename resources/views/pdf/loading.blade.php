<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Procesando Datos' }} - XamaSafe</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #11111D;
            overflow: hidden;
        }
        
        .glow-text-orange {
            text-shadow: 0 0 15px rgba(246, 146, 57, 0.4);
        }

        .loader-hud {
            border-top-color: #F69239;
            animation: spinner 1s linear infinite;
        }

        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes scan {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .animate-scan {
            position: relative;
            overflow: hidden;
        }

        .animate-scan::after {
            content: "";
            position: absolute;
            top: 0;
            width: 40%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(246, 146, 57, 0.4), transparent);
            animation: scan 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="max-w-md w-full relative">
        <!-- Decoración de Fondo Táctica -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-ep-primary/5 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-indigo-500/5 rounded-full blur-[100px]"></div>

        <!-- Módulo Central Glass -->
        <div class="bg-white/5 backdrop-blur-2xl rounded-[2.5rem] border border-white/10 p-8 sm:p-10 shadow-2xl relative z-10 overflow-hidden">
            <!-- Shimmer effect overlay -->
            <div class="absolute inset-0 bg-gradient-to-tr from-ep-primary/5 to-transparent pointer-events-none"></div>

            <div class="text-center relative z-20">
                <!-- Icono HUD -->
                <div class="mb-8 flex justify-center">
                    <div class="relative h-20 w-20 flex items-center justify-center">
                        <div class="loader-hud absolute inset-0 rounded-full border-2 border-white/5 border-t-ep-primary"></div>
                        <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner rotate-3">
                            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <!-- Ping Central -->
                        <div class="absolute -top-1 -right-1">
                            <span class="flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-ep-primary opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-ep-primary"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Títulos Técnicos -->
                <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic mb-3 glow-text-orange">
                    {{ $subtitle ?? 'Ejecutando Proceso' }}
                </h1>
                <h2 class="text-2xl font-black text-white uppercase italic tracking-tight mb-6">
                    {{ $title ?? 'Generando Informe' }}
                </h2>

                <p class="text-[10px] font-bold text-indigo-100/40 uppercase tracking-[0.2em] italic mb-8 leading-relaxed px-4">
                    {{ $description ?? 'Sincronizando nodos de datos y renderizando motor gráfico PDF de alta fidelidad...' }}
                </p>

                <!-- Barra de Progreso HUD -->
                <div class="space-y-4">
                    <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden animate-scan">
                        <div class="h-full bg-ep-primary/20 w-full"></div>
                    </div>
                    
                    <div class="flex justify-between items-center px-1">
                        <div class="flex items-center gap-2">
                             <div class="w-1.5 h-1.5 rounded-full bg-ep-primary animate-pulse"></div>
                             <span class="text-[8px] font-black text-indigo-100/30 uppercase tracking-widest italic">Link-State: Active</span>
                        </div>
                        <span class="text-[8px] font-black text-ep-primary uppercase tracking-widest italic animate-pulse">Procesando...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-10 text-center text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.3em] italic">
            XamaSafe Tactical Engine &copy; {{ date('Y') }}
        </p>
    </div>

    @if(isset($redirectUrl))
    <script>
        // Pequeño delay de 800ms para asegurar que el usuario vea la animación táctica
        setTimeout(() => {
            window.location.href = "{!! $redirectUrl !!}";
        }, 800);
    </script>
    @endif
</body>
</html>
