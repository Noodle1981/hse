<div class="w-full mx-auto">
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

                    <div class="flex flex-col">
                        <h1 class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic leading-none mb-1">
                            Escuadrón <span class="text-white">Operativo</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Control de Unidades Tácticas
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100 italic">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
            
            {{-- BUSCADOR TÁCTICO --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>
                
                <div class="relative z-10">
                    <div class="relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Frecuencia de Escaneo</span>
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               class="w-full bg-black/40 border border-white/10 rounded-2xl px-12 py-5 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic uppercase"
                               placeholder="BUSCAR TÉCNICO, DOCUMENTO O CANAL DE ENLACE...">
                        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-indigo-500/50 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MATRIZ DE DESPLIEGUE --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                
                <div class="overflow-x-auto custom-scrollbar-horizontal">
                    <table class="w-full border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-4 lg:px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Unidad Táctica / Perfil</th>
                                <th class="px-4 lg:px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Canal de Enlace</th>
                                <th class="px-4 lg:px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Identificación de Campo</th>
                                <th class="px-4 lg:px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Carga de Sedes</th>
                                <th class="px-4 lg:px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($technicians as $tech)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                    <td class="px-4 lg:px-6 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xs font-black group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                                                {{ substr($tech->nombres, 0, 1) }}{{ substr($tech->apellidos, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-indigo-400 transition-colors">
                                                    {{ $tech->nombres }} {{ $tech->apellidos }}
                                                </div>
                                                <div class="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1.5 italic">
                                                    {{ $tech->titulo ?: 'OPERADOR TÉCNICO' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-6">
                                        <div class="text-[11px] font-bold text-white/40 italic mb-1 group-hover/row:text-white/60 transition-colors uppercase leading-none">{{ $tech->email }}</div>
                                        <div class="text-[10px] font-black text-indigo-400/40 italic leading-none">{{ $tech->telefono }}</div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-6">
                                        <div class="text-[11px] font-black text-white/80 italic uppercase tracking-widest mb-1">{{ $tech->matricula ?: 'SIN MATRÍCULA' }}</div>
                                        <div class="text-[9px] font-bold text-white/20 italic tracking-widest uppercase">DOC: {{ $tech->n_doc ?: '---' }}</div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-6 text-center">
                                        @php $sedeCount = $tech->sedes()->count(); @endphp
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest italic border {{ $sedeCount > 0 ? 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'text-white/10 border-white/5 bg-white/5' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            {{ $sedeCount }} Sedes
                                        </span>
                                    </td>
                                    <td class="px-4 lg:px-6 py-6 text-right">
                                        <button wire:click="openAssignModal({{ $tech->id }})" 
                                                class="h-10 px-5 bg-indigo-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic flex items-center gap-2 ml-auto shadow-lg shadow-indigo-500/20 hover:brightness-110 active:scale-95 transition-all group/btn relative overflow-hidden">
                                            <div class="absolute inset-0 bg-white/20 -translate-x-12 group-hover/btn:translate-x-0 transition-transform"></div>
                                            <svg class="w-3.5 h-3.5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                <path d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"/>
                                            </svg>
                                            <span class="relative z-10">Desplegar</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">No se detectan unidades tácticas <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inicie el registro de técnicos para habilitar el despliegue</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-6 border-t border-white/5 bg-black/20 pagination-tactical">
                    {{ $technicians->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE ASIGNACIÓN (TERMINAL DE DESPLIEGUE) --}}
    @if($showAssignModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
        <div class="absolute inset-0 bg-black/85 backdrop-blur-md" wire:click="closeAssignModal"></div>
        
        <div class="bg-ep-dark border border-white/10 rounded-[3rem] max-w-5xl w-full relative z-10 shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent"></div>
            
            <div class="p-10">
                <div class="flex items-center justify-between mb-10 pb-6 border-b border-white/5">
                    <div>
                        <h3 class="text-2xl font-black text-white italic tracking-tight uppercase">Terminal de Asignación</h3>
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic mt-1 leading-none">Unidad: <span class="text-white">{{ $selectedTechnician->nombres }} {{ $selectedTechnician->apellidos }}</span></p>
                    </div>
                    <button wire:click="closeAssignModal" class="w-10 h-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Messages -->
                @if (session()->has('message'))
                    <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 p-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3"/></svg>
                        <p class="text-[10px] font-black uppercase italic">{{ session('message') }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    
                    <!-- Formulario de Inyección -->
                    <div class="bg-black/30 p-8 rounded-[2.5rem] border border-white/5 relative group">
                        <div class="absolute -top-3 left-8 bg-indigo-500 px-3 py-1 rounded-full text-[8px] font-black text-white uppercase tracking-widest italic shadow-lg shadow-indigo-500/20">Nueva Orden</div>
                        
                        <div class="space-y-8 mt-4">
                            <div>
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2 mb-2 block italic">Sector Corporativo</label>
                                <select wire:model.live="selectedCompanyId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer">
                                    <option value="">SELECCIONE EMPRESA...</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('selectedCompanyId') <span class="text-red-500 text-[10px] font-black italic ml-2 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2 mb-2 block italic">Punto de Despliegue (Sede)</label>
                                <select wire:model.live="selectedSedeId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer {{ empty($sedes) ? 'opacity-20 translate-y-2' : '' }} transition-all" {{ empty($sedes) ? 'disabled' : '' }}>
                                    <option value="">SELECCIONE SEDE...</option>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }} ({{ $sede->localidad ?: 'CENTRO' }})</option>
                                    @endforeach
                                </select>
                                @error('selectedSedeId') <span class="text-red-500 text-[10px] font-black italic ml-2 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <button wire:click="assignSede" 
                                    class="w-full py-5 bg-emerald-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-lg shadow-emerald-600/20 hover:brightness-110 active:scale-95 disabled:opacity-20 flex items-center justify-center gap-3 group/btn2"
                                    {{ empty($selectedSedeId) ? 'disabled' : '' }}>
                                <svg class="w-5 h-5 group-hover/btn2:scale-125 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path d="M12 4v16m8-8H4"/>
                                </svg>
                                Confirmar Despliegue
                            </button>
                        </div>
                    </div>

                    <!-- Lista de Operaciones -->
                    <div class="flex flex-col h-full">
                        <h4 class="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] italic mb-6 ml-2">Asignaciones de Campo</h4>
                        
                        <div class="bg-black/20 border border-white/5 rounded-[2.5rem] overflow-y-auto max-h-[350px] p-2 space-y-2 custom-scrollbar">
                            @if($currentAssignments && $currentAssignments->count() > 0)
                                @foreach($currentAssignments as $assignment)
                                    <div class="group/item flex items-center justify-between p-5 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all duration-300">
                                        <div>
                                            <div class="text-[11px] font-black text-white italic uppercase tracking-tight group-hover/item:text-ep-primary transition-colors leading-none mb-1">{{ $assignment->empresa->nombre }}</div>
                                            <div class="text-[9px] font-bold text-white/30 italic uppercase leading-none">{{ $assignment->nombre_sede }} ({{ $assignment->localidad ?: 'N/A' }})</div>
                                        </div>
                                        <button wire:click="removeAssignment({{ $assignment->id }})" 
                                                class="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all opacity-0 group-hover/item:opacity-100 translate-x-2 group-hover/item:translate-x-0 duration-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-10 text-center">
                                    <p class="text-[9px] font-black text-white/5 uppercase tracking-[0.4em] italic leading-relaxed">Sin operaciones activas</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-black/40 px-10 py-6 border-t border-white/5 flex justify-end">
                <button wire:click="closeAssignModal" class="px-8 py-3 bg-white/5 text-white/40 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all">
                    Cerrar Terminal
                </button>
            </div>
        </div>
    </div>
    @endif

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

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.3); }
    </style>
</div>
