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
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Centro de <span class="text-white">Despacho</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Control Maestro de Asignaciones
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Espacio para posibles acciones globales --}}
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto space-y-10 animate-fade-in">
            
            {{-- STATUS BAR: CARGA DE TRABAJO --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 animate-fade-in-down">
                {{-- BOTÓN DE ACCIÓN MAESTRA --}}
                <div class="lg:col-span-1">
                    <button wire:click="toggleForm" class="w-full h-full bg-ep-primary rounded-3xl p-6 flex flex-col items-center justify-center gap-2 group/btn hover:brightness-110 active:scale-95 transition-all shadow-[0_0_20px_rgba(246,146,57,0.3)]">
                        <svg class="w-8 h-8 text-white group-hover/btn:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}"/>
                        </svg>
                        <span class="text-[10px] font-black text-white uppercase tracking-widest italic">{{ $showForm ? 'Cerrar Panel' : 'Nueva Asignación' }}</span>
                    </button>
                </div>

                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl border border-white/5 p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all">
                    <div>
                        <p class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-1">Pendientes</p>
                        <p class="text-2xl font-black text-ep-primary italic leading-none">{{ \App\Models\ChecklistAssignment::where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-ep-primary/20 flex items-center justify-center text-ep-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl border border-white/5 p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all">
                    <div>
                        <p class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-1">Vencidos</p>
                        <p class="text-2xl font-black text-red-500 italic leading-none animate-pulse">{{ \App\Models\ChecklistAssignment::where('status', '!=', 'completed')->where('due_date', '<', now())->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center text-red-500">
                        <svg class="w-5 h-5 shadow-[0_0_10px_#ef4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>

                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl border border-white/5 p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all">
                    <div>
                        <p class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-1">En Curso</p>
                        <p class="text-2xl font-black text-indigo-400 italic leading-none">{{ \App\Models\ChecklistAssignment::where('status', 'in_progress')->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                </div>

                <div class="bg-ep-dark/40 backdrop-blur-md rounded-3xl border border-white/5 p-6 flex items-center justify-between group/stat hover:bg-white/5 transition-all">
                    <div>
                        <p class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-1">Completados</p>
                        <p class="text-2xl font-black text-emerald-400 italic leading-none">{{ \App\Models\ChecklistAssignment::where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            {{-- TOOLBAR TÁCTICO: FILTROS --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 lg:p-10 mb-10 shadow-2xl relative animate-fade-in-down">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                    
                    <div class="relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Filtrar por Empresa</span>
                        <select wire:model.live="selectedEmpresa" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                            <option value="">TODAS LAS EMPRESAS</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative group {{ !$selectedEmpresa ? 'opacity-20 pointer-events-none' : '' }}">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Filtrar por Sede</span>
                        <select wire:model.live="selectedSede" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                            <option value="">{{ $selectedEmpresa ? 'TODAS LAS SEDES' : 'BLOQUEADO' }}</option>
                            @if($selectedEmpresa)
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="lg:col-span-2 flex items-center justify-end">
                        <div class="flex items-center gap-4 px-6 py-3 bg-white/5 border border-white/10 rounded-[2rem]">
                            <span class="text-[9px] font-black text-white/20 uppercase tracking-widest italic italic">Sincronización en Tiempo Real Active</span>
                            <div class="w-2 h-2 bg-ep-primary rounded-full animate-ping"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Categorías Premium (Tabs de Gestión) --}}
            <div class="mb-10 flex flex-wrap items-center gap-6 animate-fade-in-down" style="animation-delay: 0.1s">
                <button wire:click="setActiveTab('inspections')" 
                        class="flex-1 min-w-[250px] group relative overflow-hidden rounded-[2.5rem] border transition-all duration-500 {{ $activeTab === 'inspections' ? 'bg-ep-primary border-ep-primary shadow-[0_0_40px_rgba(31,34,117,0.4)] scale-105' : 'bg-ep-dark/40 border-white/5 hover:border-ep-primary/30 hover:bg-ep-dark/60' }}">
                    <div class="px-8 py-7 flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-5">
                            <div class="h-14 w-14 rounded-[2rem] flex items-center justify-center {{ $activeTab === 'inspections' ? 'bg-white/20' : 'bg-white/5 border border-white/10 text-ep-primary group-hover:rotate-12 transition-transform' }}">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-[9px] font-black {{ $activeTab === 'inspections' ? 'text-white/60' : 'text-indigo-100/40' }} uppercase tracking-[0.3em] italic leading-none mb-1.5">Control de</p>
                                <h3 class="text-base font-black text-white uppercase italic tracking-tight">Inspecciones</h3>
                            </div>
                        </div>
                        <div class="text-3xl font-black italic {{ $activeTab === 'inspections' ? 'text-white' : 'text-ep-primary' }}">{{ $inspectionsCount }}</div>
                    </div>
                </button>

                <button wire:click="setActiveTab('trainings')" 
                        class="flex-1 min-w-[250px] group relative overflow-hidden rounded-[2.5rem] border transition-all duration-500 {{ $activeTab === 'trainings' ? 'bg-indigo-600 border-indigo-600 shadow-[0_0_40px_rgba(79,70,229,0.3)] scale-105' : 'bg-ep-dark/40 border-white/5 hover:border-indigo-600/30 hover:bg-ep-dark/60' }}">
                    <div class="px-8 py-7 flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-5">
                            <div class="h-14 w-14 rounded-[2rem] flex items-center justify-center {{ $activeTab === 'trainings' ? 'bg-white/20' : 'bg-white/5 border border-white/10 text-indigo-400 group-hover:rotate-12 transition-transform' }}">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-[9px] font-black {{ $activeTab === 'trainings' ? 'text-white/60' : 'text-indigo-100/40' }} uppercase tracking-[0.3em] italic leading-none mb-1.5">Control de</p>
                                <h3 class="text-base font-black text-white uppercase italic tracking-tight">Capacitaciones</h3>
                            </div>
                        </div>
                        <div class="text-3xl font-black italic {{ $activeTab === 'trainings' ? 'text-white' : 'text-indigo-400' }}">{{ $trainingsCount }}</div>
                    </div>
                </button>

                <button wire:click="setActiveTab('reports')" 
                        class="flex-1 min-w-[250px] group relative overflow-hidden rounded-[2.5rem] border transition-all duration-500 {{ $activeTab === 'reports' ? 'bg-emerald-600 border-emerald-600 shadow-[0_0_40px_rgba(16,185,129,0.3)] scale-105' : 'bg-ep-dark/40 border-white/5 hover:border-emerald-600/30 hover:bg-ep-dark/60' }}">
                    <div class="px-8 py-7 flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-5">
                            <div class="h-14 w-14 rounded-[2rem] flex items-center justify-center {{ $activeTab === 'reports' ? 'bg-white/20' : 'bg-white/5 border border-white/10 text-emerald-400 group-hover:rotate-12 transition-transform' }}">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-[9px] font-black {{ $activeTab === 'reports' ? 'text-white/60' : 'text-indigo-100/40' }} uppercase tracking-[0.3em] italic leading-none mb-1.5">Control de</p>
                                <h3 class="text-base font-black text-white uppercase italic tracking-tight">Informes</h3>
                            </div>
                        </div>
                        <div class="text-3xl font-black italic {{ $activeTab === 'reports' ? 'text-white' : 'text-emerald-400' }}">{{ $reportsCount }}</div>
                    </div>
                </button>
            </div>

            {{-- FORMULARIO DE INYECCIÓN DE TAREAS (HUD) --}}
            @if($showForm)
                <div class="bg-ep-dark/60 backdrop-blur-xl rounded-[3rem] border-2 border-ep-primary/30 p-10 shadow-2xl relative animate-fade-in-down overflow-hidden">
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
                                        <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-red-500 uppercase tracking-[0.3em] italic z-10">Fecha de Vencimiento</span>
                                        <input type="date" wire:model="formDueDate" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-red-500/50 transition-all [color-scheme:dark] italic">
                                    </div>
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-ep-dark px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Frecuencia (Manual Override)</span>
                                        <select class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white/20 italic uppercase" disabled>
                                            <option>SEGÚN PROTOCOLO</option>
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

            {{-- MASTER TABLE: MATRIZ DE ASIGNACIONES --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-8 py-6">
                                    <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">
                                        {{ match($activeTab) {
                                            'trainings' => 'Programa / Temario',
                                            'reports' => 'Título del Reporte',
                                            default => 'Tarea Técnica / Registro'
                                        } }}
                                    </span>
                                </th>
                                <th class="px-8 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Operador Técnico</span></th>
                                <th class="px-8 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Sede / Cliente</span></th>
                                <th class="px-8 py-6 text-center">
                                    <span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">
                                        {{ match($activeTab) {
                                            'trainings' => 'Duración / Tipo',
                                            'reports' => 'Periodo Técnico',
                                            default => 'Programación'
                                        } }}
                                    </span>
                                </th>
                                <th class="px-8 py-6 text-center"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Estado de Despacho</span></th>
                                <th class="px-8 py-6 text-right"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Comandos</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($assignments as $assignment)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                    <td class="px-8 py-8">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-ep-primary transition-colors">{{ $assignment->checklist->title ?? 'Sin Título' }}</div>
                                            @if(($assignment->source ?? 'manual') === 'contract')
                                                <span class="text-[8px] font-black text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded border border-indigo-500/20 uppercase italic">Contrato</span>
                                            @else
                                                <span class="text-[8px] font-black text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20 uppercase italic">Solicitud</span>
                                            @endif
                                        </div>
                                        @if($assignment->registry_code)
                                            <div class="text-[9px] font-bold text-white/20 italic font-mono">{{ $assignment->registry_code }}</div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="flex items-center gap-3">
                                            @if(($assignment->source ?? 'manual') === 'manual')
                                                <div class="w-8 h-8 rounded-full bg-ep-primary/20 border border-ep-primary/30 flex items-center justify-center text-[10px] font-black text-ep-primary group-hover/row:scale-110 transition-transform">
                                                    {{ substr($assignment->user->nombres ?? 'U', 0, 1) }}
                                                </div>
                                                <div class="text-sm font-bold text-indigo-100 italic">{{ $assignment->user->apellidos ?? '' }}, {{ $assignment->user->nombres ?? 'N/A' }}</div>
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-[10px] font-black text-indigo-100 group-hover/row:scale-110 transition-transform">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                </div>
                                                <div class="text-xs font-black text-white/40 uppercase italic tracking-widest leading-none">Operadores de Sede</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="text-[11px] font-black text-white italic uppercase tracking-tight leading-none mb-1">{{ $assignment->sede->nombre_sede }}</div>
                                        <div class="text-[9px] font-bold text-white/20 italic uppercase tracking-widest">{{ $assignment->empresa->nombre }}</div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        @if($activeTab === 'trainings')
                                            <div class="text-[10px] font-black text-white/60 mb-1 italic">
                                                {{ $assignment->duration ?? 'S/D' }} min
                                            </div>
                                            <div class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest italic">{{ $assignment->type ?? 'Presencial' }}</div>
                                        @elseif($activeTab === 'reports')
                                            <div class="text-[10px] font-black text-white/60 mb-1 italic">
                                                {{ $assignment->periodo ?? 'S/P' }}
                                            </div>
                                            <div class="text-[8px] font-bold text-emerald-400 uppercase tracking-widest italic">Anual / Mensual</div>
                                        @else
                                            <div class="text-[10px] font-black text-white/60 mb-1 italic">
                                                {{ $assignment->due_date ? (is_string($assignment->due_date) ? $assignment->due_date : $assignment->due_date->format('d/M/Y')) : 'SIN FECHA' }}
                                            </div>
                                            <div class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest italic">{{ $assignment->frequency_label }}</div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        @php
                                            $color = $assignment->status_badge_color ?? 'gray';
                                            $statusClasses = match($color) {
                                                'emerald', 'green' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.2)]',
                                                'blue', 'indigo' => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.2)]',
                                                'yellow', 'amber' => 'bg-amber-500/10 border-amber-500/20 text-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.2)]',
                                                'red' => 'bg-red-500/10 border-red-500/20 text-red-500 shadow-[0_0_15px_rgba(239,68,68,0.2)] animate-pulse',
                                                default => 'bg-white/5 border-white/10 text-white/30'
                                            };
                                            $statusLabel = match($assignment->status) {
                                                'pending' => 'PENDIENTE',
                                                'in_progress' => 'EN CURSO',
                                                'completed' => 'COMPLETADO',
                                                default => strtoupper($assignment->status)
                                            };
                                        @endphp
                                        <div class="inline-flex items-center px-4 py-1.5 border rounded-xl text-[9px] font-black uppercase tracking-[0.2em] italic {{ $statusClasses }}">
                                            @if($assignment->is_overdue ?? false)
                                                VENCIDO CRÍTICO
                                            @else
                                                {{ $statusLabel }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-right">
                                        <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                            @if($assignment->can_be_edited)
                                                <button wire:click="edit({{ $assignment->id }})" class="h-10 w-10 bg-white/5 border border-white/10 text-white hover:bg-blue-600 rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                            @endif

                                            @if(($assignment->source ?? 'manual') === 'contract')
                                                <a href="{{ route('gestor.annual-contract.index') }}" class="h-10 w-10 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Ver Plan Maestro">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                </a>
                                            @endif

                                            @if($assignment->status === 'completed')
                                                @if($assignment->inspection)
                                                    <a href="{{ route('gestor.inspections.pdf.preview', $assignment->inspection->id) }}" target="_blank" class="h-10 w-10 bg-ep-primary/10 border border-ep-primary/20 text-ep-primary hover:bg-ep-primary hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Ver Reporte">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                @endif
                                            @else
                                                {{-- Botón de Acción Directa --}}
                                                @if(($assignment->category ?? 'Inspección') === 'Informe')
                                                    <a href="{{ route('gestor.master-reports.index') }}" class="h-10 px-4 bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-600 hover:text-white rounded-xl flex items-center gap-2 transition-all shadow-lg text-[9px] font-black uppercase tracking-widest italic">
                                                        Redactar Informe
                                                    </a>
                                                @endif
                                            @endif

                                            @if($assignment->can_be_deleted)
                                                <button wire:click="delete({{ $assignment->id }})" wire:confirm="¿Desea abortar esta asignación táctica?" class="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">No se detectaron órdenes de trabajo activas <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inicie una nueva asignación desde el panel superior</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-6 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="text-[9px] font-black text-white/10 uppercase tracking-[0.5em] italic">XamaSafe Dispatch Engine v4.0 • Level 4 Clearance</div>
                    <div class="pagination-tactical">
                        {{ $assignments->links() }}
                    </div>
                </div>
            </div>

            
        </div>
    </div>

    <style>
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        
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

