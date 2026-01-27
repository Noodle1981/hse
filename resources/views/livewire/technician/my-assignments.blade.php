<div class="p-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Inspecciones Asignadas</h1>
            <p class="text-gray-600 mt-1">Gestiona y completa tus checklists asignados</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Buscar</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Checklist..." 
                       class="w-full rounded-lg border-gray-200 shadow-sm focus:border-ep-primary focus:ring-ep-primary text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Empresa</label>
                <select wire:model.live="selectedEmpresa" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-ep-primary focus:ring-ep-primary text-sm">
                    <option value="">Todas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Sede</label>
                <select wire:model.live="selectedSede" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-ep-primary focus:ring-ep-primary text-sm" {{ !$selectedEmpresa ? 'disabled' : '' }}>
                    <option value="">Todas</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Estado</label>
                <select wire:model.live="statusFilter" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-ep-primary focus:ring-ep-primary text-sm">
                    <option value="">Todos</option>
                    <option value="pending">Pendiente</option>
                    <option value="in_progress">En Progreso</option>
                    <option value="completed">Completado</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de Asignaciones -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500">Checklist</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500">Frecuencia</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500">Vencimiento</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 leading-tight">
                                {{ $assignment->checklist->title }}
                            </div>
                            @if($assignment->checklist->registry_code)
                                <div class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter">
                                    {{ $assignment->checklist->registry_code }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-ep-primary">{{ $assignment->empresa->nombre }}</div>
                            <div class="text-xs text-gray-500">{{ $assignment->sede->nombre_sede }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-600">
                            {{ $assignment->getFrequencyLabel() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-medium {{ $assignment->isOverdue() && $assignment->status !== 'completed' ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'Sin fecha' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusLabel = match($assignment->status) {
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En Progreso',
                                    'completed' => 'Completado',
                                    default => ucfirst($assignment->status)
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase
                                @if($assignment->isOverdue() && $assignment->status !== 'completed') bg-red-100 text-red-800
                                @elseif($assignment->status === 'completed') bg-green-100 text-green-800
                                @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                @if($assignment->isOverdue() && $assignment->status !== 'completed') Vencido @else {{ $statusLabel }} @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @if($assignment->status === 'completed' && $assignment->visitCompletion)
                                    <a href="{{ route('tecnico.visits.checklist.preview', $assignment->visitCompletion->id) }}" 
                                       target="_blank"
                                       class="p-1.5 text-ep-primary bg-ep-primary/10 rounded-lg hover:bg-ep-primary hover:text-white transition-all"
                                       title="Ver PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                @endif

                                @if($assignment->status !== 'completed')
                                    <a href="{{ route('tecnico.checklists.fill', ['id' => $assignment->dynamic_checklist_id, 'assignment' => $assignment->id]) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-ep-primary text-white text-xs font-bold rounded-lg hover:bg-ep-primary-dark transition-colors shadow-sm">
                                        Ejecutar
                                        <svg class="ml-1.5 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900">No hay inspecciones asignadas</h3>
                                <p class="text-xs text-gray-500 mt-1">Prueba ajustando tus filtros para encontrar lo que buscas.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($assignments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
