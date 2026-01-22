<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Historial de Visitas</h1>
            <p class="text-gray-500 mt-1">Todas las visitas realizadas por los técnicos</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ep-primary focus:border-ep-primary"
                       placeholder="Sede, empresa o técnico...">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select wire:model.live="filterStatus" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-ep-primary focus:border-ep-primary">
                    <option value="">Todos</option>
                    <option value="in_progress">En Progreso</option>
                    <option value="completed">Completada</option>
                    <option value="signed">Firmada</option>
                </select>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button wire:click="$set('search', ''); $set('filterStatus', '')" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Visits Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Fecha</th>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Técnico</th>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Sede / Cliente</th>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Duración</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Estado</th>
                        <th class="px-4 py-3 whitespace-nowrap text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse ($visits as $visit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800">
                                {{ $visit->visit_date->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">
                                    {{ $visit->start_time?->format('H:i') }} - {{ $visit->end_time?->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-ep-primary text-white flex items-center justify-center text-xs font-bold mr-2">
                                        {{ substr($visit->user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $visit->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $visit->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-800">
                                <span class="font-medium text-ep-dark">{{ $visit->sede->nombre_sede }}</span>
                                <div class="text-xs text-gray-500">{{ $visit->sede->empresa->nombre }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                @if($visit->end_time && $visit->start_time)
                                    {{ $visit->start_time->diffInMinutes($visit->end_time) }} min
                                @else
                                    <span class="text-blue-600">En progreso</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($visit->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        En Progreso
                                    </span>
                                @elseif($visit->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completada
                                    </span>
                                @elseif($visit->status === 'signed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Firmada
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($visit->status === 'in_progress')
                                    <a href="{{ route('gestor.visits.in-progress', $visit) }}" 
                                       class="btn-xs bg-blue-500 hover:bg-blue-600 text-white">
                                        Ver en Vivo
                                    </a>
                                @else
                                    <a href="#" class="btn-xs bg-white border-gray-200 hover:border-gray-300 text-gray-500">
                                        Ver Detalles
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No se encontraron visitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $visits->links() }}
        </div>
    </div>

</div>
