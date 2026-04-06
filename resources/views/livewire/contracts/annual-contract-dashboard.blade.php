<div class="w-full mx-auto">
    @section('header_custom')
    <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
        <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <div class="flex flex-col">
                    <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic mb-1 leading-none">
                        Generador de <span class="text-white">Plan de Operaciones</span>
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @if($efficiency['counts']['overdue'] > 0)
                <div class="hidden sm:flex px-3 py-1 bg-red-500/10 border border-red-500/30 rounded-lg animate-pulse items-center gap-2 mr-2">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full shadow-[0_0_8px_#ef4444]"></span>
                    <span class="text-[9px] font-black text-red-400 uppercase tracking-widest italic">{{ $efficiency['counts']['overdue'] }} ALERTAS</span>
                </div>
                @endif

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

    <div class="px-2 sm:px-6 lg:px-8 py-10 text-white selection:bg-ep-primary/30">
        <div class="max-w-[98rem] mx-auto">

            {{-- INDICATORS REMOVED: NOW CENTRALIZED IN MAIN DASHBOARD --}}

            {{-- MASTER CONTROLS --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                {{-- Selector de Sede --}}
                <div class="lg:col-span-12 bg-ep-dark/40 backdrop-blur-md rounded-3xl sm:rounded-[2.5rem] border border-white/5 p-5 sm:p-8 shadow-2xl relative flex flex-col md:flex-row items-center justify-between gap-6 sm:gap-10">
                    <div class="flex flex-col xl:flex-row items-center gap-6 sm:gap-10 w-full xl:w-auto">
                        <div class="flex flex-col sm:flex-row items-center gap-6 w-full xl:w-auto">
                            <div class="flex items-center gap-6 w-full sm:w-auto">
                                <div class="h-10 w-10 sm:h-14 sm:w-14 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-ep-primary shadow-inner shrink-0">
                                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-[140px]">
                                    <span class="text-[8px] sm:text-[9px] font-black text-ep-primary/60 uppercase tracking-[0.4em] italic mb-1 block">Empresa</span>
                                    <select wire:model.live="selectedCompanyId" class="w-full bg-transparent border-none text-sm sm:text-base font-black text-white italic uppercase tracking-tight focus:ring-0 cursor-pointer [color-scheme:dark] [&>option]:bg-[#1e1e2d] [&>option]:text-white p-0">
                                        @foreach($companies as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="h-px w-full sm:h-10 sm:w-px bg-white/5"></div>

                            <div class="flex-1 min-w-[140px] w-full sm:w-auto">
                                <span class="text-[8px] sm:text-[9px] font-black text-ep-primary uppercase tracking-[0.4em] italic mb-1 block">Activo / Sede</span>
                                <select wire:model.live="sedeId" class="w-full bg-transparent border-none text-sm sm:text-base font-black text-white italic uppercase tracking-tight focus:ring-0 cursor-pointer [color-scheme:dark] [&>option]:bg-[#1e1e2d] [&>option]:text-white p-0">
                                    @forelse($sedes as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre_sede }}</option>
                                    @empty
                                    <option value="">Seleccione empresa</option>
                                    @endforelse
                                </select>

                                @if($activeSede)
                                <div class="flex flex-col gap-2 mt-2 pt-2 border-t border-white/5">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[8px] font-black rounded uppercase tracking-widest italic leading-none whitespace-nowrap">{{ $activeSede->rubro ?: 'General' }}</span>
                                        <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-white/40 text-[8px] font-black rounded uppercase tracking-[0.2em] italic leading-none whitespace-nowrap">{{ $activeSede->tipo_sede ?: 'GENERAL' }}</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-[8px] font-bold text-white/30 italic uppercase tracking-widest leading-none">{{ $activeSede->direccion ?: 'SD' }}, {{ $activeSede->localidad ?: 'SD' }}</span>
                                        @if($activeSede->latitud && $activeSede->longitud)
                                            <a href="https://maps.google.com/?q={{ $activeSede->latitud }},{{ $activeSede->longitud }}" target="_blank" class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-green-500/10 border border-green-500/20 text-green-400 hover:bg-green-500 hover:text-white rounded text-[7px] font-black uppercase tracking-widest italic transition-all group shrink-0" title="Ver en Google Maps">
                                                <svg class="w-2.5 h-2.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                MAPAS
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="h-px w-full xl:h-12 xl:w-px bg-white/10 shrink-0"></div>

                        <div class="flex flex-row items-center gap-4 sm:gap-8 w-full xl:w-auto shrink-0 bg-[#1e1e2d] p-3 sm:px-5 sm:py-3 rounded-2xl border border-white/5 object-contain">
                            <div class="flex flex-col w-full">
                                <span class="text-[8px] sm:text-[9px] font-black text-indigo-400 uppercase tracking-[0.4em] italic mb-1 block">Ventana</span>
                                <input type="month" wire:model.live="contractStart" class="bg-transparent border-none text-sm sm:text-base font-black text-white italic uppercase tracking-tight focus:ring-0 cursor-pointer [color-scheme:dark] p-0 w-full min-w-[120px]">
                            </div>
                            <div class="h-8 w-px bg-white/10"></div>
                            <div class="flex flex-col w-full">
                                <span class="text-[8px] sm:text-[9px] font-black text-indigo-400 uppercase tracking-[0.4em] italic mb-1 block">Frecuencia</span>
                                <select wire:model.live="contractDuration" class="bg-transparent border-none text-sm sm:text-base font-black text-white italic uppercase tracking-tight focus:ring-0 cursor-pointer [color-scheme:dark] [&>option]:bg-[#1e1e2d] [&>option]:text-white p-0 min-w-[100px]">
                                    <option value="3">3 Meses</option>
                                    <option value="6">6 Meses</option>
                                    <option value="12">Anual (12)</option>
                                    <option value="24">Bianual (24)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto shrink-0 mt-6 lg:mt-0 lg:ml-auto">
                        @if(auth()->user()->isAdmin())
                        <button wire:click="$toggle('editMode')" class="flex-1 sm:flex-none h-12 sm:h-14 px-4 sm:px-8 rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-widest italic transition-all shadow-xl group/edit flex items-center justify-center gap-2 sm:gap-3 {{ $editMode ? 'bg-indigo-600 text-white' : 'bg-white/5 border border-white/10 text-white hover:bg-white/10' }}">
                            <svg class="w-4 h-4 {{ $editMode ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2.5" />
                            </svg>
                            <span class="hidden xs:inline">{{ $editMode ? 'Cerrar Plan' : 'Planificación' }}</span>
                            <span class="xs:hidden">{{ $editMode ? 'CERRAR' : 'EDITAR' }}</span>
                        </button>
                        @endif
                        <button wire:click="downloadPdf" class="flex-1 h-12 sm:h-14 px-4 sm:px-10 bg-white text-black rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-widest italic hover:scale-105 transition-all shadow-2xl flex items-center justify-center group/pdf active:scale-95">
                            <svg class="w-3.5 h-3.5 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="hidden sm:inline">Plan PDF</span>
                            <span class="sm:hidden ml-2">PDF</span>
                        </button>
                    </div>

                </div>

                {{-- STATUS DE SEDE REMOVED: CONSOLIDATED IN MAIN DASHBOARD --}}
            </div>

            {{-- FORMULARIO DE PLANIFICACIÓN --}}
            @if($editMode && auth()->user()->isAdmin())
            <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-3xl sm:rounded-[3.5rem] p-6 sm:p-10 mb-12 shadow-2xl animate-fade-in relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] mb-10 italic flex items-center">
                    <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    AÑADIR ACTIVIDAD AL PLAN MAESTRO
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-6 gap-8 items-end">
                    <div class="md:col-span-1">
                        <label class="block text-[8px] font-black text-indigo-100/30 mb-3 uppercase tracking-widest italic">Pilar</label>
                        <select wire:model.live="category" class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-sm text-white font-black focus:ring-2 focus:ring-indigo-500 transition-all [color-scheme:dark]">
                            <option value="Capacitación">Capacitación</option>
                            <option value="Inspección">Inspección</option>
                            <option value="Informe">Informe Técnico</option>
                        </select>
                    </div>

                    @if($category === 'Informe')
                    <div class="md:col-span-1 animate-fade-in">
                        <label class="block text-[8px] font-black text-indigo-100/30 mb-3 uppercase tracking-widest italic">Frecuencia</label>
                        <select wire:model="frequency" class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-sm text-indigo-400 font-black focus:ring-2 focus:ring-indigo-500 transition-all [color-scheme:dark]">
                            <option value="none">Manual</option>
                            <option value="mensual">Mensual</option>
                            <option value="bimestral">Bimestral</option>
                            <option value="trimestral">Trimestral</option>
                        </select>
                    </div>
                    @else
                    <div class="md:col-span-1 animate-fade-in">
                        <label class="block text-[8px] font-black text-indigo-100/30 mb-3 uppercase tracking-widest italic">Herramienta Check</label>
                        <select wire:model.live="checklist_id" class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-sm text-white font-black focus:ring-2 focus:ring-indigo-500 transition-all [color-scheme:dark]">
                            <option value="">— Vincular —</option>
                            @foreach($checklists as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block text-[8px] font-black text-indigo-100/30 mb-3 uppercase tracking-widest italic">Descripción de Actividad</label>
                        <input type="text" wire:model="activity_name" class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white font-black focus:ring-2 focus:ring-indigo-500 transition-all placeholder-white/5 uppercase italic">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[8px] font-black text-indigo-100/30 mb-3 uppercase tracking-widest italic">Código HQ / Registro</label>
                        <div class="flex gap-4">
                            <input type="text" wire:model="activity_code" placeholder="EP-HSE-{{ date('y') }}" class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white font-black focus:ring-2 focus:ring-indigo-500 transition-all placeholder-white/5 uppercase italic">
                            <button wire:click="addActivity" class="w-52 h-14 bg-indigo-600 text-white font-black text-[10px] uppercase tracking-[0.2em] italic rounded-2xl hover:bg-indigo-500 transition-all shadow-xl active:scale-95 group/save">
                                <span class="group-hover:scale-110 transition-transform inline-block underline decoration-2 underline-offset-4 decoration-white/20">REGISTRAR</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- THE MASTER COMPLIANCE MATRIX --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-t-3xl sm:rounded-t-[3.5rem] rounded-b-none border border-white/5 shadow-2xl relative overflow-hidden mb-12">
                <div class="px-5 sm:px-10 py-6 sm:py-8 border-b border-white/5 flex flex-col lg:flex-row items-center justify-between gap-6 sm:gap-10 bg-black/20">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center rounded-xl font-black italic">M</div>
                        <h4 class="text-xl font-black text-white italic uppercase tracking-tighter leading-none">MATRIZ OPERATIVA ANUAL</h4>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-10 gap-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_10px_#10b981]"></div>
                            <span class="text-[9px] font-black text-emerald-400/80 uppercase tracking-widest italic">CONFORME</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-1.5 rounded-full bg-yellow-400 shadow-[0_0_10px_#fbbf24]"></div>
                            <span class="text-[9px] font-black text-yellow-400/80 uppercase tracking-widest italic">PROYECTADO</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-1.5 rounded-full bg-red-500 shadow-[0_0_10px_#ef4444] animate-pulse"></div>
                            <span class="text-[9px] font-black text-red-500 uppercase tracking-widest italic">VENCIDO</span>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block overflow-x-hidden">
                    <table class="w-full border-collapse table-fixed">
                        <thead>
                            <tr class="bg-black/40">
                                <th class="sticky left-0 z-30 bg-black/80 backdrop-blur-xl px-4 py-6 text-left border-r border-white/5 w-[25%]">
                                    <span class="text-[9px] font-black text-ep-primary uppercase tracking-[0.2em] italic">ACTIVIDAD / CÓDIGO</span>
                                </th>

                                @foreach($dynamicMonths as $monthValue => $info)
                                <th class="px-0 py-6 text-center w-[5.8%] border-r border-white/5 last:border-r-0">
                                    <span class="text-[11px] font-black text-white uppercase italic tracking-tight">
                                        {{ $info['short'] }} {{ substr($info['year'], 2) }}
                                    </span>
                                </th>
                                @endforeach

                                @if($editMode)
                                <th class="w-[50px] bg-black/40"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @php $currentCategory = ''; @endphp
                            @forelse($plans as $plan)
                            @if($currentCategory !== $plan->category)
                            <tr class="bg-indigo-600/5">
                                <td colspan="{{ $editMode ? 14 : 13 }}" class="px-6 py-3 text-[9px] font-black text-indigo-400 uppercase tracking-[0.4em] italic leading-none">
                                    <div class="flex items-center gap-4">
                                        <div class="w-3 h-3 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center">
                                            <span class="text-[7px]">{{ substr($plan->category, 0, 1) }}</span>
                                        </div>
                                        {{ strtoupper($plan->category) }}
                                    </div>
                                </td>
                            </tr>
                            @php $currentCategory = $plan->category; @endphp
                            @endif
                            <tr class="group/row hover:bg-white/[0.03] transition-all duration-700">
                                <td class="sticky left-0 z-20 bg-[#1e1e2d] group-hover/row:bg-[#252535] px-3 py-4 border-r border-white/5 overflow-hidden">
                                    <div class="flex items-center gap-2">
                                        <div class="shrink-0 w-1 h-6 {{ $plan->category === 'Capacitación' ? 'bg-blue-500' : ($plan->category === 'Inspección' ? 'bg-orange-500' : 'bg-ep-primary') }} rounded-full shadow-lg group-hover/row:h-8 transition-all duration-700"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs sm:text-sm font-black text-white italic uppercase truncate leading-tight group-hover/row:text-ep-primary transition-colors" title="{{ $plan->activity_name }}">
                                                {{ $plan->activity_name }}
                                            </div>
                                            <div class="text-[9px] sm:text-[10px] text-indigo-100/30 font-bold uppercase truncate leading-none mt-0.5 group-hover/row:text-indigo-100/50 transition-colors">
                                                {{ $plan->activity_code ?: 'S/C' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @foreach($dynamicMonths as $monthValue => $info)
                                @php
                                $data = $complianceData[$plan->id][$monthValue] ?? ['status' => 'none', 'count' => 0];
                                $status = $data['status'];
                                $cnt = $data['count'];
                                $activeVisit = $this->getActiveVisit();
                                $isCurrentMonth = $monthValue === date('Y-m');
                                $canComplete = !$editMode && ($status === 'pending' || $status === 'overdue') && $activeVisit && $isCurrentMonth;
                                @endphp
                                <td class="px-0 py-4 text-center border-r border-white/5 last:border-r-0 relative group/cell">
                                    <div
                                        @if($editMode)
                                        wire:click="toggleMonth({{ $plan->id }}, '{{ $monthValue }}')"
                                        @elseif($canComplete)
                                        wire:click="redirectToTask({{ $activeVisit->id }}, '{{ addslashes($plan->activity_name) }}', {{ $plan->checklist_id ?: 'null' }}, '{{ $plan->category }}')"
                                        @endif
                                        class="relative mx-auto w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-500 overflow-visible {{ $canComplete ? 'cursor-pointer hover:scale-110' : 'cursor-default' }}
                                                {{ $status === 'completed' ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 shadow-[inset_0_0_10px_rgba(16,185,129,0.1)] hover:bg-emerald-500/20' : '' }}
                                                {{ $status === 'overdue' ? 'bg-red-500/10 border border-red-500/30 text-red-500 animate-pulse' : '' }}
                                                {{ $status === 'pending' ? 'bg-yellow-400/10 border border-yellow-500/30 text-yellow-400 hover:bg-yellow-400/20' : '' }}
                                                {{ $status === 'none' ? 'opacity-10' : '' }}
                                                {{ $editMode ? 'cursor-pointer hover:scale-110 border-dashed border-2' : '' }}">


                                        @if($status === 'completed')
                                        <svg class="w-3.5 h-3.5 drop-shadow-[0_0_8px_#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                        @elseif($status === 'pending')
                                        <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full shadow-[0_0_8px_#fbbf24]"></div>
                                        @elseif($status === 'overdue')
                                        <span class="text-[10px] font-black drop-shadow-[0_0_8px_#ef4444]">!</span>
                                        @else
                                        <div class="h-0.5 w-0.5 bg-white/20 rounded-full"></div>
                                        @endif

                                        {{-- SIMPLIFIED CONTRACTUAL HOVER CARD --}}
                                        @if(!$editMode && $status !== 'none')
                                        <div class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 w-52 opacity-0 invisible group-hover/cell:opacity-100 group-hover/cell:visible transition-all duration-300 z-[100] pointer-events-none">
                                            <div class="bg-[#1e1e2d]/98 backdrop-blur-2xl border border-white/10 rounded-2xl p-4 shadow-[0_20px_60px_rgba(0,0,0,0.6)] relative overflow-hidden">
                                                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

                                                <div class="flex items-center justify-between mb-3 border-b border-white/5 pb-2">
                                                    <div class="text-[8px] font-black text-indigo-100/50 uppercase tracking-widest italic">{{ $info['name'] }} {{ $info['year'] }}</div>
                                                    <span class="text-[7px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-black uppercase italic">CUMPLIDO</span>
                                                </div>

                                                <div class="space-y-2.5">
                                                    @if(($data['details']['type'] ?? '') === 'inspección')
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-[9px] font-bold text-white/40 uppercase">Resultados</span>
                                                        <div class="flex gap-2">
                                                            <span class="text-[9px] font-black text-emerald-400 flex items-center gap-1 bg-emerald-500/5 px-1.5 py-0.5 rounded border border-emerald-500/10">
                                                                ✅ {{ $data['details']['passed'] ?? 0 }}
                                                            </span>
                                                            <span class="text-[9px] font-black text-red-400 flex items-center gap-1 bg-red-500/5 px-1.5 py-0.5 rounded border border-red-500/10">
                                                                ❌ {{ $data['details']['failed'] ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-[9px] font-bold text-white/40 uppercase italic">Hallazgos</span>
                                                        <span class="text-[10px] font-black text-yellow-500">🚩 {{ $data['details']['findings'] ?? 0 }}</span>
                                                    </div>
                                                    @elseif(($data['details']['type'] ?? '') === 'capacitación')
                                                    <div class="p-2 rounded-xl bg-blue-500/5 border border-blue-500/10 flex flex-col items-center gap-1">
                                                        <span class="text-[8px] font-black text-blue-400/60 uppercase tracking-tighter italic">Personas Capacitadas</span>
                                                        <span class="text-xl font-black text-blue-400 tracking-tighter italic">{{ $data['details']['attendees'] ?? 0 }}</span>
                                                    </div>
                                                    @else
                                                    <div class="flex items-center justify-center p-2">
                                                        <span class="text-[9px] font-black text-emerald-400 italic">EJECUCIÓN CONFORME</span>
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- Pointer arrow --}}
                                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-[#1e1e2d] border-r border-b border-white/10 rotate-45"></div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                @endforeach

                                @if($editMode)
                                <td class="px-2 text-center align-middle">
                                    <button wire:confirm="¿Seguro que deseas eliminar esta actividad?"
                                        wire:click="deleteActivity({{ $plan->id }})"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500/40 hover:text-red-500 hover:bg-red-500/10 transition-all duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="px-10 py-40 text-center">
                                    <div class="h-24 w-24 bg-white/5 rounded-[3rem] border border-white/5 flex items-center justify-center mx-auto mb-8 text-indigo-100/10">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-black text-indigo-100/20 uppercase tracking-[0.5em] italic mb-3">MATRIZ DESIERTA</h3>
                                    <p class="text-xs text-indigo-100/10 font-bold uppercase tracking-widest italic max-w-md mx-auto leading-relaxed">No se han detectado compromisos planificados para esta sede en el ciclo operativo de {{ date('M Y', strtotime($contractStart)) }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE TACTICAL CARDS (Exclusive for small screens) --}}
                <div class="md:hidden">
                    @php $currentCategoryMob = ''; @endphp
                    @forelse($plans as $plan)
                    @if($currentCategoryMob !== $plan->category)
                    <div class="px-6 py-4 bg-indigo-600/10 text-[10px] font-black text-indigo-400 uppercase tracking-widest italic border-y border-white/5">
                        {{ strtoupper($plan->category) }}
                    </div>
                    @php $currentCategoryMob = $plan->category; @endphp
                    @endif

                    <div class="p-6 border-b border-white/5 hover:bg-white/[0.02] transition-colors group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex-1">
                                <h5 class="text-xs font-black text-white uppercase italic tracking-tight mb-1 group-hover:text-ep-primary transition-colors leading-tight">{{ $plan->activity_name }}</h5>
                                <div class="text-[9px] text-indigo-100/20 font-bold uppercase tracking-widest italic">{{ $plan->activity_code ?: 'SIN CÓDIGO' }}</div>
                            </div>
                            @if($editMode)
                            <button wire:click="deleteActivity({{ $plan->id }})" class="p-2.5 bg-red-500/10 text-red-500 rounded-xl border border-red-500/20 active:scale-90">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" />
                                </svg>
                            </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mb-2">
                            @foreach($dynamicMonths as $monthValue => $info)
                            @php
                            $data = $complianceData[$plan->id][$monthValue] ?? ['status' => 'none', 'count' => 0];
                            $status = $data['status'];
                            $cnt = $data['count'];
                            $hasIncidents = ($data['details']['incidents'] ?? 0) > 0;
                            $hasFindings = ($data['details']['findings'] ?? 0) > 0;
                            $isFailed = ($data['details']['failed'] ?? 0) > 0;
                            @endphp
                            <div
                                @if($editMode) wire:click="toggleMonth({{ $plan->id }}, '{{ $monthValue }}')" @endif
                                class="aspect-square rounded-xl flex flex-col items-center justify-center relative overflow-hidden transition-all duration-300
                                                {{ $status === 'completed' ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 active:bg-emerald-500/20' : '' }}
                                                {{ $status === 'overdue' ? 'bg-red-500/10 border border-red-500/30 text-red-500 active:bg-red-500/20' : '' }}
                                                {{ $status === 'pending' ? 'bg-yellow-400/10 border border-yellow-500/30 text-yellow-400 active:bg-yellow-400/20' : '' }}
                                                {{ $status === 'none' ? 'bg-white/5 border border-white/5' : '' }}
                                                {{ $editMode ? 'active:scale-90' : 'active:scale-110' }}">

                                <span class="text-[7px] font-black uppercase tracking-tighter opacity-40 mb-0.5">{{ $info['short'] }} '{{ substr($info['year'], 2) }}</span>

                                <div class="flex items-center gap-0.5">
                                    @if($status === 'completed')
                                    @if($isFailed)
                                    <svg class="w-2 h-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                                        <path d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    @else
                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981]"></div>
                                    @endif
                                    @elseif($status === 'overdue')
                                    <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_#ef4444]"></div>
                                    @elseif($status === 'pending')
                                    <div class="w-1.5 h-1.5 rounded-full bg-yellow-400/50"></div>
                                    @else
                                    <div class="w-1 h-1 rounded-full bg-white/10"></div>
                                    @endif

                                    @if($hasIncidents)
                                    <div class="w-1 h-1 rounded-full bg-red-500 animate-ping absolute top-1 right-1"></div>
                                    <div class="w-1 h-1 rounded-full bg-red-500 absolute top-1 right-1"></div>
                                    @endif

                                    @if($hasFindings && $status === 'completed')
                                    <div class="w-1 h-1 rounded-full bg-yellow-400 absolute bottom-1 right-1"></div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center text-indigo-100/20 italic text-[11px] font-black uppercase tracking-widest">
                        No hay actividades programadas
                    </div>
                    @endforelse
                </div>
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
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.2);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.4);
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes fade-in-down {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 1s ease-out forwards;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .group\/cell:hover svg,
        .group\/cell:hover span {
            animation: pulse-tactical 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }

        @keyframes pulse-tactical {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(0.95);
            }
        }
    </style>
</div>