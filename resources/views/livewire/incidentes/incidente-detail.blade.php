<div class="min-h-screen bg-[#11111D] font-sans pb-20" x-data="{ openModal: false, modalImage: '', modalDesc: '', showGestion: @entangle('showGestionForm') }">
    {{-- Header de Incidentes / HUD Táctico --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark/80 backdrop-blur-xl flex items-center z-20 border-b border-white/5 shadow-2xl">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.index') : route('tecnico.incidentes.index') }}" 
                       class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 text-indigo-100/60 hover:text-white hover:border-ep-primary/40 transition-all group">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.3em] italic mb-0.5 leading-none">
                            Expediente <span class="text-white">Incidente</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100/80 uppercase tracking-tight leading-none italic">
                            Reporte Técnico #{{ str_pad($incidente->id, 5, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.pdf.preview', $incidente) : route('tecnico.incidentes.pdf.preview', $incidente) }}" 
                       target="_blank"
                       class="hidden sm:flex px-5 py-2.5 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black italic uppercase tracking-widest rounded-xl border border-white/10 transition-all items-center gap-2 shadow-lg group">
                        <svg class="w-4 h-4 text-red-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Exportar PDF
                    </a>

                    @if(auth()->user()->isAdmin())
                        <button @click="showGestion = true" 
                                class="px-5 py-2.5 bg-ep-primary text-white text-[10px] font-black italic uppercase tracking-widest rounded-xl shadow-lg shadow-ep-primary/20 hover:brightness-110 transition-all active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Gestionar
                        </button>
                    @endif
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Banner de Alerta Crítica (Glow) --}}
        @if($incidente->estado === 'pendiente')
            <div class="mb-10 p-1 bg-gradient-to-r from-yellow-500/20 via-yellow-500/40 to-yellow-500/20 rounded-[2.5rem] shadow-[0_0_50px_rgba(234,179,8,0.1)]">
                <div class="bg-[#1a1a2e]/90 backdrop-blur-2xl px-8 py-6 rounded-[2.3rem] border border-white/5 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden group">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-yellow-500/10 rounded-full blur-[100px] group-hover:scale-125 transition-transform duration-1000"></div>
                    
                    <div class="flex items-center gap-6 relative z-10">
                        <div class="h-16 w-16 bg-yellow-500/10 rounded-2xl border border-yellow-500/30 flex items-center justify-center text-yellow-500 shadow-[0_0_20px_rgba(234,179,8,0.2)]">
                            <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white italic uppercase tracking-wider mb-1">Estatus: <span class="text-yellow-500 glow-text-yellow">Acción Requerida</span></h3>
                            <p class="text-indigo-100/60 text-sm font-medium italic">Protocolo incompleto. Se requiere carga de licencia médica o cierre administrativo final.</p>
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <button @click="showGestion = true" 
                                class="px-8 py-3 bg-yellow-500 text-black text-[11px] font-black uppercase italic tracking-[0.2em] rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-xl shadow-yellow-500/20 relative z-10">
                            Ejecutar Gestión
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-fade-in">
            
            {{-- Columna Central: Detalle Técnico --}}
            <div class="lg:col-span-8 space-y-10">
                
                {{-- Bloque: Información General (Glass) --}}
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative group overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner rotate-3 group-hover:rotate-0 transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-white italic uppercase tracking-tight">Registro de Contexto Operativo</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic">Compañía Contratante</span>
                            <p class="text-lg font-bold text-white tracking-tight italic uppercase">{{ $incidente->sede->empresa->nombre ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic">Instalación / Sede</span>
                            <p class="text-lg font-bold text-indigo-100 tracking-tight italic uppercase">{{ $incidente->sede->nombre_sede ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic">Intervalo de Verificación</span>
                            <p class="text-lg font-bold text-white tracking-tight italic uppercase">{{ $incidente->fecha_ocurrencia->format('d \d\e M, Y') }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic">Agente Reportante</span>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-ep-primary/20 rounded-lg flex items-center justify-center text-[10px] font-black text-ep-primary border border-ep-primary/30">
                                    {{ substr($incidente->user->nombres, 0, 1) }}
                                </div>
                                <p class="text-sm font-bold text-indigo-100 tracking-tight italic uppercase">{{ $incidente->user->nombres }} {{ $incidente->user->apellidos }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bloque: Cuerpo del Reporte (Glass) --}}
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-10 border-b border-white/5 pb-6">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-indigo-100/60 shadow-inner -rotate-6 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <h3 class="text-xl font-black text-white italic uppercase tracking-tight">Descripción Técnica del Suceso</h3>
                        </div>

                        <div class="flex gap-3">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border border-white/10 bg-white/5 text-indigo-100/60 shadow-inner italic">
                                {{ strtoupper(str_replace('_', ' ', $incidente->tipo)) }}
                            </span>
                            @php
                                $gravedadStyle = match($incidente->gravedad) {
                                    'fatal' => 'border-red-500/40 text-red-500 glow-text-red bg-red-500/5',
                                    'grave' => 'border-orange-500/40 text-orange-500 bg-orange-500/5',
                                    'moderado' => 'border-yellow-500/40 text-yellow-500 bg-yellow-500/5',
                                    default => 'border-indigo-400/40 text-indigo-400 bg-indigo-400/5'
                                };
                            @endphp
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border transition-all italic {{ $gravedadStyle }}">
                                Gravedad: {{ strtoupper($incidente->gravedad) }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-ep-primary"></span>
                                Crónica de los Hechos
                            </label>
                            <div class="p-6 bg-black/40 rounded-3xl border border-white/5 text-indigo-100/80 leading-relaxed italic font-medium text-sm sm:text-base border-l-4 border-l-indigo-500/30">
                                {{ $incidente->descripcion }}
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]"></span>
                                Acciones de Respuesta / Mitigación
                            </label>
                            <div class="p-6 bg-black/40 rounded-3xl border border-white/5 text-emerald-100/70 leading-relaxed italic font-medium text-sm sm:text-base border-l-4 border-l-emerald-500/30">
                                {{ $incidente->medidas_tomadas ?: 'No se registraron acciones inmediatas adicionales.' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bloque: Evidencia Visual (Glass) --}}
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner rotate-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-white italic uppercase tracking-tight">Archivo Fotográfico Técnico</h3>
                    </div>

                    @if($incidente->fotos->isEmpty())
                        <div class="p-12 text-center bg-black/20 rounded-[2rem] border border-white/5 border-dashed">
                            <p class="text-indigo-100/20 font-bold uppercase tracking-widest italic text-xs">Sin registros fotográficos en este expediente</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($incidente->fotos as $foto)
                                <div class="group relative aspect-[4/3] rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl cursor-pointer"
                                     @click="openModal = true; modalImage = '{{ $foto->getUrl() }}'; modalDesc = '{{ $foto->descripcion ?? 'Evidencia del incidente #' . $incidente->id }}'">
                                    <img src="{{ $foto->getUrl() }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4">
                                        <p class="text-[10px] font-black text-white uppercase italic tracking-wider">{{ $foto->descripcion ?? 'VER EVIDENCIA' }}</p>
                                    </div>
                                    <div class="absolute top-4 right-4 h-8 w-8 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all group-hover:translate-y-0 translate-y-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Columna Lateral: Dash Técnico --}}
            <div class="lg:col-span-4 space-y-8">
                
                {{-- KPI: Magnitud de Daño --}}
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-ep-primary/5 rounded-full blur-3xl"></div>
                    
                    <h4 class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-8 border-b border-white/5 pb-2">Magnitud del Suceso</h4>
                    
                    <div class="space-y-10">
                        <div class="relative">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-[11px] font-black text-indigo-100/60 uppercase italic">Afectados</span>
                                <span class="text-4xl font-black text-ep-primary italic glow-text-orange">{{ $incidente->personas_afectadas }}</span>
                            </div>
                            <div class="h-1.5 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-ep-primary shadow-[0_0_15px_rgba(255,107,0,0.5)]" style="width: {{ min(($incidente->personas_afectadas * 20), 100) }}%"></div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="flex justify-between items-end mb-2 text-yellow-500">
                                <span class="text-[11px] font-black uppercase italic opacity-60">Días Perdidos</span>
                                <span class="text-4xl font-black italic glow-text-yellow">{{ $incidente->dias_perdidos ?? '0' }}</span>
                            </div>
                            <div class="h-1.5 bg-white/5 rounded-full overflow-hidden text-yellow-500">
                                <div class="h-full bg-current shadow-[0_0_15px_rgba(234,179,8,0.5)]" style="width: {{ min(($incidente->dias_perdidos * 10), 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hallazgo Enlazado --}}
                @if($incidente->hallazgo_id)
                    <div class="bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative group">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                        <h4 class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-6">Hallazgo Raíz</h4>
                        
                        <div class="p-5 bg-black/40 rounded-2xl border border-white/5 border-l-4 border-l-indigo-500/40 mb-6">
                            <p class="text-xs font-bold text-indigo-100/60 leading-relaxed italic">
                                "{{ Str::limit($incidente->hallazgo->descripcion, 120) }}"
                            </p>
                        </div>
                        
                        <a href="{{ auth()->user()->isAdmin() ? route('gestor.hallazgos.show', $incidente->hallazgo_id) : route('tecnico.hallazgos.show', $incidente->hallazgo_id) }}" 
                           class="flex items-center justify-center w-full py-4 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 transition-all group/link">
                            Analizar Origen
                            <svg class="w-4 h-4 ml-2 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                @endif
                
                {{-- Quick Actions --}}
                <div class="pt-4 grid grid-cols-2 gap-4">
                    <button @click="window.print()" class="p-4 bg-white/5 rounded-2xl border border-white/10 text-indigo-100/40 hover:text-white transition-all flex flex-col items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="text-[8px] font-black uppercase tracking-widest italic">Imprimir</span>
                    </button>
                    <a href="mailto:?subject=Incidente #{{ $incidente->id }}&body=Enlace: {{ url()->current() }}" class="p-4 bg-white/5 rounded-2xl border border-white/10 text-indigo-100/40 hover:text-white transition-all flex flex-col items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span class="text-[8px] font-black uppercase tracking-widest italic">Compartir</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Overlay Modal: Previsualización de Imagen --}}
    <div x-show="openModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-8 bg-ep-dark/95 backdrop-blur-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;"
         @keydown.escape.window="openModal = false">
        
        <div class="relative max-w-6xl w-full flex flex-col lg:flex-row bg-[#1a1a2e] rounded-[3rem] border border-white/10 shadow-[0_0_100px_rgba(0,0,0,0.8)] overflow-hidden" @click.away="openModal = false">
            <button @click="openModal = false" class="absolute top-6 right-6 z-20 h-10 w-10 flex items-center justify-center bg-black/40 hover:bg-black/60 text-white rounded-full transition-all border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            
            <div class="lg:w-2/3 bg-black flex items-center justify-center p-4 sm:p-8">
                <img :src="modalImage" class="max-w-full max-h-[80vh] object-contain shadow-2xl rounded-2xl">
            </div>
            
            <div class="lg:w-1/3 p-10 flex flex-col justify-between bg-ep-dark/40">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.3em] italic">Descripción de la Evidencia</label>
                        <p class="text-lg font-bold text-white italic tracking-tight" x-text="modalDesc"></p>
                    </div>
                </div>
                
                <div class="pt-10 border-t border-white/5 space-y-4">
                    <a :href="modalImage" download class="flex items-center justify-center w-full py-4 bg-ep-primary text-white text-[11px] font-black italic uppercase tracking-widest rounded-2xl shadow-lg shadow-ep-primary/20 hover:brightness-110 transition-all">
                        Descargar Raw Data
                    </a>
                    <button @click="openModal = false" class="w-full py-4 bg-white/5 text-indigo-100/40 text-[10px] font-black uppercase tracking-widest italic rounded-2xl hover:text-white transition-all">
                        Cerrar Visor
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Overlay Modal: Gestión Administrativa (Admin) --}}
    @if(auth()->user()->isAdmin())
        <div x-show="showGestion" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-ep-dark/80 backdrop-blur-xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             style="display: none;"
             @keydown.escape.window="showGestion = false">
            
            <div class="relative bg-[#1e1e2d] rounded-[3rem] border border-white/10 shadow-[0_0_80px_rgba(0,0,0,0.5)] max-w-xl w-full overflow-hidden animate-fade-in-down" @click.away="showGestion = false">
                <div class="bg-gradient-to-r from-ep-primary/10 via-transparent to-transparent p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-12 w-12 rounded-2xl bg-ep-primary/20 border border-ep-primary/40 flex items-center justify-center text-ep-primary shadow-lg shadow-ep-primary/10 italic font-black text-xl">
                            !
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white italic uppercase tracking-tight">Panel de Cierre</h3>
                            <p class="text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic">Actualización de Protocolos Médicos</p>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="actualizarIncidente" class="space-y-8">
                        <div class="space-y-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1">Estado de la Gestión</label>
                                <select wire:model="nuevoEstado" class="w-full bg-black/40 border-white/10 border rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] cursor-pointer">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="cerrado">Cerrado / Resuelto</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1">Suma Total de Días Perdidos</label>
                                <input type="number" min="0" wire:model="dias_perdidos" 
                                       class="w-full bg-black/40 border-white/10 border rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all">
                                @error('dias_perdidos') <span class="text-red-500 text-[10px] font-black uppercase italic ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1">Notas de Cierre / Detalle de Licencias</label>
                                <textarea wire:model="medidas_tomadas" rows="4" 
                                          class="w-full bg-black/40 border-white/10 border rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5"
                                          placeholder="Ej: Juan Pérez con 5 días (Cert #1234)..."></textarea>
                                @error('medidas_tomadas') <span class="text-red-500 text-[10px] font-black uppercase italic ml-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-8 flex gap-4 border-t border-white/5">
                            <button type="button" @click="showGestion = false" 
                                    class="flex-1 py-4 bg-white/5 hover:bg-white/10 text-indigo-100/40 hover:text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl transition-all border border-white/5">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="flex-[2] py-4 bg-ep-primary text-white text-[10px] font-black italic uppercase tracking-widest rounded-2xl shadow-lg shadow-ep-primary/30 hover:brightness-110 transition-all active:scale-95">
                                Consolidar Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .glow-text-red { text-shadow: 0 0 10px rgba(239, 68, 68, 0.5); }
        .glow-text-orange { text-shadow: 0 0 15px rgba(255, 107, 0, 0.6); }
        .glow-text-yellow { text-shadow: 0 0 15px rgba(234, 179, 8, 0.6); }
        
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }

        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Custom Scrollbar for Dark Mode */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #11111d;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e1e2d;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ep-primary;
        }
    </style>
</div>
