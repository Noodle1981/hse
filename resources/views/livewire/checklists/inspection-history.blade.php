<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-ep-dark">Historial de Inspecciones</h1>
        <a href="{{ route('checklists.index') }}" 
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            ← Volver a Plantillas
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por checklist o usuario..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary">
            </div>
            <div>
                <select wire:model.live="statusFilter" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todos los estados</option>
                    <option value="completed">Completados</option>
                    <option value="draft">Borradores</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de Inspecciones -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($inspections->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-xl">No hay inspecciones registradas</p>
                <p class="text-sm mt-2">Las inspecciones aparecerán aquí cuando se completen checklists</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-ep-dark text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">Checklist</th>
                        <th class="px-6 py-3 text-left">Inspector</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-left">Fecha</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($inspections as $inspection)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-ep-dark">{{ $inspection->checklist->title }}</div>
                                @if($inspection->checklist->category)
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        @if($inspection->checklist->category === 'hys') bg-red-100 text-red-800
                                        @elseif($inspection->checklist->category === 'calidad') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($inspection->checklist->category) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $inspection->user->nombres ?? 'N/A' }} {{ $inspection->user->apellidos ?? '' }}</div>
                                <div class="text-sm text-gray-500">{{ $inspection->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $inspection->status === 'completed' ? 'Completado' : 'Borrador' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($inspection->completed_at)
                                    <div>{{ $inspection->completed_at->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $inspection->completed_at->format('H:i') }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('inspections.show', $inspection) }}"
                                       class="px-3 py-1 bg-ep-primary text-white rounded hover:bg-ep-primary-dark text-sm">
                                        Ver Detalle
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
</div>
