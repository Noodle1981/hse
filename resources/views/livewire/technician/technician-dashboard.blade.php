<div class="min-h-screen bg-gray-50/50 pb-12">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
    
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
    
                    <h1 class="text-lg md:text-xl font-medium text-white tracking-tight">
                        Mi <span class="text-ep-primary italic tracking-tight">Dashboard</span>
                    </h1>
                    @if($myAlerts['overdue_tasks'] > 0)
                        <div class="flex items-center gap-1.5 px-2 py-0.5 bg-red-500/20 border border-red-500/30 rounded-full animate-pulse">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            <span class="text-[10px] font-black text-red-200 uppercase">{{ $myAlerts['overdue_tasks'] }} Alertas</span>
                        </div>
                    @endif
                </div>
    
                <div class="flex items-center space-x-3">
                    @php
                        $user = auth()->user();
                        $canSwitch = $user->rol === \App\Models\User::ROLE_GESTOR || 
                                     $user->rol === \App\Models\User::ROLE_SUPER_ADMIN || 
                                     session('active_role') == \App\Models\User::ROLE_GESTOR;
                    @endphp

                    @if($canSwitch)
                        <a href="{{ route('switch-role', \App\Models\User::ROLE_GESTOR) }}" 
                           class="hidden xs:flex bg-white/10 backdrop-blur-md text-indigo-100 hover:bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all items-center gap-1">
                            Modo Gestor
                        </a>
                    @endif
                    
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
        </header>
    @endsection

    {{-- Main Content - No offset --}}
    <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 relative z-10">
        
        {{-- Dashboard Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ $greeting }}</h2>
                <p class="text-sm text-gray-500 font-medium italic">Resumen de tu actividad y tareas pendientes.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button wire:click="$refresh" class="bg-white p-2.5 rounded-2xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-all text-gray-400 hover:text-ep-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
        </div>

        {{-- Row 1: Personal Operations --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Tareas Pendientes -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-orange-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 text-orange-600 rounded-2xl group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Planificación</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Tareas Pendientes</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['my_tasks']['pending'] }}</span>
                    <span class="text-xs font-bold text-orange-500">{{ $kpis['my_tasks']['overdue'] }} vencidas</span>
                </div>
            </div>

            <!-- Inspecciones Realizadas -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-green-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-2xl group-hover:bg-green-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Inspecciones</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Completadas Mes</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['my_inspections']['this_month'] }}</span>
                    <span class="text-xs font-bold text-green-500">{{ $kpis['my_inspections']['today'] }} hoy</span>
                </div>
            </div>

            <!-- Capacitaciones -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-indigo-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Capacitación</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Participación Mes</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['my_trainings']['attended_this_month'] }}</span>
                    <span class="text-xs font-bold text-indigo-500">{{ $kpis['my_trainings']['upcoming'] }} próximas</span>
                </div>
            </div>

            <!-- Constancias Generadas -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-ep-primary hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 text-ep-primary rounded-2xl group-hover:bg-ep-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Certificación</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1 leading-none">Constancias Visita</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['my_visit_certificates']['this_month'] }}</span>
                    <span class="text-xs font-bold text-orange-500">{{ $kpis['my_visit_certificates']['pending_signature'] }} firmar</span>
                </div>
            </div>
        </div>

        {{-- Row 2: Personal Risk Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Gestión de Hallazgos -->
            <div class="col-span-1 md:col-span-2 bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100 italic">
                <div class="px-6 py-4 bg-red-50/50 border-b border-red-100 flex items-center justify-between">
                    <h3 class="text-xs font-black text-red-900 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Hallazgos Identificados
                    </h3>
                    @if($kpis['my_findings']['critical'] > 0)
                        <span class="px-2 py-1 bg-red-500 text-white text-[10px] font-black rounded-lg animate-pulse uppercase">¡{{ $kpis['my_findings']['critical'] }} Críticos!</span>
                    @endif
                </div>
                <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <span class="block text-2xl font-black text-gray-900">{{ $kpis['my_findings']['total'] }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Totales</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-2xl font-black text-red-600">{{ $kpis['my_findings']['pending'] }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pendientes</span>
                    </div>
                    <div class="text-center">
                        <span class="block text-2xl font-black text-indigo-600">
                            {{ $kpis['my_findings']['total'] > 0 ? round((($kpis['my_findings']['total'] - $kpis['my_findings']['pending']) / $kpis['my_findings']['total']) * 100) : 0 }}%
                        </span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Eficacia</span>
                    </div>
                </div>
            </div>

            <!-- Incidentes Personales -->
            <div class="bg-indigo-900 rounded-3xl shadow-xl p-6 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                <div class="flex items-center justify-between mb-6 relative">
                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-300">Seguridad</span>
                </div>
                <h3 class="text-sm font-bold text-indigo-100 mb-1 leading-none">Incidentes Reportados</h3>
                <div class="flex items-baseline gap-2 mb-4">
                    <span class="text-4xl font-black tracking-tight">{{ $kpis['my_incidents']['total'] }}</span>
                </div>
                <div class="pt-4 border-t border-white/10 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-indigo-300 uppercase">Reporte Mes Actual:</span>
                    <span class="text-lg font-black">{{ $kpis['my_incidents']['this_month'] }}</span>
                </div>
            </div>
        </div>

        {{-- Row 3: Reports & Compliance --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Master Reports Card -->
            <div class="col-span-1 md:col-span-2 bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border border-gray-100 flex items-center gap-6">
                <div class="p-4 bg-orange-50 text-ep-primary rounded-2xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Mis Informes Maestros</h3>
                    <div class="flex items-center gap-4">
                        <div>
                            <span class="block text-2xl font-black text-gray-900">{{ $kpis['my_reports']['total_master'] }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Totales</span>
                        </div>
                        <div class="h-8 w-px bg-gray-100"></div>
                        <div>
                            <span class="block text-2xl font-black text-green-600">{{ $kpis['my_reports']['completed'] }}</span>
                            <span class="text-[10px] font-bold text-gray-500 uppercase">Finalizados</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-500 uppercase">
                        {{ $kpis['my_reports']['pending'] }} Edición
                    </span>
                </div>
            </div>

            <!-- My Performance (Gauge equivalent) -->
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-l-8 border-green-500 flex flex-col justify-center col-span-1 md:col-span-2">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Rendimiento Operativo (7D)</h3>
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-black text-gray-900">
                        {{ count($myTasks) == 0 ? 100 : round(($kpis['my_inspections']['this_month'] / max(1, $kpis['my_tasks']['pending'] + $kpis['my_inspections']['this_month'])) * 100) }}%
                    </span>
                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full" style="width:75%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lower Section: Unified Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            {{-- Left: Tasks List (Col 2) --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                    <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight italic">Próximas Inspecciones</h3>
                        <span class="px-3 py-1 bg-white rounded-full text-[10px] font-black text-ep-primary border border-gray-100 uppercase">
                            {{ count($myTasks) }} Pendientes
                        </span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($myTasks as $task)
                            <div class="p-6 hover:bg-gray-50 transition-colors group">
                                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-ep-primary/10 group-hover:text-ep-primary transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900">{{ $task->checklist->title }}</p>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">{{ $task->sede->nombre_sede }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right hidden md:block">
                                            <p class="text-[10px] font-black text-gray-400 uppercase leading-none mb-1">Fecha Límite</p>
                                            <p class="text-xs font-bold {{ $task->isOverdue() ? 'text-red-500' : 'text-gray-700' }}">
                                                {{ $task->due_date?->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <a href="{{ route('tecnico.checklists.fill', ['id' => $task->dynamic_checklist_id, 'assignment' => $task->id]) }}" 
                                           class="px-4 py-2 bg-ep-dark text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-ep-primary transition-colors shadow-lg shadow-gray-200">
                                            Iniciar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <p class="text-sm text-gray-400 font-bold italic">No hay tareas programadas para hoy.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: Charts (Col 1) --}}
            <div class="lg:col-span-1 space-y-8">
                <!-- Trend Chart -->
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-6 border border-gray-100">
                    <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-6 border-l-4 border-indigo-500 pl-4 italic">Actividad Mensual</h3>
                    <div style="height: 200px;">
                        <canvas id="myInspectionsChart"></canvas>
                    </div>
                </div>

                <!-- Productivity Gauge (Bar Chart) -->
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-6 border border-gray-100">
                    <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest mb-6 border-l-4 border-orange-500 pl-4 italic">Productividad (7D)</h3>
                    <div style="height: 200px;">
                        <canvas id="productivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                bg: '#f8fafc'
            }
        };

        Chart.defaults.font.family = config.font;
        Chart.defaults.color = '#94a3b8';

        // 1. Trend Line
        const trendCtx = document.getElementById('myInspectionsChart');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($charts['myInspectionsPerMonth']['labels']),
                    datasets: [{
                        data: @json($charts['myInspectionsPerMonth']['data']),
                        borderColor: config.colors.info,
                        borderWidth: 4,
                        backgroundColor: 'rgba(99, 102, 241, 0.05)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { display: false },
                        x: { grid: { display: false }, border: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 2. Productivity Bar
        const prodCtx = document.getElementById('productivityChart');
        if (prodCtx) {
            new Chart(prodCtx, {
                type: 'bar',
                data: {
                    labels: @json($charts['myProductivity']['labels']),
                    datasets: [{
                        data: @json($charts['myProductivity']['data']),
                        backgroundColor: config.colors.primary,
                        borderRadius: 12,
                        barThickness: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { display: false },
                        x: { grid: { display: false }, border: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }
</script>
@endpush
