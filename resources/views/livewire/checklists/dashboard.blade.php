<div>
    <!-- KPIs Card Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Checklists -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase">Plantillas</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['total_checklists'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $stats['active_checklists'] }} activas</p>
        </div>

        <!-- Total Inspections -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase">Total Inspecciones</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['total_inspections'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Todas las categorías</p>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase">Este Mes</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['completed_this_month'] }}</h3>
                </div>
                <div class="w-12 h-12 bg-ep-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Inspecciones completadas</p>
        </div>

        <!-- Avg per Day -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase">Promedio Diario</p>
                    <h3 class="text-3xl font-bold text-gray-800">
                        {{ $inspectionsPerDay->isNotEmpty() ? round($inspectionsPerDay->avg('total'), 1) : 0 }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Últimos 30 días</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Inspections by Checklist -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Inspecciones por Checklist</h3>
            <div class="space-y-3">
                @forelse($inspectionsByChecklist as $item)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 truncate">{{ $item['checklist'] }}</span>
                            <span class="font-semibold text-gray-900">{{ $item['total'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-ep-primary h-2 rounded-full" 
                                 style="width: {{ ($item['total'] / $inspectionsByChecklist->max('total')) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-8">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>

        <!-- Activity Chart (simple bar chart) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Actividad (Últimos 30 días)</h3>
            <div class="flex items-end justify-between h-48 space-x-1">
                @php
                    $maxValue = $inspectionsPerDay->max('total') ?: 1;
                @endphp
                @forelse($inspectionsPerDay->take(30) as $day)
                    <div class="flex-1 bg-green-500 rounded-t hover:bg-green-600 transition-colors cursor-pointer group relative"
                         style="height: {{ ($day['total'] / $maxValue) * 100 }}%"
                         title="{{ $day['date'] }}: {{ $day['total'] }} inspecciones">
                        <div class="hidden group-hover:block absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}: {{ $day['total'] }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-center w-full py-8">No hay datos disponibles</p>
                @endforelse
            </div>
            <div class="text-xs text-gray-400 mt-2 text-center">
                Hover para ver detalles
            </div>
        </div>
    </div>

    <!-- Recent Inspections Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Inspecciones Recientes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checklist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inspector</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentInspections as $inspection)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $inspection->checklist->title }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $inspection->user ? $inspection->user->nombres . ' ' . $inspection->user->apellidos : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $inspection->completed_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('inspections.show', $inspection) }}" 
                                   class="text-ep-primary hover:text-ep-primary-dark">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                No hay inspecciones recientes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
