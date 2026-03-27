<div class="px-4 sm:px-6 lg:px-8 pt-8 w-full max-w-9xl mx-auto">
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
                        Historial de <span class="text-ep-primary italic">Visitas</span>
                    </h1>
                </div>

                <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                    <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                        {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                    </div>
                    <div class="h-4 w-px bg-white/10 mx-1"></div>
                    <span class="text-[10px] font-bold text-indigo-100">{{ auth()->user()->nombres }}</span>
                </a>
            </div>
        </header>
    @endsection

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
                        <th class="px-4 py-3 whitespace-nowrap text-center">Acciones</th>
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
                                    @php
                                        $mins = $visit->start_time->diffInMinutes($visit->end_time);
                                        $hours = floor($mins / 60);
                                        $remainingMins = $mins % 60;
                                        $isExcessive = $hours >= 8;
                                    @endphp
                                    @if($isExcessive)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 border border-red-200" title="Posible olvido de cierre">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ $hours }}h {{ $remainingMins }}m
                                        </span>
                                    @else
                                        @if($hours > 0)
                                            {{ $hours }}h {{ $remainingMins }}m
                                        @else
                                            {{ $remainingMins }} min
                                        @endif
                                    @endif
                                @else
                                    <span class="text-blue-600">En progreso</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($visit->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        En Progreso
                                    </span>
                                @elseif($visit->status === 'completed' && !$visit->responsible_signature && $visit->signature_skip_reason)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pendiente Firma
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
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                @if($visit->status === 'in_progress')
                                    <a href="{{ route('gestor.visits.in-progress', $visit) }}" 
                                       class="btn-xs bg-blue-500 hover:bg-blue-600 text-white">
                                        Ver en Vivo
                                    </a>
                                @endif

                                @if($visit->status === 'completed' || $visit->status === 'signed')
                                    <a href="{{ route('gestor.visits.certificate.preview', $visit->id) }}" 
                                       target="_blank"
                                       class="btn-xs bg-white border-gray-200 hover:text-blue-600 transition-colors p-1" 
                                       title="Ver Constancia PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('gestor.visits.certificate.download', $visit->id) }}" 
                                       class="ml-1 btn-xs bg-white border-gray-200 hover:text-green-600 transition-colors p-1" 
                                       title="Descargar Constancia PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    @if($visit->status === 'completed' && !$visit->responsible_signature && $visit->signature_skip_reason && $visit->email_responsable)
                                    <button wire:click="resendEmail({{ $visit->id }})" 
                                            wire:loading.attr="disabled"
                                            class="ml-2 btn-xs bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200">
                                        Reenviar Email
                                    </button>
                                    @endif
                                @endif

                                @if(auth()->user()->isAdmin())
                                    <button wire:click="editVisit({{ $visit->id }})" 
                                            class="ml-2 btn-xs bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-200"
                                            title="Editar Tiempos">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                @endif
                                </div>
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

    <!-- Modal Editar Visita (Admin) -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showEditModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Corregir Tiempos de Visita
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Fecha de Visita</label>
                                        <input type="date" wire:model="editVisitDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-ep-primary focus:border-ep-primary sm:text-sm">
                                        @error('editVisitDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Hora Inicio</label>
                                            <input type="time" wire:model="editStartTime" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-ep-primary focus:border-ep-primary sm:text-sm">
                                            @error('editStartTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Hora Fin</label>
                                            <input type="time" wire:model="editEndTime" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-ep-primary focus:border-ep-primary sm:text-sm">
                                            @error('editEndTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 italic">
                                        Use esta herramienta para corregir registros donde el técnico olvidó cerrar la visita a tiempo.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="updateVisit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Guardar Cambios
                        </button>
                        <button type="button" wire:click="$set('showEditModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ep-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
