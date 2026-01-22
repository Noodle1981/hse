<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Mis Visitas</h1>
            <p class="text-gray-500 mt-1">Historial y gestión de visitas técnicas</p>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <button 
                wire:click="openStartVisitModal"
                class="btn bg-ep-primary hover:bg-orange-600 text-white"
            >
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0 mr-2" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                Iniciar Nueva Visita
            </button>
        </div>
    </div>

    <!-- Active Visit Banner (if logic fails redirect) -->
    <!-- This is handled in mount/boot but just in case -->

    <!-- Past Visits Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Fecha</th> // Fixed whitespace
                        <th class="px-4 py-3 whitespace-nowrap text-left">Sede / Cliente</th>
                        <th class="px-4 py-3 whitespace-nowrap text-left">Duración</th>
                        <th class="px-4 py-3 whitespace-nowrap text-center">Estado</th>
                        <th class="px-4 py-3 whitespace-nowrap text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse ($pastVisits as $visit)
                        <tr>
                            <td class="px-4 py-3 text-gray-800">
                                {{ $visit->visit_date->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">
                                    {{ $visit->start_time?->format('H:i') }} - {{ $visit->end_time?->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-800">
                                <span class="font-medium text-ep-dark">{{ $visit->sede->nombre_sede }}</span>
                                <div class="text-xs text-gray-500">{{ $visit->empresa->nombre }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                @if($visit->end_time && $visit->start_time)
                                    {{ $visit->start_time->diffInMinutes($visit->end_time) }} min
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $visit->status_badge }}">
                                    {{ $visit->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="#" class="btn-xs bg-white border-gray-200 hover:border-gray-300 text-gray-500">Ver Detalles</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No has realizado visitas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $pastVisits->links() }}
        </div>
    </div>

    <!-- Start Visit Modal -->
    @if($showStartVisitModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showStartVisitModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Iniciar Nueva Visita
                                </h3>
                                <div class="mt-2 text-sm text-gray-500">
                                    Selecciona la sede o cliente donde realizarás la visita técnica.
                                </div>

                                <div class="mt-4">
                                    <label for="sede" class="block text-sm font-medium text-gray-700">Sede</label>
                                    <select 
                                        id="sede" 
                                        wire:model.live="selectedSedeId"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md"
                                    >
                                        <option value="">Seleccionar Sede...</option>
                                        @foreach($sedes as $sede)
                                            <option value="{{ $sede->id }}">
                                                {{ $sede->empresa->nombre }} - {{ $sede->nombre_sede }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedSedeId') 
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="button" 
                            wire:click="startVisit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-ep-primary text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm"
                            @if(!$selectedSedeId) disabled @endif
                        >
                            Iniciar Visita
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('showStartVisitModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
