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
                </div>
    
                <div class="flex items-center space-x-3">
                    {{-- Compact KPI HUD --}}
                    <div class="hidden sm:flex gap-2 mr-2">
                        <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-lg px-2 py-1">
                            <p class="text-[7px] font-bold text-gray-400 uppercase leading-none mb-0.5">Insp.</p>
                            <p class="text-xs font-black text-white leading-none">{{ $kpis['my_inspections']['this_month'] }}</p>
                        </div>
                    </div>
    
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

    <div class="relative z-10">
        {{-- Main Content - No offset --}}
        <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            
            {{-- Active Alert / Urgent Action --}}
            @if($myAlerts['active_visit_overdue'] || $myAlerts['overdue_tasks'] > 0)
                <div class="mb-8">
                    @if($myAlerts['active_visit_overdue'])
                        <div class="bg-red-500 rounded-3xl p-6 shadow-xl shadow-red-200 flex flex-col md:flex-row items-center justify-between gap-4 animate-pulse">
                            <div class="flex items-center gap-4 text-white">
                                <div class="p-3 bg-white/20 rounded-2xl">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black uppercase tracking-tight">Visita en curso prolongada</h3>
                                    <p class="opacity-90 font-medium">Llevas más de 30 min en {{ $myAlerts['active_visit_overdue']->sede->nombre_sede }}</p>
                                </div>
                            </div>
                            <a href="{{ route('tecnico.visits.history') }}" class="bg-white text-red-600 px-6 py-3 rounded-2xl font-black text-sm hover:bg-red-50 transition-colors uppercase tracking-widest shadow-lg">
                                Gestionar Visita
                            </a>
                        </div>
                    @elseif($myAlerts['overdue_tasks'] > 0)
                        <div class="bg-orange-500 rounded-3xl p-6 shadow-xl shadow-orange-200 flex items-center gap-4 text-white">
                            <div class="p-3 bg-white/20 rounded-2xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black uppercase tracking-tight">Atención: Tareas Vencidas</h3>
                                <p class="opacity-90 font-medium">Tienes {{ $myAlerts['overdue_tasks'] }} inspecciones fuera de fecha.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Left Side: Tasks (Col 8) --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Mis Tareas <span class="text-ep-primary">Pendientes</span></h3>
                        <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1 rounded-full uppercase">{{ count($myTasks) }} Total</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($myTasks as $task)
                            <div class="group bg-white rounded-[2rem] p-6 shadow-lg shadow-gray-200/50 border border-transparent hover:border-ep-primary/30 transition-all duration-300">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="p-4 bg-gray-50 rounded-2xl group-hover:bg-ep-primary/5 transition-colors">
                                            <svg class="w-6 h-6 text-gray-400 group-hover:text-ep-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-black text-gray-900 leading-tight mb-1 group-hover:text-ep-primary transition-colors">
                                                {{ $task->checklist->title }}
                                            </h4>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-tighter">
                                                    {{ $task->sede->nombre_sede }}
                                                </span>
                                                <span class="text-xs font-medium text-gray-400">&bull; Vence: <span class="{{ $task->isOverdue() ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $task->due_date?->format('d/M') }}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-3">
                                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full 
                                            {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $task->status === 'pending' ? 'Pendiente' : 'En Curso' }}
                                        </span>
                                        <a href="{{ route('tecnico.checklists.fill', ['id' => $task->dynamic_checklist_id, 'assignment' => $task->id]) }}" 
                                           class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-ep-dark text-white hover:bg-ep-primary transition-all shadow-lg hover:shadow-ep-primary/20">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-green-50 rounded-[2rem] p-12 text-center border-2 border-dashed border-green-200">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl shadow-green-100">
                                    <span class="text-4xl">🎉</span>
                                </div>
                                <h4 class="text-xl font-black text-green-900 mb-1">¡Al día!</h4>
                                <p class="text-green-700 font-medium">No tienes tareas pendientes por ahora.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Right Side: Productivity (Col 4) --}}
                <div class="lg:col-span-4 space-y-8">
                    <!-- Productivity Chart Card -->
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-8 border border-gray-100">
                        <h3 class="text-lg font-black text-gray-900 tracking-tight mb-6">Mi <span class="text-indigo-500">Rendimiento</span></h3>
                        <div style="height: 250px;">
                            <canvas id="productivityChart"></canvas>
                        </div>
                        <div class="mt-6 p-4 bg-gray-50 rounded-2xl">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-center">Total Mes Actual</p>
                            <p class="text-3xl font-black text-center text-gray-900">{{ $kpis['my_inspections']['this_month'] }} <span class="text-sm font-bold text-gray-400 uppercase tracking-tighter">Inspecciones</span></p>
                        </div>
                    </div>

                    <!-- History Graph -->
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 p-8 border border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Tendencia Mensual</h3>
                        <div style="height: 200px;">
                            <canvas id="myInspectionsChart"></canvas>
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
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // Mi Productividad (7 días)
    new Chart(document.getElementById('productivityChart'), {
        type: 'bar',
        data: {
            labels: @json($charts['myProductivity']['labels']),
            datasets: [{
                label: 'Inspecciones',
                data: @json($charts['myProductivity']['data']),
                backgroundColor: '#F69239',
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

    // Mis Inspecciones por Mes
    new Chart(document.getElementById('myInspectionsChart'), {
        type: 'line',
        data: {
            labels: @json($charts['myInspectionsPerMonth']['labels']),
            datasets: [{
                label: 'Mis Inspecciones',
                data: @json($charts['myInspectionsPerMonth']['data']),
                borderColor: '#6366f1',
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
</script>
@endpush
