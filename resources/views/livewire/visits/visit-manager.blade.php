<div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white">
    <div class="max-w-7xl mx-auto">
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

                    <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.3em] mb-1 italic">
                        Mis <span class="text-white">Visitas</span>
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
    <div class="flex justify-end mb-10">
            <button 
                wire:click="openStartVisitModal"
                class="bg-ep-primary text-white rounded-2xl px-8 py-4 font-black shadow-xl shadow-ep-primary/20 hover:brightness-110 active:scale-95 transition-all flex items-center gap-4 group uppercase tracking-[0.2em] text-[11px] italic"
            >
                <div class="bg-white/20 rounded-xl p-1.5 group-hover:rotate-90 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <span>Protocolo Nueva Visita</span>
            </button>
        </div>

    <!-- Active Visit Banner (if logic fails redirect) -->
    <!-- This is handled in mount/boot but just in case -->

    <!-- Past Visits List (Cards on Mobile, Table on Desktop) -->
    <div class="bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 shadow-2xl overflow-hidden mb-10">
        <div class="px-8 py-6 bg-black/20 border-b border-white/5 flex items-center gap-4">
            <div class="h-10 w-10 rounded-xl bg-ep-primary/10 border border-ep-primary/20 text-ep-primary flex items-center justify-center shadow-inner italic">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="font-black text-white uppercase tracking-[0.2em] text-[11px] italic leading-none">Historial de <span class="text-ep-primary">Operaciones</span></h2>
        </div>

        <!-- Desktop Table (visible from md) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="table-auto w-full text-left">
                <thead class="bg-black/30 text-white border-b border-white/5">
                    <tr class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-100/40 italic">
                        <th class="px-6 py-5 whitespace-nowrap">Registro Temporal</th>
                        <th class="px-4 py-5 whitespace-nowrap">Cronología</th>
                        <th class="px-4 py-5 whitespace-nowrap">Ubicación / Nodo</th>
                        <th class="px-4 py-5 whitespace-nowrap">Escala Operativa</th>
                        <th class="px-4 py-5 whitespace-nowrap text-center">Estado</th>
                        <th class="px-6 py-5 whitespace-nowrap text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($pastVisits as $visit)
                        <tr class="hover:bg-white/[0.04] transition-all duration-300 group/row border-transparent border-l-2 hover:border-ep-primary">
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-xl bg-ep-primary/10 border border-ep-primary/20 text-ep-primary font-black italic shadow-inner">
                                        {{ $visit->visit_date->format('d') }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-[11px] font-black text-white uppercase italic tracking-widest">{{ ucfirst($visit->visit_date->locale('es')->translatedFormat('F, Y')) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-[10px] font-black text-indigo-100/60 uppercase tracking-widest italic">
                                {{ $visit->start_time?->format('H:i') }} - {{ $visit->end_time?->format('H:i') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-white uppercase italic tracking-tight group-hover/row:text-ep-primary transition-colors leading-none">{{ $visit->sede->nombre_sede }}</span>
                                    <span class="text-[9px] font-bold text-indigo-100/20 uppercase tracking-widest mt-1 italic">{{ $visit->empresa->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none">
                                @if($visit->end_time && $visit->start_time)
                                    @php
                                        $duration = $visit->start_time->diffInMinutes($visit->end_time);
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                                        @if($hours > 0)
                                            <span class="text-white">{{ $hours }}:{{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }} hrs</span>
                                        @else
                                            <span class="text-white">{{ $minutes }} min</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-red-500/30">Inconclusa</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <span class="bg-white/5 border border-white/5 px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $visit->status_badge }} italic shadow-lg">
                                    {{ $visit->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-3 text-indigo-100/30">
                                    @if($visit->status === 'completed' || $visit->status === 'signed')
                                        <a href="{{ route('tecnico.visits.certificate.preview', $visit->id) }}" 
                                           target="_blank"
                                           class="hover:text-ep-primary transition-all p-2 bg-white/5 rounded-xl border border-white/5 hover:border-ep-primary/30 active:scale-95" 
                                           title="Ver Constancia PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('tecnico.visits.certificate.download', $visit->id) }}" 
                                           class="hover:text-emerald-500 transition-all p-2 bg-white/5 rounded-xl border border-white/5 hover:border-emerald-500/30 active:scale-95" 
                                           title="Descargar Constancia PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                    @else
                                        <span class="text-[9px] font-black uppercase tracking-widest text-red-500/40 italic">Inconclusa</span>
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

        <!-- Mobile View (Cards) -->
        <div class="md:hidden divide-y divide-white/5">
            @forelse ($pastVisits as $visit)
                <div class="p-8 hover:bg-white/[0.04] transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-xl bg-ep-primary/10 border border-ep-primary/20 text-ep-primary font-black italic shadow-inner">
                                {{ $visit->visit_date->format('d') }}
                            </div>
                            <div class="ml-4">
                                <div class="text-[11px] font-black text-white uppercase italic tracking-widest leading-none">{{ ucfirst($visit->visit_date->locale('es')->translatedFormat('F, Y')) }}</div>
                                <div class="text-[9px] font-bold text-indigo-100/20 uppercase tracking-[0.2em] mt-2 italic">
                                    {{ $visit->start_time?->format('H:i') }} - {{ $visit->end_time?->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        <span class="bg-white/5 border border-white/5 px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest {{ $visit->status_badge }} italic">
                            {{ $visit->status_label }}
                        </span>
                    </div>

                    <div class="mb-6 pl-1 pr-2">
                        <div class="text-xs font-black text-white uppercase italic tracking-tighter group-hover:text-ep-primary transition-colors leading-tight">{{ $visit->sede->nombre_sede }}</div>
                        <div class="text-[9px] font-bold text-indigo-100/20 uppercase tracking-[0.2em] mt-2 italic">{{ $visit->empresa->nombre }}</div>
                    </div>

                    <div class="flex justify-between items-center pt-6 border-t border-white/5">
                        <div class="text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic flex items-center gap-2">
                            @if($visit->end_time && $visit->start_time)
                                @php
                                    $duration = $visit->start_time->diffInMinutes($visit->end_time);
                                    $hours = floor($duration / 60);
                                    $minutes = $duration % 60;
                                @endphp
                                <svg class="w-3.5 h-3.5 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                                <span class="text-white">{{ $hours > 0 ? $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ' hrs' : $minutes . ' min' }}</span>
                            @else
                                <span class="text-red-500/30">Inconclusa</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if($visit->status === 'completed' || $visit->status === 'signed')
                                <a href="{{ route('tecnico.visits.certificate.download', $visit->id) }}" 
                                   class="bg-ep-primary/10 border border-ep-primary/20 text-ep-primary hover:bg-ep-primary hover:text-white font-black text-[9px] px-4 py-2 rounded-xl flex items-center gap-2 transition-all uppercase tracking-widest italic shadow-lg"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-indigo-100/10">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] italic mb-1 group-hover:text-white transition-colors">Sistema Limpio</p>
                    <p class="text-[8px] font-bold uppercase tracking-[0.2em] italic">Sin registros registrados</p>
                </div>
            @endforelse
        </div>
        
        <div class="px-8 py-5 border-t border-white/5 bg-black/10">
            {{ $pastVisits->links() }}
        </div>
    </div>

    <style>
        /* Custom Premium Orange Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #11111D;
        }
        ::-webkit-scrollbar-thumb {
            background: #F69239;
            border-radius: 20px;
            border: 2px solid #11111D;
            box-shadow: 0 0 10px rgba(246, 146, 57, 0.2);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #FF9F4D;
        }
        
        * {
            scrollbar-width: thin;
            scrollbar-color: #F69239 #11111D;
        }

        /* Override Pagination Styles for Dark Theme */
        .pagination { @apply flex gap-2; }
        .page-item .page-link { 
            @apply bg-white/5 border border-white/10 text-indigo-100/40 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase italic transition-all;
        }
        .page-item.active .page-link {
            @apply bg-ep-primary border-ep-primary text-white shadow-lg shadow-ep-primary/20;
        }
        .page-item .page-link:hover:not(.disabled) {
            @apply bg-white/10 text-white border-white/20;
        }
    </style>

    <!-- Start Visit Modal Premium -->
    @if($showStartVisitModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" 
                     aria-hidden="true" 
                     wire:click="$set('showStartVisitModal', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Content --}}
                <div class="inline-block align-bottom bg-ep-dark text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full rounded-3xl border border-white/10">
                    <div class="px-4 pt-5 pb-4 sm:p-8 sm:pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-2xl bg-white/5 border border-white/10 sm:mx-0 sm:h-12 sm:w-12 shadow-inner">
                                <svg class="h-6 w-6 text-ep-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="mt-4 text-center sm:mt-0 sm:ml-6 sm:text-left w-full">
                                <h3 class="text-xl font-black text-white tracking-tight italic uppercase">
                                    Iniciar Nueva <span class="text-ep-primary">Visita</span>
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-indigo-100/60 italic leading-relaxed">
                                        Selecciona la sede o cliente donde realizarás la visita técnica interactiva.
                                    </p>
                                </div>

                                <div class="mt-6">
                                    <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2">Seleccionar Sede</label>
                                    <div class="relative group">
                                        <select 
                                            wire:model.live="selectedSedeId"
                                            class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all appearance-none cursor-pointer group-hover:bg-white/10"
                                        >
                                            <option value="" class="bg-ep-dark text-gray-400">Seleccionar Sede...</option>
                                            @foreach($sedes as $sede)
                                                <option value="{{ $sede->id }}" class="bg-ep-dark text-white">
                                                    {{ $sede->empresa->nombre }} - {{ $sede->nombre_sede }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-ep-primary">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </div>
                                    </div>
                                    @error('selectedSedeId') 
                                        <span class="text-red-400 text-xs mt-2 block font-medium italic">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Footer --}}
                    <div class="bg-black/20 px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-white/10">
                        <button 
                            type="button" 
                            wire:click="startVisit"
                            class="w-full inline-flex justify-center rounded-xl px-6 py-2.5 bg-ep-primary text-white text-[10px] font-black shadow-lg shadow-ep-primary/20 hover:brightness-110 focus:outline-none transition-all sm:w-auto uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed disabled:grayscale"
                            @if(!$selectedSedeId) disabled @endif
                        >
                            Iniciar Visita
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('showStartVisitModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-xl bg-white/5 px-6 py-2.5 text-white text-[10px] font-black border border-white/10 hover:bg-white/10 focus:outline-none transition-all sm:mt-0 sm:w-auto uppercase tracking-widest"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
