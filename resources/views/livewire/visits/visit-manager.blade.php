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
                        Mis <span class="text-ep-primary italic">Visitas</span>
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

    <!-- Page Header Actions -->
    <div class="flex justify-end mb-8">
            <button 
                wire:click="openStartVisitModal"
                class="bg-ep-primary text-white rounded-xl px-5 py-2.5 font-bold shadow-lg shadow-ep-primary/30 hover:shadow-ep-primary/50 hover:bg-ep-primary-dark hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 group"
            >
                <div class="bg-white/20 rounded-full p-1 group-hover:bg-white/30 transition-colors">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span>Iniciar Nueva Visita</span>
            </button>
        </div>

    <!-- Active Visit Banner (if logic fails redirect) -->
    <!-- This is handled in mount/boot but just in case -->

    <!-- Past Visits List (Cards on Mobile, Table on Desktop) -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <!-- Desktop Table (visible from md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="bg-ep-dark text-white border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 whitespace-nowrap text-left text-xs font-bold uppercase tracking-wider pl-6">Fecha</th>
                        <th class="px-4 py-4 whitespace-nowrap text-left text-xs font-bold uppercase tracking-wider">Horario</th>
                        <th class="px-4 py-4 whitespace-nowrap text-left text-xs font-bold uppercase tracking-wider">Ubicación</th>
                        <th class="px-4 py-4 whitespace-nowrap text-left text-xs font-bold uppercase tracking-wider">Duración</th>
                        <th class="px-4 py-4 whitespace-nowrap text-center text-xs font-bold uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-4 whitespace-nowrap text-right text-xs font-bold uppercase tracking-wider pr-6">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse ($pastVisits as $visit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap pl-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 font-bold">
                                        {{ $visit->visit_date->format('d') }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 capitalize">{{ ucfirst($visit->visit_date->locale('es')->translatedFormat('F, Y')) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ $visit->start_time?->format('H:i') }} - {{ $visit->end_time?->format('H:i') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900">{{ $visit->sede->nombre_sede }}</span>
                                    <span class="text-xs text-gray-500">{{ $visit->empresa->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($visit->end_time && $visit->start_time)
                                    @php
                                        $duration = $visit->start_time->diffInMinutes($visit->end_time);
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        @if($hours > 0)
                                            <span class="font-medium">{{ $hours }}:{{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }} hrs</span>
                                        @else
                                            <span>{{ $minutes }} min</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $visit->status_badge }}">
                                    {{ $visit->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if($visit->status === 'completed' || $visit->status === 'signed')
                                        <a href="{{ route('tecnico.visits.certificate.preview', $visit->id) }}" 
                                           target="_blank"
                                           class="text-gray-400 hover:text-blue-600 transition-colors p-1" 
                                           title="Ver Constancia PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('tecnico.visits.certificate.download', $visit->id) }}" 
                                           class="text-gray-400 hover:text-green-600 transition-colors p-1" 
                                           title="Descargar Constancia PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Visita no finalizada</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                             <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No has realizado visitas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Cards, visible strictly on < md) -->
        <div class="md:hidden divide-y divide-gray-100">
            @forelse ($pastVisits as $visit)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <!-- Header: Date and Badge -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 font-bold">
                                {{ $visit->visit_date->format('d') }}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-bold text-gray-900 capitalize">{{ ucfirst($visit->visit_date->locale('es')->translatedFormat('F, Y')) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $visit->start_time?->format('H:i') }} - {{ $visit->end_time?->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $visit->status_badge }}">
                            {{ $visit->status_label }}
                        </span>
                    </div>

                    <!-- Body: Location -->
                    <div class="mb-3 pl-13">
                        <div class="text-sm font-semibold text-gray-900">{{ $visit->sede->nombre_sede }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->empresa->nombre }}</div>
                    </div>

                    <!-- Footer: Duration and Actions -->
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-600 flex items-center">
                            @if($visit->end_time && $visit->start_time)
                                @php
                                    $duration = $visit->start_time->diffInMinutes($visit->end_time);
                                    $hours = floor($duration / 60);
                                    $minutes = $duration % 60;
                                @endphp
                                <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @if($hours > 0)
                                    <span>{{ $hours }}:{{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }} hrs</span>
                                @else
                                    <span>{{ $minutes }} min</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($visit->status === 'completed' || $visit->status === 'signed')
                                <a href="{{ route('tecnico.visits.certificate.download', $visit->id) }}" 
                                   class="border border-emerald-100 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs px-3 py-2 rounded-lg flex items-center gap-1.5 transition-colors shadow-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Descargar PDF
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">Inconclusa</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    No has realizado visitas aún.
                </div>
            @endforelse
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
