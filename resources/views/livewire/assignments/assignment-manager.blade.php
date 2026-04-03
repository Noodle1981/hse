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
                            Centro de <span class="text-white">Despacho</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Gestión de Inspecciones
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
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/40 to-transparent"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6 lg:gap-8 items-end relative z-10">
                    {{-- BUSCADOR TÁCTICO --}}
                    <div class="md:col-span-5 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Terminal de Escaneo</span>
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   placeholder="BUSCAR PROTOCOLO O CÓDIGO..."
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-12 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic uppercase">
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-ep-primary/30 group-focus-within:text-ep-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- FILTROS DE INTELIGENCIA --}}
                    <div class="md:col-span-4 grid grid-cols-2 gap-4">
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Empresa</span>
                            <select wire:model.live="selectedEmpresa" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer">
                                <option value="">TODAS</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/20 uppercase tracking-[0.3em] italic z-10">Sede</span>
                            <select wire:model.live="selectedSede" class="w-full bg-black/40 border border-white/10 rounded-2xl px-4 py-4 text-[10px] font-black text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer {{ !$selectedEmpresa ? 'opacity-20 cursor-not-allowed' : '' }}" {{ !$selectedEmpresa ? 'disabled' : '' }}>
                                <option value="">TODAS</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ACCIÓN PRIMARIA --}}
                    <div class="md:col-span-3">
                        <button wire:click="toggleForm" 
                           class="w-full flex h-14 bg-ep-primary text-white hover:brightness-110 active:scale-95 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-3 shadow-[0_0_25px_rgba(246,146,57,0.3)] group/btn relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/10 translate-y-14 group-hover/btn:translate-y-0 transition-transform duration-500"></div>
                            <svg class="w-5 h-5 group-hover/btn:-rotate-90 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}"/>
                            </svg>
                            <span class="relative z-10">{{ $showForm ? 'Cerrar Despacho' : 'Asignar Tarea' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO DE INYECCIÓN DE TAREAS (HUD) --}}
            @if($showForm)
                <div class="bg-ep-dark/60 backdrop-blur-xl rounded-3xl sm:rounded-[3rem] border-2 border-ep-primary/30 p-5 sm:p-10 shadow-[0_0_50px_rgba(246,146,57,0.1)] relative animate-fade-in-down overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-ep-primary/50 to-transparent"></div>
                    
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xs font-black text-white italic uppercase tracking-[0.3em]">
                            Laboratorio de <span class="text-ep-primary">Asignación Maestro</span>
                        </h3>
                        <div class="text-[10px] font-black text-white/20 uppercase tracking-widest italic">Operación de Despacho Técnico</div>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                            
                            {{-- MASTER SELECTORS --}}
                            <div class="md:col-span-4 space-y-8">
                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Empresa de Destino</span>
                                    @if($editingId)
                                        <input type="text" value="{{ $empresas->find($formEmpresaId)?->nombre }}" disabled class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white/40 italic">
                                    @else
                                        <select wire:model.live="formEmpresaId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] italic uppercase">
                                            <option value="">SELECCIONAR EMPRESA...</option>
                                            @foreach($empresas as $empresa)
                                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('formEmpresaId') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                                    @endif
                                </div>

                                <div class="relative group {{ !$formEmpresaId ? 'opacity-20 pointer-events-none' : '' }}">
                                    <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Sede Operativa</span>
                                    @if($editingId)
                                        <input type="text" value="{{ $empresas->find($formEmpresaId)?->sedes->find($formSedeId)?->nombre_sede }}" disabled class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white/40 italic">
                                    @else
                                        <select wire:model.live="formSedeId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase">
                                            <option value="">{{ $formEmpresaId ? 'SELECCIONAR SEDE...' : 'BLOQUEADO' }}</option>
                                            @if($formEmpresaId)
                                                @foreach($empresas->find($formEmpresaId)?->sedes ?? [] as $sede)
                                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('formSedeId') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                                    @endif
                                </div>
                            </div>

                            {{-- PROTOCOLO Y TÉCNICO --}}
                            <div class="md:col-span-8 space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="relative group {{ !$formSedeId ? 'opacity-20 pointer-events-none' : '' }}">
                                        <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Selección de Protocolo Maestro</span>
                                        @if($editingId)
                                            <input type="text" value="{{ \App\Models\DynamicChecklist::find($formChecklistId)?->title }}" disabled class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white/40 italic">
                                        @else
                                            <select wire:model="formChecklistId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all [color-scheme:dark] italic uppercase">
                                                <option value="">{{ $formSedeId ? 'SELECCIONAR PROTOCOLO...' : 'BLOQUEADO' }}</option>
                                                @if($formSedeId)
                                                    @foreach($checklists as $checklist)
                                                        <option value="{{ $checklist->id }}">{{ $checklist->title }} {{ $checklist->registry_code ? "($checklist->registry_code)" : '' }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('formChecklistId') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                                        @endif
                                    </div>

                                    <div class="relative group {{ !$formSedeId ? 'opacity-20 pointer-events-none' : '' }}">
                                        <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Técnico Asignado</span>
                                        <select wire:model="formUserId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] italic uppercase">
                                            <option value="">{{ $formSedeId ? 'SELECCIONAR TÉCNICO...' : 'BLOQUEADO' }}</option>
                                            @if($formSedeId)
                                                @foreach($tecnicos as $tecnico)
                                                    <option value="{{ $tecnico->id }}">{{ $tecnico->apellidos }}, {{ $tecnico->nombres }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('formUserId') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-red-500 uppercase tracking-[0.3em] italic z-10">Fecha Límite Táctica</span>
                                        <input type="date" wire:model="formDueDate" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] italic">
                                    </div>
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Frecuencia (Manual Override)</span>
                                        <select class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white/20 italic uppercase" disabled>
                                            <option>ÚNICA / UNA VEZ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Notas de Despacho y Logística</span>
                            <textarea wire:model="formNotes" rows="2" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic" placeholder="Instrucciones especiales para el técnico..."></textarea>
                        </div>

                        <div class="flex justify-end gap-6 pt-10 border-t border-white/5">
                            <button type="button" wire:click="toggleForm" class="px-8 h-12 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic transition-all">Descartar</button>
                            <button type="submit" wire:loading.attr="disabled" class="px-10 h-12 bg-ep-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic shadow-[0_0_20px_rgba(246,146,57,0.3)] hover:brightness-110 active:scale-95 transition-all">
                                <span wire:loading.remove>{{ $editingId ? 'CONSOLIDAR CAMBIOS' : 'INICIAR ASIGNACIÓN' }}</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Sincronizando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- MATRIZ DE DE ASIGNACIONES (REDISEÑO) --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Inspección / Protocolo</th>
                                <th class="hidden xl:table-cell px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Técnico</th>
                                <th class="hidden md:table-cell px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Punto de Despliegue</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Cronograma</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">HUD</th>
                                <th class="px-4 lg:px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($assignments as $assignment)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                    <td class="px-4 lg:px-8 py-6">
                                        <div class="flex items-center gap-3 sm:gap-4">
                                            <div class="relative group/icon hidden xs:block">
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl {{ ($assignment->source ?? 'manual') === 'contract' ? 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400' : 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary' }} border flex items-center justify-center text-xs font-black group-hover:scale-110 transition-transform">
                                                    @if(($assignment->source ?? 'manual') === 'contract')
                                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                                    @else
                                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                                    <div class="text-xs sm:text-sm font-black text-white italic uppercase tracking-tight leading-none lg:group-hover/row:text-ep-primary transition-colors">
                                                        {{ $assignment->checklist->title ?? 'Sin Título' }}
                                                    </div>
                                                </div>
                                                <div class="text-[8px] sm:text-[9px] font-black text-white/20 uppercase tracking-widest italic flex items-center gap-2">
                                                    @if($assignment->registry_code)
                                                        <span class="font-mono text-white/30 hidden sm:inline">[{{ $assignment->registry_code }}]</span>
                                                    @endif
                                                    <span class="text-white/40">{{ ($assignment->source ?? 'manual') === 'contract' ? 'Contrato Anual' : 'Despacho Manual' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="hidden xl:table-cell px-4 lg:px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl border {{ ($assignment->source ?? 'manual') === 'contract' ? 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400' : 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary' }} flex items-center justify-center text-[10px] font-black shadow-inner">
                                                {{ substr($assignment->user->nombres ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-black text-white italic uppercase leading-none">{{ $assignment->user->nombres ?? 'NO ASIGNADO' }}</div>
                                                <div class="text-[9px] font-bold text-white/20 italic mt-0.5 whitespace-nowrap">{{ $assignment->user->apellidos ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="hidden md:table-cell px-4 lg:px-8 py-6">
                                        <div class="text-[10px] font-black text-white italic uppercase tracking-tight leading-none mb-1 lg:group-hover/row:text-ep-primary transition-colors">{{ $assignment->empresa->nombre ?? 'N/A' }}</div>
                                        <div class="text-[8px] font-bold text-white/20 italic tracking-widest uppercase">{{ $assignment->sede->nombre_sede ?? 'N/A' }}</div>
                                    </td>
                                    
                                    <td class="px-4 lg:px-8 py-6">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2 text-[10px] font-black text-white/60 italic">
                                                <svg class="w-3 h-3 text-red-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $assignment->due_date ? (is_string($assignment->due_date) ? $assignment->due_date : $assignment->due_date->format('d/m/y')) : 'Sin Fecha' }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[8px] font-black {{ ($assignment->source ?? 'manual') === 'contract' ? 'text-indigo-400' : 'text-ep-primary' }} uppercase italic">{{ $assignment->frequency_label ?? 'Manual' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 lg:px-8 py-6 text-center">
                                        @php
                                            $color = $assignment->status_badge_color ?? 'gray';
                                            $statusClasses = match($color) {
                                                'emerald', 'green' => 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_10px_rgba(16,185,129,0.1)]',
                                                'blue', 'indigo' => 'text-indigo-400 border-indigo-500/30 bg-indigo-500/10 shadow-[0_0_10px_rgba(99,102,241,0.1)]',
                                                'yellow', 'amber' => 'text-amber-400 border-amber-500/30 bg-amber-500/10 shadow-[0_0_10px_rgba(245,158,11,0.1)]',
                                                'red' => 'text-red-400 border-red-500/30 bg-red-500/10 shadow-[0_0_10px_rgba(239,68,68,0.1)]',
                                                default => 'text-white/20 border-white/5 bg-white/5'
                                            };
                                            $statusLabel = match($assignment->status) {
                                                'pending' => 'PENDIENTE',
                                                'in_progress' => 'EN CURSO',
                                                'completed' => 'FINALIZADA',
                                                default => strtoupper($assignment->status)
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 sm:px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest italic border {{ $statusClasses }}">
                                            @if($assignment->is_overdue ?? false) VENCIDO @else {{ $statusLabel }} @endif
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 lg:px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2 sm:gap-3 lg:translate-x-4 lg:opacity-0 lg:group-hover/row:translate-x-0 lg:group-hover/row:opacity-100 transition-all duration-500">
                                            
                                            @if(($assignment->source ?? 'manual') === 'contract')
                                                <a href="{{ route('gestor.annual-contract.index') }}" class="h-9 w-9 sm:h-10 sm:w-auto sm:px-4 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg text-[9px] font-black uppercase italic tracking-widest">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    <span class="hidden md:inline">Plan Maestro</span>
                                                </a>
                                            @else
                                                @if($assignment->can_be_edited)
                                                    <button wire:click="edit({{ $assignment->id }})" class="h-9 w-9 sm:h-10 sm:w-10 bg-white/5 border border-white/10 text-white hover:bg-blue-600 rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </button>
                                                @endif
                                                @if($assignment->status === 'completed' && isset($assignment->inspection))
                                                    <a href="{{ route('gestor.inspections.pdf.preview', $assignment->inspection->id) }}" target="_blank" class="h-9 w-9 sm:h-10 sm:w-10 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" title="Ver Reporte">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    </a>
                                                @endif
                                                @if($assignment->can_be_deleted)
                                                    <button wire:click="delete({{ $assignment->id }})" wire:confirm="¿Desea abortar esta asignación táctica?" class="h-9 w-9 sm:h-10 sm:w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">Sin Despliegues Activos <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inicie una nueva asignación de protocolo desde la barra superior</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-10 py-6 border-t border-white/5 bg-black/20 pagination-tactical">
                    {{ $assignments->links() }}
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
            border-color: rgba(246,146,57,0.3) !important;
            color: #f69239 !important;
            background: rgba(246,146,57,0.1) !important;
        }
    </style>
</div>
