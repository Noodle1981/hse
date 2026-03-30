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
                            Tablero de <span class="text-white">Cumplimiento Anual</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Ciclo Operativo {{ $year }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-[9px] font-black text-white uppercase tracking-widest italic leading-none mb-1">Sede Activa</span>
                        <span class="text-xs font-black text-white italic uppercase">{{ $sedes->find($sedeId)?->nombre_sede }}</span>
                    </div>
                    <a href="{{ route('profile') }}" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary group hover:bg-white/10 transition-all shadow-inner">
                        <span class="text-sm font-black">{{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto">
            
            {{-- KPI ROW: RESUMEN DE INTELIGENCIA --}}
            @php
                $totalProgrammed = 0;
                $completed = 0;
                $overdue = 0;
                foreach($complianceData as $pId => $mobs) {
                    foreach($mobs as $name => $data) {
                        if ($data['status'] !== 'none') $totalProgrammed++;
                        if ($data['status'] === 'completed') $completed++;
                        if ($data['status'] === 'overdue') $overdue++;
                    }
                }
                $progress = $totalProgrammed > 0 ? round(($completed / $totalProgrammed) * 100) : 0;
            @endphp
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 animate-fade-in-down">
                {{-- KPI: Progreso --}}
                <div class="bg-ep-dark/40 backdrop-blur-md p-6 rounded-[2.5rem] border border-white/5 shadow-2xl relative group overflow-hidden border-l-4 border-ep-primary/40">
                    <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/5 to-transparent pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-black text-ep-primary uppercase tracking-[0.2em] italic">Progreso Anual</span>
                            <div class="p-2 bg-ep-primary/10 rounded-xl text-ep-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white italic tracking-tighter">{{ $progress }}%</span>
                            <span class="text-[10px] font-bold text-white uppercase tracking-widest italic">Completado</span>
                        </div>
                        <div class="mt-4 h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-ep-primary shadow-[0_0_10px_rgba(246,146,57,0.5)] transition-all duration-1000" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- KPI: Servicios --}}
                <div class="bg-ep-dark/40 backdrop-blur-md p-6 rounded-[2.5rem] border border-white/5 shadow-2xl relative group overflow-hidden border-l-4 border-indigo-500/40">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] italic">Servicios Planificados</span>
                            <div class="p-2 bg-indigo-500/10 rounded-xl text-indigo-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white italic tracking-tighter">{{ $totalProgrammed }}</span>
                            <span class="text-[10px] font-bold text-white uppercase tracking-widest italic">Tareas Totales</span>
                        </div>
                        <p class="mt-3 text-[9px] text-indigo-100/20 font-bold uppercase tracking-widest italic">Consolidado del ciclo {{ $year }}</p>
                    </div>
                </div>

                {{-- KPI: Éxito --}}
                <div class="bg-ep-dark/40 backdrop-blur-md p-6 rounded-[2.5rem] border border-white/5 shadow-2xl relative group overflow-hidden border-l-4 border-emerald-500/40">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] italic">Cumplimiento Operativo</span>
                            <div class="p-2 bg-emerald-500/10 rounded-xl text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white italic tracking-tighter">{{ $completed }}</span>
                            <span class="text-[10px] font-bold text-white uppercase tracking-widest italic">Ejecutados ✅</span>
                        </div>
                        <p class="mt-3 text-[9px] text-emerald-500/40 font-black uppercase tracking-widest italic leading-none">Validación técnica conforme</p>
                    </div>
                </div>

                {{-- KPI: Riesgo --}}
                <div class="bg-ep-dark/40 backdrop-blur-md p-6 rounded-[2.5rem] border border-white/5 shadow-2xl relative group overflow-hidden border-l-4 border-red-500/40">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-black text-red-500 uppercase tracking-[0.2em] italic">Vencimientos detectados</span>
                            <div class="p-2 bg-red-500/10 rounded-xl text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white italic tracking-tighter {{ $overdue > 0 ? 'animate-pulse text-red-500' : '' }} font-black">{{ $overdue }}</span>
                            <span class="text-[10px] font-bold text-white uppercase tracking-widest italic">Pendientes/Vencidos</span>
                        </div>
                        <p class="mt-3 text-[9px] text-red-500/40 font-black uppercase tracking-widest italic leading-none">Requiere atención inmediata</p>
                    </div>
                </div>
            </div>

            {{-- TOOLBAR & FILTERS --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 sm:p-10 mb-10 shadow-2xl relative">
                <div class="flex flex-col md:flex-row justify-between items-center gap-12 relative z-10">
                    <div class="flex flex-col sm:flex-row items-center gap-10 w-full md:w-auto">
                        <div class="relative group w-full sm:w-80">
                            <span class="absolute -top-3 left-6 bg-[#11111d] px-3 text-[9px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Filtrar por Sede</span>
                            <select wire:model.live="sedeId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] appearance-none cursor-pointer">
                                @foreach($sedes as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre_sede }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-ep-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        @if(auth()->user()->isAdmin())
                            <button wire:click="$toggle('editMode')" class="w-full sm:w-auto h-14 px-8 flex items-center justify-center gap-3 rounded-2xl font-black text-[10px] uppercase tracking-widest italic transition-all shadow-xl group/edit {{ $editMode ? 'bg-indigo-600 text-white' : 'bg-white/5 border border-white/10 text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4 {{ $editMode ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                                {{ $editMode ? 'Finalizar Edición' : 'Modo Planificación' }}
                            </button>
                        @endif
                    </div>

                    @if(!$editMode && $sedeId)
                        <button wire:click="downloadPdf" class="w-full md:w-auto h-14 px-10 bg-white text-black rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] italic hover:scale-105 transition-all shadow-2xl flex items-center justify-center group/pdf">
                            <svg class="w-4 h-4 mr-3 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Exportar Reporte
                        </button>
                    @endif
                </div>
            </div>

            {{-- FORMULARIO DE PLANIFICACIÓN (Modo Admin) --}}
            @if($editMode && auth()->user()->isAdmin())
            <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-[2.5rem] p-8 sm:p-12 mb-10 shadow-2xl animate-fade-in relative overflow-hidden group/form">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                <h3 class="text-xs font-black text-indigo-400 uppercase tracking-[0.3em] mb-8 italic flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Interfaz de Programación Técnica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-6 items-end">
                    <div class="md:col-span-1">
                        <label class="block text-[8px] font-black text-indigo-100/40 mb-2 uppercase tracking-widest italic">Categoría</label>
                        <select wire:model.live="category" class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-sm text-white font-bold focus:ring-2 focus:ring-indigo-500 transition-all [color-scheme:dark]">
                            <option value="Capacitación">Capacitación (E)</option>
                            <option value="Inspección">Inspección (I)</option>
                            <option value="Informe">Informe (M/B/T)</option>
                        </select>
                    </div>

                    @if($category === 'Informe')
                    <div class="md:col-span-1 animate-fade-in">
                        <label class="block text-[8px] font-black text-indigo-100/40 mb-2 uppercase tracking-widest italic">Frecuencia</label>
                        <select wire:model="frequency" class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-sm text-indigo-400 font-black focus:ring-2 focus:ring-indigo-500 transition-all [color-scheme:dark]">
                            <option value="none">Manual</option>
                            <option value="mensual">Mensual (12)</option>
                            <option value="bimestral">Bimestral (6)</option>
                            <option value="trimestral">Trimestral (4)</option>
                        </select>
                    </div>
                    @else
                    <div class="md:col-span-1 animate-fade-in">
                        <label class="block text-[8px] font-black text-indigo-100/40 mb-2 uppercase tracking-widest italic">Checklist Vinculado</label>
                        <select wire:model.live="checklist_id" class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-sm text-white font-bold focus:ring-2 focus:ring-indigo-500 transition-all [color-scheme:dark]">
                            <option value="">— Seleccionar —</option>
                            @foreach($checklists as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="md:col-span-2">
                        <label class="block text-[8px] font-black text-indigo-100/40 mb-2 uppercase tracking-widest italic">Nombre de la Actividad</label>
                        <input type="text" wire:model="activity_name" placeholder="Ej: Control de Extintores" class="w-full bg-black/40 border border-white/5 rounded-xl px-5 py-3 text-sm text-white font-bold focus:ring-2 focus:ring-indigo-500 transition-all placeholder-white/5 uppercase">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[8px] font-black text-indigo-100/40 mb-2 uppercase tracking-widest italic">Código de Registro</label>
                        <div class="flex gap-4">
                            <input type="text" wire:model="activity_code" placeholder="EP-REG-01" class="w-full bg-black/40 border border-white/5 rounded-xl px-5 py-3 text-sm text-white font-bold focus:ring-2 focus:ring-indigo-500 transition-all placeholder-white/5 uppercase">
                            <button wire:click="addActivity" class="w-52 h-12 bg-indigo-600 text-white font-black text-[9px] uppercase tracking-[0.2em] italic rounded-xl hover:bg-indigo-500 transition-all shadow-lg active:scale-95">
                                REGISTRAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- MASTER COMPLIANCE BOARD (MATRIX) --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group/matrix mb-10">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                
                {{-- Legend Táctica --}}
                <div class="px-10 py-6 border-b border-white/5 flex flex-wrap items-center gap-x-8 gap-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981] ring-2 ring-white/10"></div>
                        <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic leading-none">Ejecutado</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-yellow-400 shadow-[0_0_8px_#fbbf24] ring-2 ring-white/10"></div>
                        <span class="text-[9px] font-black text-yellow-400 uppercase tracking-widest italic leading-none">Planificado (Pendiente)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444] ring-2 ring-white/20 animate-pulse"></div>
                        <span class="text-[9px] font-black text-red-500 uppercase tracking-widest italic leading-none">Vencimiento Crítico</span>
                    </div>
                    <div class="flex items-center gap-3 opacity-30">
                        <div class="w-3 h-3 rounded-full bg-white/10 ring-1 ring-white/10"></div>
                        <span class="text-[9px] font-black text-indigo-100 uppercase tracking-widest italic leading-none">Sin Programar</span>
                    </div>
                </div>

                {{-- DESKTOP TABLE VIEW --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20">
                                <th class="sticky left-0 z-20 bg-black/40 backdrop-blur-md px-10 py-6 text-left">
                                    <span class="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic">Matriz de Actividades</span>
                                </th>
                                @foreach($months as $name => $num)
                                    <th class="px-2 py-6 text-center">
                                        <span class="text-[9px] font-black text-white uppercase tracking-[0.4em] italic">{{ substr(strtoupper($name), 0, 3) }}</span>
                                    </th>
                                @endforeach
                                @if($editMode) <th class="px-6 py-6 text-right"></th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @php $currentCategory = ''; @endphp
                            @forelse($plans as $plan)
                                @if($currentCategory !== $plan->category)
                                    <tr class="bg-white/5">
                                        <td colspan="15" class="px-10 py-3 text-[8px] font-black text-indigo-400 uppercase tracking-[0.4em] italic">
                                            <span class="flex items-center gap-3">
                                                <span class="w-8 h-px bg-indigo-400/20"></span>
                                                CATEGORÍA: {{ $plan->category }}
                                            </span>
                                        </td>
                                    </tr>
                                    @php $currentCategory = $plan->category; @endphp
                                @endif
                                <tr class="group/row hover:bg-white/5 transition-all duration-500">
                                    <td class="sticky left-0 z-10 bg-[#1e1e2d] group-hover/row:bg-white/5 px-10 py-6 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-1.5 h-8 {{ $plan->category === 'Capacitación' ? 'bg-blue-500' : ($plan->category === 'Inspección' ? 'bg-orange-500' : 'bg-ep-primary') }} rounded-full shadow-lg"></div>
                                            <div>
                                                <div class="text-sm font-black text-white italic uppercase tracking-tight leading-none mb-2">{{ $plan->activity_name }}</div>
                                                <div class="text-[9px] text-white font-bold uppercase tracking-widest italic leading-none">{{ $plan->activity_code ?: 'SIN CÓDIGO' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @foreach($months as $name => $num)
                                        @php 
                                            $data = $complianceData[$plan->id][$name] ?? ['status' => 'none', 'count' => 0];
                                            $status = $data['status'];
                                            $cnt = $data['count'];
                                        @endphp
                                        <td class="px-1 py-6 text-center">
                                            <div 
                                                @if($editMode) wire:click="toggleMonth({{ $plan->id }}, '{{ $name }}')" @endif
                                                class="relative mx-auto w-10 h-10 rounded-2xl flex items-center justify-center text-xs font-black transition-all group/cell
                                                {{ $status === 'completed' ? 'bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.4)] text-white scale-110 active:scale-95' : '' }}
                                                {{ $status === 'overdue' ? 'bg-red-500 shadow-[0_0_15px_rgba(239,68,68,0.4)] text-white animate-pulse' : '' }}
                                                {{ $status === 'pending' ? 'bg-yellow-400 shadow-[0_0_15px_rgba(251,191,36,0.3)] text-white' : '' }}
                                                {{ $status === 'none' ? 'bg-white/5 border border-white/5 text-transparent opacity-20' : '' }}
                                                {{ $editMode ? 'cursor-pointer hover:scale-110 border-2 border-white/10' : 'cursor-default border border-white/5' }}">
                                                
                                                @if($status !== 'none')
                                                    @if($status === 'completed')
                                                        <svg class="w-5 h-5 group-hover/cell:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                                        @if($cnt > 1)
                                                            <span class="absolute -top-2 -right-2 bg-white text-emerald-600 text-[8px] font-black px-1.5 py-0.5 rounded-lg shadow-xl">{{ $cnt }}</span>
                                                        @endif
                                                    @elseif($status === 'pending')
                                                        <span class="opacity-40">T</span>
                                                    @elseif($status === 'overdue')
                                                        <span class="animate-bounce">!</span>
                                                    @endif
                                                @endif
                                                
                                                {{-- Tooltip sutil --}}
                                                <div class="absolute -top-12 left-1/2 -translate-x-1/2 px-3 py-1 bg-black text-[8px] font-black text-white uppercase rounded-lg opacity-0 group-hover/cell:opacity-100 transition-opacity whitespace-nowrap z-30 pointer-events-none">
                                                    {{ ucfirst($name) }}: {{ $status === 'none' ? 'No Planificado' : strtoupper($status) }}
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach
        
                                    @if($editMode)
                                    <td class="px-8 py-6 text-right">
                                        <button wire:click="deleteActivity({{ $plan->id }})" class="p-3 bg-red-500/10 text-red-500 rounded-xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="px-10 py-32 text-center">
                                        <div class="h-20 w-20 bg-white/5 rounded-3xl border border-white/5 flex items-center justify-center mx-auto mb-6 text-indigo-100/10">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <h3 class="text-sm font-black text-indigo-100/20 uppercase tracking-[0.4em] italic mb-2">Archivo Anual Vacío</h3>
                                        <p class="text-[10px] text-indigo-100/10 font-bold uppercase tracking-widest italic leading-none">No se han detectado actividades planificadas para el ciclo actual</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        
                {{-- MOBILE TACTICAL VIEW --}}
                <div class="lg:hidden flex flex-col gap-6 p-6">
                    @php $mobileCategory = ''; @endphp
                    @forelse($plans as $plan)
                        @if($mobileCategory !== $plan->category)
                            <div class="text-[9px] font-black text-ep-primary uppercase tracking-[0.4em] italic mt-6 first:mt-0 flex items-center gap-3">
                                <span class="w-4 h-px bg-ep-primary/30"></span> {{ strtoupper($plan->category) }}
                            </div>
                            @php $mobileCategory = $plan->category; @endphp
                        @endif
                        <div class="bg-black/40 rounded-[2rem] border border-white/5 p-6 hover:border-ep-primary/20 transition-all shadow-xl">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <div class="text-xs font-black text-white italic uppercase tracking-tight leading-none mb-2">
                                        {{ $plan->activity_name }}
                                    </div>
                                    <div class="text-[9px] text-white font-bold uppercase tracking-widest italic">
                                        {{ $plan->activity_code ?: 'SN CODE' }}
                                    </div>
                                </div>
                                <div class="w-2 h-6 {{ $plan->category === 'Capacitación' ? 'bg-blue-500' : ($plan->category === 'Inspección' ? 'bg-orange-500' : 'bg-ep-primary') }} rounded-full shadow-lg"></div>
                            </div>
        
                            <div class="grid grid-cols-4 gap-3 pt-6 border-t border-white/5">
                                @foreach($months as $name => $num)
                                    @php 
                                        $data = $complianceData[$plan->id][$name] ?? ['status' => 'none', 'count' => 0];
                                        $status = $data['status'];
                                    @endphp
                                    <div class="flex flex-col items-center">
                                        <span class="text-[8px] font-black text-white uppercase mb-2 italic">
                                            {{ substr(strtoupper($name), 0, 3) }}
                                        </span>
                                        <div 
                                            @if($editMode) wire:click="toggleMonth({{ $plan->id }}, '{{ $name }}')" @endif
                                            class="relative w-10 h-10 rounded-xl flex items-center justify-center text-xs font-black border
                                            {{ $status === 'completed' ? 'bg-emerald-500 border-white/20 text-white shadow-lg' : '' }}
                                            {{ $status === 'overdue' ? 'bg-red-500 border-white/20 text-white animate-pulse' : '' }}
                                            {{ $status === 'pending' ? 'bg-yellow-400 border-white/20 text-white' : '' }}
                                            {{ $status === 'none' ? 'bg-white/5 border-white/5 text-transparent opacity-20' : '' }}">
                                            
                                            @if($status === 'completed')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                            @elseif($status !== 'none')
                                                !
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
        
                            @if($editMode)
                                <div class="flex justify-end pt-4 mt-4 border-t border-white/5">
                                    <button wire:click="deleteActivity({{ $plan->id }})" class="text-[9px] font-black text-red-500 uppercase tracking-widest italic flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar Registro
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-indigo-100/10 text-[9px] font-black uppercase tracking-widest italic">
                            No se han programado actividades aún
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="text-center mt-12 pb-20">
                <p class="text-[9px] font-black text-indigo-100/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Annual Matrix HUD • Master Compliance Specialist Protocol</p>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('open-url', (event) => {
            window.open(event.url, '_blank');
        });
    </script>
    @endscript

    <style>
        .glow-text-orange { text-shadow: 0 0 10px rgba(246,146,57,0.3); }
        .animate-fade-in-down {
            animation: fade-in-down 0.6s ease-out forwards;
        }
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</div>
