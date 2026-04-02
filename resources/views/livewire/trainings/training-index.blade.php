<div class="w-full mx-auto">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Centro de <span class="text-white">Entrenamiento</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Gestión de Capacitaciones
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100 italic tracking-tight">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-2 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
            
            {{-- HUB DE COMANDOS: FILTROS Y ACCIÓN MAESTRA --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-4 sm:p-6 lg:p-8 shadow-2xl relative overflow-hidden animate-fade-in-down">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6 lg:gap-8 items-end relative z-10">
                    {{-- BUSCADOR TÁCTICO --}}
                    <div class="md:col-span-5 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Terminal de Escaneo</span>
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   placeholder="BUSCAR ENTRENAMIENTO O INSTRUCTOR..."
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-12 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic uppercase">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-500/30 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- FILTROS DE INTELIGENCIA --}}
                    <div class="md:col-span-4 grid grid-cols-2 gap-4">
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Empresa</span>
                            <select wire:model.live="empresaFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer">
                                <option value="">TODAS</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Sede</span>
                            <select wire:model.live="sedeFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer {{ !$empresaFilter ? 'opacity-20 cursor-not-allowed' : '' }}" {{ !$empresaFilter ? 'disabled' : '' }}>
                                <option value="">TODAS</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ACCIÓN PRIMARIA (FUERA DEL BANNER) --}}
                    <div class="md:col-span-3">
                        <a href="{{ route('gestor.trainings.create') }}" 
                           class="flex h-14 bg-ep-primary text-white hover:brightness-110 active:scale-95 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-3 shadow-[0_0_25px_rgba(246,146,57,0.3)] group/btn relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/10 translate-y-14 group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                            <svg class="w-5 h-5 group-hover/btn:rotate-90 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="relative z-10">Asignar Capacitación</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- MATRIZ DE ENTRENAMIENTO --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Capacitación</th>
                                <th class="hidden xl:table-cell px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Responsable</th>
                                <th class="hidden md:table-cell px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Punto de Despliegue</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Cronograma</th>
                                <th class="hidden sm:table-cell px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Asistencia</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">HUD</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($trainings as $training)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                    <td class="px-4 lg:px-8 py-6">
                                        <div class="flex items-center gap-3 sm:gap-4">
                                            <div class="relative group/icon hidden xs:block">
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl {{ ($training->source ?? 'manual') === 'contract' ? 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400' : 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary' }} border flex items-center justify-center text-xs font-black group-hover:scale-110 transition-transform">
                                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" stroke-width="2"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke-width="2"/></svg>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                                    <div class="text-xs sm:text-sm font-black text-white italic uppercase tracking-tight leading-none lg:group-hover/row:text-indigo-400 transition-colors">
                                                        {{ $training->title }}
                                                    </div>
                                                </div>
                                                <div class="text-[8px] sm:text-[9px] font-black text-white/20 uppercase tracking-widest italic flex items-center gap-2">
                                                    @if($training->registry_code)
                                                        <span class="font-mono text-white/30 hidden sm:inline">[{{ $training->registry_code }}]</span>
                                                    @endif
                                                    <span class="text-white/40">{{ $training->trainer_name ?: 'NO ASIGNADO' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden xl:table-cell px-4 lg:px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            @if($training->responsibleUser)
                                                <div class="w-8 h-8 rounded-xl border {{ ($training->source ?? 'manual') === 'contract' ? 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400' : 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary' }} flex items-center justify-center text-[10px] font-black">
                                                    {{ substr($training->responsibleUser->nombres ?? 'U', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-white/60 italic uppercase leading-none">{{ $training->responsibleUser->nombres }}</div>
                                                    <div class="text-[9px] font-bold text-white/20 italic mt-0.5 whitespace-nowrap">{{ $training->responsibleUser->email }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-4 lg:px-8 py-6">
                                        <div class="text-[10px] font-black text-white italic uppercase tracking-tight leading-none mb-1 lg:group-hover/row:text-ep-primary transition-colors">{{ $training->empresa->nombre ?? 'N/A' }}</div>
                                        <div class="text-[8px] font-bold text-white/20 italic tracking-widest uppercase">{{ $training->sede->nombre_sede ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 lg:px-8 py-6">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2 text-[10px] font-black text-white/60 italic">
                                                <svg class="w-3 h-3 text-indigo-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $training->date->format('d/m/y') }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[8px] font-black {{ ($training->source ?? 'manual') === 'contract' ? 'text-indigo-400' : 'text-emerald-400' }} uppercase italic">{{ $training->duration ?? '60m' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 lg:px-8 py-6 text-center">
                                        @if(isset($training->attendances_count) || isset($training->view_attendances_count))
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                                                {{ $training->attendances_count ?? 0 }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 lg:px-8 py-6 text-center">
                                        @php
                                            $statusBadge = match($training->status) {
                                                'scheduled' => 'text-amber-400 border-amber-500/30 bg-amber-500/10 shadow-[0_0_10px_rgba(245,158,11,0.1)]',
                                                'completed' => 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_10px_rgba(16,185,129,0.1)]',
                                                'cancelled' => 'text-red-400 border-red-500/30 bg-red-500/10 shadow-[0_0_10px_rgba(239,68,68,0.1)]',
                                                default => 'text-white/20 border-white/5 bg-white/5'
                                            };
                                            $statusText = match($training->status) {
                                                'scheduled' => 'PROGRAMADA',
                                                'completed' => 'FINALIZADA',
                                                'cancelled' => 'CANCELADA',
                                                default => strtoupper($training->status)
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-1 rounded-full text-[7px] sm:text-[8px] font-black uppercase tracking-widest italic border {{ $statusBadge }}">
                                            <span class="hidden xs:inline">{{ $statusText }}</span>
                                            <span class="xs:hidden">{{ substr($statusText, 0, 4) }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2 sm:gap-3 lg:translate-x-4 lg:opacity-0 lg:group-hover/row:translate-x-0 lg:group-hover/row:opacity-100 transition-all duration-500">
                                            @if(($training->source ?? 'manual') === 'contract')
                                                <a href="{{ route('gestor.annual-contract.index') }}" class="h-9 w-9 sm:h-10 sm:w-auto sm:px-4 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg text-[9px] font-black uppercase italic tracking-widest">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    <span class="hidden md:inline">Plan Maestro</span>
                                                </a>
                                            @else
                                                <a href="{{ route('gestor.trainings.pdf.preview', $training->id) }}" target="_blank" class="h-9 w-9 sm:h-10 sm:w-10 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Ver Reporte">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </a>
                                                <a href="{{ route('gestor.trainings.edit', $training->id) }}" class="h-9 w-9 sm:h-10 sm:w-10 bg-white/5 border border-white/10 text-white/40 hover:bg-white/20 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">Sin registros de entrenamiento <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inyecte una nueva capacitación para iniciar el seguimiento</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-10 py-6 border-t border-white/5 bg-black/20 pagination-tactical">
                    {{ $trainings->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .pagination-tactical nav div:first-child { display: none; }
        .pagination-tactical nav span, .pagination-tactical nav a {
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.2) !important;
            border-radius: 12px !important; margin: 0 4px !important; padding: 8px 14px !important;
            font-weight: 900 !important; font-size: 10px !important; transition: all 0.3s !important;
        }
        .pagination-tactical nav span[aria-current="page"] {
            border-color: rgba(99,102,241,0.3) !important;
            color: #6366f1 !important;
            background: rgba(99,102,241,0.1) !important;
        }
    </style>
</div>
