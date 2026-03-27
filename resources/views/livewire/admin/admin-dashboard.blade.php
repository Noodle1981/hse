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
                        Dashboard <span class="text-ep-primary italic">Administrativo</span>
                    </h1>
                </div>
    
                <div class="flex items-center space-x-3">
                    @if(auth()->user()->rol === \App\Models\User::ROLE_GESTOR || auth()->user()->rol === \App\Models\User::ROLE_SUPER_ADMIN)
                        <a href="{{ route('switch-role', \App\Models\User::ROLE_TECNICO) }}" 
                           class="hidden xs:flex bg-white/10 backdrop-blur-md text-indigo-100 hover:bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all items-center gap-1">
                            Modo Técnico
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
    <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10">
        {{-- KPI Cards - Premium Glassmorphism --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Inspecciones -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-green-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-2xl group-hover:bg-green-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Mensual</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1">Inspecciones</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['inspections']['total_this_month'] }}</span>
                    <span class="text-xs font-bold text-green-500">+{{ $kpis['inspections']['completed_today'] }} hoy</span>
                </div>
            </div>

            <!-- Checklists Pendientes -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-orange-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 text-orange-600 rounded-2xl group-hover:bg-orange-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-red-500 animate-pulse uppercase tracking-wider">¡Urgente!</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1">Checklists Vencidos</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight text-orange-600">{{ $kpis['checklists']['overdue_assignments'] }}</span>
                    <span class="text-xs font-bold text-orange-400">{{ $kpis['checklists']['pending_assignments'] }} pendientes</span>
                </div>
            </div>

            <!-- Capacitaciones -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-indigo-500 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Próximos</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1">Capacitaciones</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['trainings']['total_scheduled'] }}</span>
                    <span class="text-xs font-bold text-indigo-500">{{ $kpis['trainings']['upcoming_7_days'] }} esta semana</span>
                </div>
            </div>

            <!-- Constancias -->
            <div class="group bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border-b-4 border-ep-primary hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 text-ep-primary rounded-2xl group-hover:bg-ep-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Visitas</span>
                </div>
                <h3 class="text-sm font-bold text-gray-500 mb-1">Constancias de Visita</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-gray-900 tracking-tight">{{ $kpis['visit_certificates']['completed_this_month'] }}</span>
                    <span class="text-xs font-bold text-orange-500">{{ $kpis['visit_certificates']['pending_signature'] }} por firmar</span>
                </div>
            </div>
        </div>

        {{-- Alerts Section if any --}}
        @if($alerts['overdue_assignments'] > 0 || $alerts['overdue_annual_activities'] > 0)
            <div class="mb-10 animate-fade-in">
                <div class="bg-red-50 border-l-8 border-red-500 p-6 rounded-3xl shadow-lg flex items-center gap-6">
                    <div class="bg-red-500 p-4 rounded-2xl text-white shadow-lg shadow-red-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-red-900 uppercase tracking-widest">Alertas de Alta Prioridad</h3>
                        <p class="text-red-700 font-medium">Hay elementos que requieren tu atención inmediata para mantener el cumplimiento.</p>
                        <div class="mt-2 flex gap-4">
                            @if($alerts['overdue_assignments'] > 0)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">{{ $alerts['overdue_assignments'] }} Asignaciones Vencidas</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Layout Grid for Charts and Lists --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Left Column: Charts and Stats (Col 8) --}}
            <div class="lg:col-span-8 space-y-8">
                <!-- Main Trend Chart -->
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-8 border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight">Evolución de Inspecciones</h3>
                            <p class="text-sm text-gray-500 font-medium">Cumplimiento operativo mes a mes ({{ now()->year }})</p>
                        </div>
                        <div class="flex items-center gap-2">
                             <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-full">
                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                <span class="text-[10px] font-bold text-green-700 uppercase">Inspecciones</span>
                             </div>
                        </div>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="inspectionsChart"></canvas>
                    </div>
                </div>

                <!-- Secondary Charts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6 px-2 border-l-4 border-indigo-500 pl-4">Por Categoría</h3>
                        <div style="height: 250px;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-6 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6 px-2 border-l-4 border-orange-500 pl-4">Top Actividad</h3>
                        <div style="height: 250px;">
                            <canvas id="topChecklistsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Timeline and Upcoming (Col 4) --}}
            <div class="lg:col-span-4 space-y-8">
                <!-- Activity Timeline -->
                <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 flex flex-col h-full border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Actividad <span class="text-ep-primary">Reciente</span></h3>
                        <span class="p-2 bg-white rounded-xl shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[1000px] scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
                        <div class="relative ml-4 border-l-2 border-gray-100 space-y-8 pb-4">
                            @forelse($recentActivity as $activity)
                                <div class="relative pl-8">
                                    {{-- Time Dot --}}
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm
                                        {{ $activity->type === 'visita' ? 'bg-blue-500' : 
                                           ($activity->type === 'inspeccion' ? 'bg-green-500' : 
                                           ($activity->type === 'capacitacion' ? 'bg-indigo-500' : 'bg-orange-500')) }}">
                                    </div>
                                    
                                    <div class="flex flex-col">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                                {{ $activity->type }}
                                            </p>
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                                {{ $activity->date ? \Carbon\Carbon::parse($activity->date)->format('d/m') : '-' }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-bold text-gray-800 leading-snug mb-1 group-hover:text-ep-primary transition-colors">
                                            {{ $activity->title }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-50 px-2 py-0.5 rounded border border-gray-100 truncate max-w-[120px]">
                                                {{ $activity->empresa }}
                                            </span>
                                            <span class="text-[10px] font-medium text-gray-400 truncate">
                                                por {{ $activity->user_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-sm text-gray-400 font-medium">Sin actividad reciente</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Configuración Global de Charts para Estética Premium
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b';
    
    // Inspecciones por Mes
    new Chart(document.getElementById('inspectionsChart'), {
        type: 'line',
        data: {
            labels: @json($charts['inspectionsPerMonth']['labels']),
            datasets: [{
                label: 'Inspecciones',
                data: @json($charts['inspectionsPerMonth']['data']),
                borderColor: '#10B981',
                borderWidth: 4,
                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10B981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            scales: {
                y: { grid: { borderDash: [5, 5], color: '#f1f5f9' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            },
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1e2d',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    cornerRadius: 12,
                    displayColors: false
                }
            }
        }
    });

    // Por Categoría
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($charts['checklistsByCategory']['labels']),
            datasets: [{
                data: @json($charts['checklistsByCategory']['data']),
                backgroundColor: ['#292938', '#F69239', '#4F46E5', '#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 20, font: { weight: 'bold', size: 11 } }
                }
            }
        }
    });

    // Top Checklists
    new Chart(document.getElementById('topChecklistsChart'), {
        type: 'bar',
        data: {
            labels: @json($charts['topChecklists']['labels']),
            datasets: [{
                data: @json($charts['topChecklists']['data']),
                backgroundColor: '#F69239',
                borderRadius: 12,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { display: false },
                y: { grid: { display: false }, border: { display: false }, ticks: { font: { weight: 'bold' } } }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
