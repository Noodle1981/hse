<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Mi Dashboard</h1>
            <p class="text-gray-600">Hola, {{ auth()->user()->nombres }} 👋</p>
        </div>

        {{-- KPIs Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Mis Tareas --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Mis Tareas Pendientes</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['my_tasks']['pending'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <span class="text-blue-600">{{ $kpis['my_tasks']['in_progress'] }} en progreso</span>
                    @if($kpis['my_tasks']['overdue'] > 0)
                        <span class="text-red-600">{{ $kpis['my_tasks']['overdue'] }} vencidas</span>
                    @endif
                </div>
            </div>

            {{-- Mis Inspecciones --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Inspecciones (Mes)</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['my_inspections']['this_month'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <span class="text-green-600">{{ $kpis['my_inspections']['today'] }} hoy</span>
                    <span class="text-gray-600">{{ $kpis['my_inspections']['total'] }} total</span>
                </div>
            </div>

            {{-- Mis Capacitaciones --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Capacitaciones</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['my_trainings']['attended_this_month'] }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-sm">
                    <span class="text-purple-600">{{ $kpis['my_trainings']['upcoming'] }} próximas</span>
                </div>
            </div>

            {{-- Mis Entregas EPP --}}
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Entregas EPP (Mes)</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $kpis['my_epp_deliveries']['this_month'] }}</p>
                    </div>
                    <div class="bg-orange-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-sm">
                    <span class="text-orange-600">{{ $kpis['my_epp_deliveries']['total'] }} total</span>
                </div>
            </div>


        </div>

        {{-- Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Mi Productividad (7 días) --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mi Productividad (Últimos 7 días)</h3>
                <div style="height: 300px;">
                    <canvas id="productivityChart"></canvas>
                </div>
            </div>

            {{-- Mis Inspecciones por Mes --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Mis Inspecciones por Mes</h3>
                <div style="height: 300px;">
                    <canvas id="myInspectionsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Mis Tareas Pendientes --}}
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Mis Tareas Pendientes</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($myTasks as $task)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $task->checklist->title }}</p>
                                <p class="text-sm text-gray-600">{{ $task->sede->nombre_sede }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm {{ $task->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $task->due_date?->format('d/m/Y') }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $task->status === 'pending' ? 'Pendiente' : 'En Progreso' }}
                                </span>
                                <a href="{{ route('tecnico.checklists.fill', ['id' => $task->dynamic_checklist_id, 'assignment' => $task->id]) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Ejecutar →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">¡Excelente! No tienes tareas pendientes 🎉</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Mis Alertas --}}
        @if($myAlerts['overdue_tasks'] > 0 || $myAlerts['tasks_due_soon'] > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Mis Alertas</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                @if($myAlerts['overdue_tasks'] > 0)
                                    <li>{{ $myAlerts['overdue_tasks'] }} tareas vencidas</li>
                                @endif
                                @if($myAlerts['tasks_due_soon'] > 0)
                                    <li>{{ $myAlerts['tasks_due_soon'] }} tareas vencen en 3 días</li>
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
    // Mi Productividad (7 días)
    new Chart(document.getElementById('productivityChart'), {
        type: 'bar',
        data: {
            labels: @json($charts['myProductivity']['labels']),
            datasets: [{
                label: 'Inspecciones',
                data: @json($charts['myProductivity']['data']),
                backgroundColor: '#3B82F6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
