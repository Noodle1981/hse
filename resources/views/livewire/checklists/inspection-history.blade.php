<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-ep-dark">Historial de Inspecciones</h1>
        <a href="{{ route('checklists.index') }}" 
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            ← Volver a Plantillas
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 mt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Buscador Principal -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Búsqueda rápida</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Buscar por checklist, usuario o sede..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Estado</label>
                <select wire:model.live="statusFilter" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todos los estados</option>
                    <option value="completed">Completados</option>
                    <option value="draft">Borradores</option>
                </select>
            </div>

            <!-- Checklist -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Checklist</label>
                <select wire:model.live="checklistFilter" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todas las plantillas</option>
                    @foreach($checklists as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Empresa -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Empresa</label>
                <select wire:model.live="empresaFilter" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todas las empresas</option>
                    @foreach($empresas as $e)
                        <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sede -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sede</label>
                <select wire:model.live="sedeFilter" 
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ep-primary"
                        {{ !$empresaFilter ? 'disabled' : '' }}>
                    <option value="">Todas las sedes</option>
                    @foreach($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre_sede }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Desde -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Desde</label>
                <input type="date" wire:model.live="fromDate" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ep-primary">
            </div>

            <!-- Fecha Hasta -->
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Hasta</label>
                <input type="date" wire:model.live="toDate" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-ep-primary">
            </div>
        </div>
    </div>

    <!-- Tabla de Inspecciones -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($inspections->isEmpty())
            <div class="p-16 text-center text-gray-500">
                <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">No se encontraron inspecciones</h3>
                <p class="mt-2 text-gray-400">Intenta ajustar los filtros para encontrar lo que buscas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detalles</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Inspector</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($inspections as $inspection)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 leading-tight">{{ $inspection->checklist->title }}</div>
                                    <div class="text-xs text-gray-400 mt-1 uppercase tracking-tighter">ID: #{{ $inspection->id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-ep-primary">{{ $inspection->sede->empresa->nombre ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $inspection->sede->nombre_sede ?? 'Sede no especificada' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-700">{{ $inspection->user->nombres ?? 'Desconocido' }} {{ $inspection->user->apellidos ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide uppercase
                                        {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $inspection->status === 'completed' ? 'Completado' : 'Borrador' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($inspection->completed_at)
                                        <div class="text-sm font-medium text-gray-900">{{ $inspection->completed_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $inspection->completed_at->format('H:i') }} hs</div>
                                    @else
                                        <div class="text-xs text-gray-400 font-bold uppercase italic">En proceso</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('inspections.show', $inspection) }}" 
                                           title="Ver detalle"
                                           class="p-2 text-ep-primary bg-ep-primary/10 rounded-lg hover:bg-ep-primary hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if($inspection->status === 'completed')
                                            <a href="{{ route('inspections.pdf.download', $inspection) }}" 
                                               title="Descargar PDF"
                                               class="p-2 text-red-600 bg-red-100 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-6 4h6m-6-8h6"/>
                                                </svg>
                                            </a>
                                        @endif
                                        
                                        @if($inspection->status === 'draft' || auth()->user()->isAdmin())
                                            <button wire:click="delete({{ $inspection->id }})"
                                                    wire:confirm="¿Estás seguro de eliminar esta inspección (borrador)?"
                                                    title="Eliminar Borrador"
                                                    class="p-2 text-gray-500 bg-gray-100 rounded-lg hover:bg-red-100 hover:text-red-600 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
</div>
