<div class="min-h-screen bg-[#11111d] pb-12 text-white">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-[#855533] hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-[10px] font-black text-[#855533] uppercase tracking-[0.4em] italic mb-1 leading-none">
                            Sistema de <span class="text-white">Operaciones</span>
                        </h1>
                        <h2 class="text-sm font-bold text-white/90 uppercase tracking-tight leading-none italic">
                            Centro de Mando
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @php
                        $user = auth()->user();
                        $canSwitch = $user->rol === \App\Models\User::ROLE_GESTOR || 
                                     $user->rol === \App\Models\User::ROLE_SUPER_ADMIN || 
                                     session('active_role') == \App\Models\User::ROLE_GESTOR;
                    @endphp

                    @if($canSwitch)
                        <a href="{{ route('switch-role', \App\Models\User::ROLE_GESTOR) }}" 
                           class="hidden md:flex bg-white/5 hover:bg-white/10 text-[#855533]/60 hover:text-white px-3 py-1.5 rounded-xl text-[8px] font-black border border-white/5 transition-all items-center gap-2 uppercase tracking-widest italic">
                           <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Modo Gestor
                        </a>
                    @endif

                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-white/80 italic tracking-tight">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-[#855533]/30 to-transparent"></div>
        </header>
    @endsection

    <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 pt-10 relative z-10">
        
        {{-- Mission Control Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-ep-primary/10 border border-ep-primary/30 rounded-lg text-ep-primary text-[9px] font-black uppercase tracking-[0.3em] italic">Estado: Operativo</span>
                    <span class="w-12 h-px bg-white/10"></span>
                    <span class="text-indigo-100/30 text-[9px] font-black uppercase tracking-[0.3em] italic">{{ now()->format('d M Y') }} // HQ-CENTRAL</span>
                </div>
                <h2 class="text-4xl font-black text-white tracking-tighter uppercase italic leading-none">
                    CENTRO DE <span class="text-ep-primary">MANDO</span>
                </h2>
            </div>
            
            <div class="flex items-center gap-4">
                <button wire:click="$refresh" class="bg-white/5 p-3 rounded-2xl border border-white/10 hover:bg-ep-primary/20 hover:text-ep-primary text-indigo-100/40 transition-all shadow-inner group">
                    <svg class="w-6 h-6 group-hover:rotate-180 transition-transform duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Row 0: Active Mission (Conditional) --}}
        @if($misionActiva)
            <div class="mb-12 animate-fade-in-up">
                <div class="group relative bg-gradient-to-r from-emerald-600/20 to-transparent border border-emerald-500/30 rounded-[2.5rem] p-1 overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-grid-white/[0.02] bg-[size:20px_20px]"></div>
                    <div class="relative bg-[#11111d]/90 backdrop-blur-xl rounded-[2.4rem] p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-10">
                        <div class="flex items-center gap-8">
                            <div class="relative">
                                <div class="h-20 w-20 bg-emerald-500/10 rounded-3xl border border-emerald-500/20 text-emerald-500 flex items-center justify-center shadow-inner animate-pulse">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="absolute -top-2 -right-2 h-6 w-6 bg-emerald-500 rounded-full border-4 border-[#11111d] animate-ping"></div>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.4em] italic leading-none">MISIÓN EN CURSO</span>
                                    <span class="h-1 w-1 bg-emerald-500/30 rounded-full"></span>
                                    <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none">{{ $misionActiva->start_time->diffForHumans() }}</span>
                                </div>
                                <h3 class="text-2xl font-black text-white uppercase italic tracking-tight mb-2">{{ $misionActiva->sede->nombre_sede }}</h3>
                                <p class="text-[11px] font-bold text-indigo-100/30 uppercase tracking-widest italic">{{ $misionActiva->empresa->nombre }}</p>
                            </div>
                        </div>
                        <a href="{{ route('tecnico.visits.in-progress', $misionActiva->id) }}" 
                           class="w-full md:w-auto px-12 h-16 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-[0.3em] italic rounded-2xl hover:bg-emerald-500 active:scale-95 transition-all shadow-xl shadow-emerald-900/40 flex items-center justify-center gap-4 group">
                            <span>REANUDAR MISIÓN</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Row 1: Tactical Modules --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 mb-12">
            
            {{-- Left: Missions HUB (Col 8) --}}
            <div class="lg:col-span-8 space-y-10">
                
                {{-- Quick Actions Deck --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <a href="{{ route('tecnico.visits.index') }}" class="group p-6 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 hover:border-ep-primary/30 transition-all text-center">
                        <div class="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-ep-primary mx-auto mb-3 shadow-inner group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic group-hover:text-white transition-colors">Nueva Visita</span>
                    </a>
                    <a href="{{ route('tecnico.hallazgos.create') }}" class="group p-6 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 hover:border-ep-primary/30 transition-all text-center">
                        <div class="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-orange-500 mx-auto mb-3 shadow-inner group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic group-hover:text-white transition-colors">Hallazgo</span>
                    </a>
                    <a href="{{ route('tecnico.visits.index') }}" class="group p-6 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 hover:border-emerald-500/30 transition-all text-center">
                        <div class="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-emerald-500 mx-auto mb-3 shadow-inner group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic group-hover:text-white transition-colors">Historial</span>
                    </a>
                    <a href="{{ route('tecnico.master-reports.index') }}" class="group p-6 bg-white/5 border border-white/5 rounded-3xl hover:bg-white/10 hover:border-indigo-500/30 transition-all text-center">
                        <div class="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-indigo-400 mx-auto mb-3 shadow-inner group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic group-hover:text-white transition-colors">Reportes</span>
                    </a>
                </div>

                {{-- PRÓXIMAS MISIONES HUB --}}
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
                    <div class="px-10 py-8 bg-black/20 border-b border-white/5 flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 text-ep-primary shadow-inner">
                                <svg class="w-6 h-6 rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-xl font-black text-white tracking-tighter uppercase italic leading-none mb-1">PRÓXIMAS MISIONES</h3>
                                <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.3em] italic">Asignaciones en curso</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                             @if($proximasMisiones['vencidas']->count() > 0)
                                <span class="px-3 py-1 bg-red-500/10 text-[9px] font-black text-red-500 border border-red-500/20 rounded-lg italic uppercase tracking-widest animate-pulse">{{ $proximasMisiones['vencidas']->count() }} CRÍTICAS</span>
                             @endif
                        </div>
                    </div>
                    
                    <div class="p-4 space-y-4 relative z-10">
                        {{-- Group: Hoy --}}
                        @if($proximasMisiones['hoy']->count() > 0)
                            <div class="mb-4">
                                <h4 class="px-6 py-2 text-[9px] font-black text-ep-primary uppercase tracking-[0.4em] italic mb-4">ASIGNADO PARA HOY</h4>
                                @foreach($proximasMisiones['hoy'] as $task)
                                    @include('livewire.technician.partials.mission-row', ['task' => $task, 'style' => 'today'])
                                @endforeach
                            </div>
                        @endif

                        {{-- Group: Vencidas --}}
                        @if($proximasMisiones['vencidas']->count() > 0)
                            <div class="mb-4">
                                <h4 class="px-6 py-2 text-[9px] font-black text-red-500 uppercase tracking-[0.4em] italic mb-4">MISIONES REZAGADAS</h4>
                                @foreach($proximasMisiones['vencidas'] as $task)
                                    @include('livewire.technician.partials.mission-row', ['task' => $task, 'style' => 'overdue'])
                                @endforeach
                            </div>
                        @endif

                        {{-- Group: Semana --}}
                        @if($proximasMisiones['semana']->count() > 0)
                            <div class="mb-4">
                                <h4 class="px-6 py-2 text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.4em] italic mb-4">PROGRAMADO ESTA SEMANA</h4>
                                @foreach($proximasMisiones['semana'] as $task)
                                    @include('livewire.technician.partials.mission-row', ['task' => $task, 'style' => 'week'])
                                @endforeach
                            </div>
                        @endif

                        @if($proximasMisiones['hoy']->count() == 0 && $proximasMisiones['vencidas']->count() == 0 && $proximasMisiones['semana']->count() == 0)
                            <div class="p-20 text-center bg-black/10 rounded-[2.5rem] border border-dashed border-white/5">
                                <h3 class="text-sm font-black text-indigo-100/10 uppercase tracking-[0.4em] italic mb-2 leading-none">SECTOR DESPEJADO</h3>
                                <p class="text-[10px] text-indigo-100/5 font-bold uppercase tracking-widest italic leading-none">No hay misiones inmediatas detectadas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: Intelligence & Training (Col 4) --}}
            <div class="lg:col-span-4 space-y-10">
                
                {{-- CAPACITACIONES ASIGNADAS --}}
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl relative">
                    <div class="px-8 py-6 bg-black/20 border-b border-white/5 flex items-center gap-4">
                        <div class="h-8 w-8 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center rounded-lg italic font-black">!</div>
                        <h3 class="text-[10px] font-black text-white uppercase tracking-[0.4em] italic leading-none">CAPACITACIONES</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($proximasCapacitaciones as $training)
                            <div class="p-5 bg-white/[0.03] rounded-3xl border border-white/5 hover:bg-white/[0.06] transition-all group">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <h4 class="text-xs font-black text-white uppercase italic tracking-tight mb-1 group-hover:text-indigo-400 transition-colors leading-none">{{ $training->title }}</h4>
                                        <p class="text-[9px] font-bold text-indigo-100/30 uppercase tracking-widest italic leading-none">{{ $training->sede->nombre_sede }}</p>
                                    </div>
                                    <div class="bg-indigo-600/20 px-2 py-1 rounded-lg border border-indigo-500/30">
                                        <span class="text-[8px] font-black text-indigo-400 italic">{{ $training->date->format('d/m') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest italic">{{ $training->duration ?? '30' }} MIN</span>
                                    <a href="{{ route('tecnico.trainings.index') }}" class="text-[9px] font-black text-white uppercase tracking-widest italic hover:text-indigo-400 transition-colors">DETALLES</a>
                                </div>
                            </div>
                        @empty
                            <div class="py-10 text-center">
                                <p class="text-[9px] font-black text-indigo-100/5 uppercase tracking-widest italic">Sin sesiones agendadas</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- STATS COMPACT --}}
                <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 p-8 shadow-2xl space-y-8">
                    <div>
                        <h4 class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.4em] mb-6 italic border-b border-white/5 pb-2">RENDIMIENTO TÁCTICO</h4>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <span class="block text-2xl font-black text-white italic leading-none mb-1">{{ $kpis['my_inspections']['this_month'] }}</span>
                                <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest italic">Protocolos / Mes</span>
                            </div>
                            <div>
                                <span class="block text-2xl font-black text-emerald-400 italic leading-none mb-1">{{ $kpis['my_inspections']['today'] }}</span>
                                <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest italic">Completados Hoy</span>
                            </div>
                        </div>
                    </div>
                    
                    <div style="height: 180px;" class="opacity-60 grayscale hover:grayscale-0 transition-all duration-700">
                        <canvas id="productivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Intelligence Hub (Charts & KPIs) --}}
        <div class="mt-12 pt-12 border-t border-white/5">
             <h3 class="text-[11px] font-black text-indigo-100/20 uppercase tracking-[0.6em] italic mb-10 text-center">SECCIÓN DE INTELIGENCIA Y MÉTRICAS</h3>
             
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                 <!-- Hallazgos & Incidentes -->
                 <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-xl">
                     <div class="flex items-center justify-between mb-8">
                         <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest italic leading-none">Hallazgos Abiertos</span>
                         <span class="text-3xl font-black text-white italic leading-none">{{ $kpis['my_findings']['pending'] }}</span>
                     </div>
                     <div class="h-1 bg-white/5 rounded-full overflow-hidden">
                         <div class="h-full bg-orange-500" style="width: {{ $kpis['my_findings']['total'] > 0 ? ($kpis['my_findings']['pending'] / max(1, $kpis['my_findings']['total'])) * 100 : 0 }}%"></div>
                     </div>
                 </div>

                 <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-xl">
                     <div class="flex items-center justify-between mb-8">
                         <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic leading-none">Incidentes Mes</span>
                         <span class="text-3xl font-black text-white italic leading-none">{{ $kpis['my_incidents']['this_month'] }}</span>
                     </div>
                     <div class="h-1 bg-white/5 rounded-full overflow-hidden">
                         <div class="h-full bg-indigo-400" style="width: 40%"></div>
                     </div>
                 </div>

                 <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-xl">
                     <div class="flex items-center justify-between mb-8">
                         <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic leading-none">Certificaciones</span>
                         <span class="text-3xl font-black text-white italic leading-none">{{ $kpis['my_visit_certificates']['this_month'] }}</span>
                     </div>
                     <div class="h-1 bg-white/5 rounded-full overflow-hidden">
                         <div class="h-full bg-emerald-400" style="width: 100%"></div>
                     </div>
                 </div>
             </div>
        </div>
        
        <div class="mt-20 text-center">
            <p class="text-[9px] font-black text-indigo-100/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Tactical Command Unit • Model-03 • OS-V2.5</p>
        </div>
    </div>

    <style>
        .glow-text-red { text-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
        .glow-emerald-text { text-shadow: 0 0 10px rgba(16, 185, 129, 0.4); }
        .glow-emerald { text-shadow: 0 0 15px rgba(16, 185, 129, 0.6); }
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.6s ease-out forwards;
        }
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
        }
    </style>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:navigated', initDash);
    document.addEventListener('DOMContentLoaded', initDash);

    function initDash() {
        const config = {
            font: "'Inter', sans-serif",
            colors: {
                primary: '#F69239',
                info: '#6366f1',
                success: '#10b981',
                muted: 'rgba(255, 255, 255, 0.05)',
                text: '#FFFFFF'
            }
        };

        Chart.defaults.font.family = config.font;
        Chart.defaults.color = config.text;

        // Productivity Bar Chart
        const prodCtx = document.getElementById('productivityChart');
        if (prodCtx) {
            new Chart(prodCtx, {
                type: 'bar',
                data: {
                    labels: @json($charts['myProductivity']['labels']),
                    datasets: [{
                        data: @json($charts['myProductivity']['data']),
                        backgroundColor: config.colors.primary,
                        borderRadius: 8,
                        barThickness: 12,
                        hoverBackgroundColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { display: false },
                        x: { 
                            grid: { display: false }, 
                            border: { display: false },
                            ticks: { 
                                font: { size: 9, weight: 'bold' },
                                padding: 10
                            }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }
</script>
@endpush

