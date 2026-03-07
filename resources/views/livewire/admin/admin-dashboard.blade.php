<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Administrativo</h1>
            <p class="text-gray-600">Vista ejecutiva del sistema</p>
        </div>

        {{-- KPIs Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Checklists --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Checklists Activos</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['checklists']['total_active'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <span class="text-yellow-600">{{ $kpis['checklists']['pending_assignments'] }} pendientes</span>
                    <span class="text-red-600">{{ $kpis['checklists']['overdue_assignments'] }} vencidas</span>
                </div>
            </div>

            {{-- Inspecciones --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Inspecciones (Mes)</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['inspections']['total_this_month'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <span class="text-green-600">{{ $kpis['inspections']['completed_today'] }} hoy</span>
                    <span class="text-gray-600">{{ $kpis['inspections']['avg_per_day'] }} promedio/día</span>
                </div>
            </div>

            {{-- Capacitaciones --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Capacitaciones</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['trainings']['total_scheduled'] }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <span class="text-purple-600">{{ $kpis['trainings']['attendees_this_month'] }} asistentes</span>
                    <span class="text-gray-600">{{ $kpis['trainings']['upcoming_7_days'] }} próximas</span>
                </div>
            </div>


            {{-- Contrato Anual --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Cumplimiento Anual</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['annual']['compliance_percentage'] }}%</p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <span class="text-red-600">{{ $kpis['annual']['overdue_activities'] }} vencidas</span>
                    <span class="text-gray-600">{{ $kpis['annual']['activities_this_month'] }} este mes</span>
                </div>
            </div>
        </div>

        {{-- Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Inspecciones por Mes --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Inspecciones por Mes</h3>
                <div style="height: 300px;">
                    <canvas id="inspectionsChart"></canvas>
                </div>
            </div>

            {{-- Por Categoría --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Categoría</h3>
                <div style="height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            {{-- Top Checklists --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Checklists</h3>
                <div style="height: 300px;">
                    <canvas id="topChecklistsChart"></canvas>
                </div>
            </div>

            {{-- Cumplimiento Anual --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cumplimiento Anual</h3>
                <div style="height: 300px;">
                    <canvas id="complianceChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Actividades y Asignaciones --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Bitácora de Actividades --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Bitácora de Actividades</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-start gap-4 py-3 border-b border-gray-100 last:border-0">
                                <div class="mt-1 flex-shrink-0">
                                    @if($activity->type === 'visita')
                                        <span class="inline-flex items-center justify-center p-2 bg-blue-100 rounded-lg">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </span>
                                    @elseif($activity->type === 'inspeccion')
                                        <span class="inline-flex items-center justify-center p-2 bg-green-100 rounded-lg">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </span>
                                    @elseif($activity->type === 'capacitacion')
                                        <span class="inline-flex items-center justify-center p-2 bg-purple-100 rounded-lg">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center p-2 bg-orange-100 rounded-lg">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" title="{{ $activity->title }}">
                                        {{ $activity->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                        <span class="font-medium text-gray-700">{{ $activity->empresa }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $activity->user_name }}</span>
                                    </p>
                                </div>
                                <div class="text-xs text-gray-500 whitespace-nowrap">
                                    {{ $activity->date ? \Carbon\Carbon::parse($activity->date)->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-500">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                No hay actividades recientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Actividades Asignadas / Próximas --}}
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Actividades Asignadas / Próximas</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @forelse($upcomingActivities as $upcoming)
                            <div class="flex items-start gap-4 py-3 border-b border-gray-100 last:border-0">
                                <div class="mt-1 flex-shrink-0">
                                    @if($upcoming->type === 'capacitacion')
                                        <span class="inline-flex items-center justify-center p-2 bg-purple-100 rounded-lg" title="Capacitación">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center p-2 bg-yellow-100 rounded-lg" title="Checklist Asignado">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $upcoming->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                        <span class="font-medium text-gray-700">{{ $upcoming->empresa }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $upcoming->user_name }}</span>
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-xs font-medium {{ $upcoming->date && \Carbon\Carbon::parse($upcoming->date)->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ $upcoming->date ? \Carbon\Carbon::parse($upcoming->date)->format('d/m/Y') : 'Sin fecha limite' }}
                                    </span>
                                    @if($upcoming->type !== 'capacitacion' && $upcoming->status === 'in_progress')
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-medium">En curso</span>
                                    @elseif($upcoming->type !== 'capacitacion' && $upcoming->status === 'pending')
                                        <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-[10px] font-medium">Pendiente</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-500">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                No hay actividades pendientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertas --}}
        @if($alerts['overdue_assignments'] > 0 || $alerts['overdue_annual_activities'] > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Alertas del Sistema</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                @if($alerts['overdue_assignments'] > 0)
                                    <li>{{ $alerts['overdue_assignments'] }} asignaciones vencidas</li>
                                @endif
                                @if($alerts['overdue_annual_activities'] > 0)
                                    <li>{{ $alerts['overdue_annual_activities'] }} actividades del contrato vencidas</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Dashboard Charts - Updated: {{ now()->timestamp }}
    // Inspecciones por Mes
    new Chart(document.getElementById('inspectionsChart'), {
        type: 'line',
        data: {
            labels: @json($charts['inspectionsPerMonth']['labels']),
            datasets: [{
                label: 'Inspecciones',
                data: @json($charts['inspectionsPerMonth']['data']),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Por Categoría
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($charts['checklistsByCategory']['labels']),
            datasets: [{
                data: @json($charts['checklistsByCategory']['data']),
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#6366F1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Top Checklists
    new Chart(document.getElementById('topChecklistsChart'), {
        type: 'bar',
        data: {
            labels: @json($charts['topChecklists']['labels']),
            datasets: [{
                label: 'Inspecciones',
                data: @json($charts['topChecklists']['data']),
                backgroundColor: '#3B82F6'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // Cumplimiento Anual
    new Chart(document.getElementById('complianceChart'), {
        type: 'bar',
        data: {
            labels: @json($charts['annualCompliance']['labels']),
            datasets: [
                {
                    label: 'Planificado',
                    data: @json($charts['annualCompliance']['planned']),
                    backgroundColor: '#93C5FD'
                },
                {
                    label: 'Completado',
                    data: @json($charts['annualCompliance']['completed']),
                    backgroundColor: '#3B82F6'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
