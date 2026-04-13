<div class="w-full mx-auto">
    @section('header_custom')
    <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg border-b border-white/5">
        <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <div class="flex flex-col">
                    <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none">
                        GENERADOR <span class="text-white">PLAN OPERATIVO</span>
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @if($efficiency['counts']['overdue'] > 0)
                <div class="hidden md:flex px-3 py-1 bg-red-500/10 border border-red-500/30 rounded-lg animate-pulse items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                    <span class="text-[9px] font-black text-red-400 uppercase tracking-widest italic">{{ $efficiency['counts']['overdue'] }} ALERTAS</span>
                </div>
                @endif
                <a href="{{ route('profile') }}" class="flex items-center gap-2 bg-white/5 p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                    <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black">{{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}</div>
                    <span class="text-[10px] font-bold text-indigo-100 italic truncate max-w-[80px]">{{ auth()->user()->nombres }}</span>
                </a>
            </div>
        </div>
    </header>
    @endsection

    <div class="px-2 sm:px-6 lg:px-8 py-8 text-white selection:bg-ep-primary/30">
        <div class="max-w-[98rem] mx-auto">

            {{-- CONTROLES MAESTROS REDISEÑADOS --}}
            <div wire:loading.class="opacity-70 pointer-events-none" class="relative z-50 bg-ep-dark/40 backdrop-blur-md rounded-[2rem] border border-white/5 p-4 sm:p-6 shadow-2xl mb-8 transition-all duration-300">
                <div class="flex flex-col xl:flex-row gap-6 items-stretch xl:items-center">

                    {{-- Bloque Izquierdo: Selección de Activo --}}
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center gap-5 group transition-all">
                            <div class="h-12 w-12 shrink-0 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner rotate-3 group-hover:rotate-0 transition-all duration-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <x-custom-select 
                                    label="Empresa Mandante" 
                                    model="selectedCompanyId" 
                                    :options="$companyOptions" 
                                    placeholder="Seleccione Empresa"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-5 group transition-all">
                            <div class="h-12 w-12 shrink-0 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner rotate-3 group-hover:rotate-0 transition-all duration-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <x-custom-select 
                                    label="Activo / Sede Operativa" 
                                    model="sedeId" 
                                    :options="$sedeOptions" 
                                    placeholder="Seleccione Sede"
                                />
                            </div>
                        </div>
                    </div>

                    {{-- Bloque Central: Ventana y Frecuencia --}}
                    <div class="flex flex-row items-center gap-4 bg-indigo-600/10 p-3 rounded-2xl border border-indigo-500/20">
                        <div class="flex-1">
                            <label class="flex items-center gap-2 text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1 mb-3">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Inicio Plan
                            </label>
                            <div class="relative">
                                <input type="month" wire:model.live="contractStart" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all backdrop-blur-md [color-scheme:dark] cursor-pointer">
                            </div>
                        </div>
                        <div class="w-px h-8 bg-indigo-500/20"></div>
                        <div class="flex-1">
                            <x-custom-select 
                                label="Periodo" 
                                model="contractDuration" 
                                :options="$durationOptions" 
                            />
                        </div>
                    </div>

                    {{-- Bloque Derecho: Acciones (Aquí se arregla el botón PDF) --}}
                    <div class="flex flex-row gap-3">
                        @if(auth()->user()->isAdmin())
                        <button wire:click="$toggle('editMode')" class="flex-1 xl:flex-none h-12 px-6 rounded-xl font-black text-[10px] uppercase tracking-widest italic transition-all flex items-center justify-center gap-2 {{ $editMode ? 'bg-indigo-600 text-white shadow-[0_0_20px_rgba(79,70,229,0.4)]' : 'bg-white/5 border border-white/10 text-white hover:bg-white/10' }}">
                            <svg class="w-4 h-4 {{ $editMode ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" />
                            </svg>
                            <span>{{ $editMode ? 'GUARDAR' : 'PLANIFICAR' }}</span>
                        </button>
                        @endif
                        <button wire:click="downloadPdf" class="flex-1 xl:flex-none h-12 px-6 bg-white text-black rounded-xl font-black text-[10px] uppercase tracking-widest italic hover:bg-ep-primary hover:text-white transition-all shadow-xl flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>PDF</span>
                        </button>
                    </div>
                </div>

                {{-- Hub de Información Táctica (Dirección, Teléfonos, GPS) --}}
                @if($activeSede)
                <div class="mt-4 pt-4 border-t border-white/5 flex flex-wrap items-center justify-between gap-6">
                        {{-- Grupo Identidad de Sede y Equipo (Dirección, GPS, Teléfono + Técnicos) --}}
                        <div class="flex items-center gap-3 bg-white/5 px-4 py-2 rounded-2xl border border-white/5 shadow-inner overflow-hidden">
                            
                            <!-- BLOQUE 1: DIRECCIÓN Y TELÉFONO DE LA SEDE -->
                            <div class="flex items-center gap-3 pr-3 border-r border-white/10 shrink-0">
                                <div class="h-8 w-8 bg-ep-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-white/90 uppercase tracking-tight italic leading-none">
                                        {{ $activeSede->direccion ?? 'DIRECCIÓN NO DISPONIBLE' }}
                                    </span>
                                    @if($activeSede->telefono)
                                    <a href="tel:{{ $activeSede->telefono }}" class="flex items-center gap-1 mt-1 group/tel">
                                        <svg class="w-2.5 h-2.5 text-white/30 group-hover/tel:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke-width="2"/>
                                        </svg>
                                        <span class="text-[8px] font-bold text-white/40 italic group-hover/tel:text-white/70 transition-colors">{{ $activeSede->telefono }}</span>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            <!-- BLOQUE 2: BOTÓN MAPS -->
                            <div class="pr-3 border-r border-white/10 shrink-0">
                                @if($activeSede->latitud && $activeSede->longitud)
                                <a href="https://maps.google.com/?q={{ $activeSede->latitud }},{{ $activeSede->longitud }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 bg-ep-primary/20 border border-ep-primary/30 text-ep-primary rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-ep-primary hover:text-white transition-all italic group">
                                    <svg class="w-3 h-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                    </svg>
                                    MAPS
                                </a>
                                @endif
                            </div>

                            <!-- BLOQUE 3: EQUIPO TÉCNICO + WHATSAPP -->
                            <div class="flex items-center gap-3 pl-1 overflow-x-auto no-scrollbar">
                                <span class="text-[8px] font-black text-indigo-400 uppercase italic tracking-widest shrink-0">EQUIPO:</span>
                                <div class="flex items-center gap-2">
                                    @foreach($activeSede->users->where('rol', 'tecnico') as $tech)
                                    <div class="flex items-center gap-2 bg-indigo-500/10 px-3 py-1 rounded-xl border border-indigo-500/20 group/tech shrink-0">
                                        <span class="text-[9px] font-black text-white/80 uppercase italic">{{ $tech->full_name }}</span>
                                        
                                        <div class="flex items-center gap-2 ml-1 border-l border-white/10 pl-2">
                                            @if($tech->telefono)
                                            <!-- Solo WhatsApp para el técnico -->
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tech->telefono) }}" target="_blank" class="text-emerald-500 hover:text-emerald-400 hover:scale-110 transition-all" title="WhatsApp">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path>
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- FORMULARIO DE PLANIFICACIÓN (SIN CAMBIOS ESTRUCTURALES, SOLO PADDINGS) --}}
            @if($editMode && auth()->user()->isAdmin())
            <div class="relative z-40 bg-indigo-600/5 border border-indigo-500/20 rounded-[2.5rem] p-6 mb-8 animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    <div class="md:col-span-1">
                        <x-custom-select 
                            label="Pilar" 
                            model="category" 
                            :options="$categoryOptions" 
                        />
                    </div>
                    <div class="md:col-span-1">
                        <x-custom-select 
                            label="Vínculo" 
                            model="checklist_id" 
                            :options="$checklistOptions" 
                            placeholder="Ninguno"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-indigo-100/40 mb-2 uppercase italic tracking-[0.2em] ml-1">Actividad</label>
                        <input type="text" wire:model="activity_name" placeholder="Ej: Control de Plagas" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-xs text-white font-black uppercase italic focus:ring-2 focus:ring-ep-primary/50 transition-all outline-none">
                    </div>
                    <div class="md:col-span-2 flex gap-3">
                        <div class="flex-1">
                            <label class="block text-[10px] font-black text-indigo-100/40 mb-2 uppercase italic tracking-[0.2em] ml-1">Código</label>
                            <input type="text" wire:model="activity_code" placeholder="EP-HSE-01" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-xs text-white font-black uppercase italic focus:ring-2 focus:ring-ep-primary/50 transition-all outline-none">
                        </div>
                        <button wire:click="addActivity" class="h-[52px] px-8 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 self-end">
                            REGISTRAR
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- MATRIZ OPERATIVA (CON table-fixed PARA EVITAR DESBORDE) --}}
            <div wire:loading.class="opacity-50" class="relative z-30 bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 shadow-2xl mb-12 transition-all duration-500">
                {{-- Overlay de carga sutil --}}
                <div wire:loading class="absolute inset-0 z-[100] flex items-center justify-center bg-ep-dark/20 backdrop-blur-[2px] rounded-[2.5rem]">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 h-10 border-4 border-ep-primary border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-[10px] font-black text-white uppercase tracking-[0.3em] italic animate-pulse">Sincronizando...</span>
                    </div>
                </div>
                <div class="px-6 py-5 border-b border-white/5 flex items-center justify-between bg-black/20">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-6 bg-ep-primary rounded-full"></div>
                        <h4 class="text-sm font-black text-white italic uppercase tracking-widest">Matriz de Cumplimiento Operativo</h4>
                    </div>
                    {{-- Leyenda Simplificada para evitar que ocupe mucho espacio --}}
                    <div class="hidden sm:flex gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div><span class="text-[8px] font-bold text-white/40 uppercase">OK</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-yellow-400"></div><span class="text-[8px] font-bold text-white/40 uppercase">PEND</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div><span class="text-[8px] font-bold text-white/40 uppercase">ALERTA</span>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block overflow-x-hidden">
                    <table class="w-full border-collapse table-fixed">
                        <thead>
                            <tr class="bg-black/20">
                                <th class="sticky left-0 z-30 bg-[#11111D] px-6 py-6 text-left border-r border-white/5 w-[25%] shadow-[5px_0_15px_rgba(0,0,0,0.3)]">
                                    <span class="text-[10px] font-black text-ep-primary uppercase tracking-[0.2em] italic">ACTIVIDAD / REGISTRO</span>
                                </th>
                                @foreach($dynamicMonths as $monthValue => $info)
                                <th class="px-0 py-6 text-center w-[5.8%] border-r border-white/5 last:border-r-0">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black text-white italic uppercase tracking-tight leading-none">{{ $info['short'] }}</span>
                                        <span class="text-[8px] font-black text-indigo-100/20 uppercase tracking-[0.2em] mt-1.5 font-mono italic">/ {{ substr($info['year'], 2) }}</span>
                                    </div>
                                </th>
                                @endforeach
                                @if($editMode) <th class="w-[50px] bg-black/20"></th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @php $currentCategory = ''; @endphp
                            @forelse($plans as $plan)
                                {{-- Grupo de Categoría --}}
                                @if($currentCategory !== $plan->category)
                                <tr wire:key="category-header-{{ $plan->category }}" class="bg-indigo-600/5">
                                    <td colspan="{{ $editMode ? 14 : 13 }}" class="px-6 py-2 text-[9px] font-black text-indigo-400 uppercase italic tracking-[0.3em]">
                                        {{ strtoupper($plan->category) }}
                                    </td>
                                </tr>
                                @php $currentCategory = $plan->category; @endphp
                                @endif

                                <tr wire:key="plan-row-{{ $plan->id }}" class="group/row hover:bg-white/[0.02] transition-colors">
                                <td class="sticky left-0 z-20 bg-[#1e1e2d] px-4 py-4 border-r border-white/5">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <div class="shrink-0 w-1 h-5 {{ $plan->category === 'Capacitación' ? 'bg-blue-500' : ($plan->category === 'Inspección' ? 'bg-orange-500' : 'bg-ep-primary') }} rounded-full"></div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <div class="text-[11px] font-black text-white italic uppercase truncate" title="{{ $plan->activity_name }}">{{ $plan->activity_name }}</div>
                                                @if($plan->frequency !== 'none')
                                                <span class="px-1.5 py-0.5 bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 text-[7px] font-black rounded uppercase italic">{{ $plan->frequency }}</span>
                                                @endif
                                            </div>
                                            <div class="text-[8px] text-white/20 font-bold truncate tracking-widest">{{ $plan->activity_code ?: 'SIN CÓDIGO' }}</div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($dynamicMonths as $monthValue => $info)
                                @php
                                $data = $complianceData[$plan->id][$monthValue] ?? ['status' => 'none', 'count' => 0];
                                $status = $data['status'];
                                @endphp
                                <td wire:key="plan-{{ $plan->id }}-month-{{ $monthValue }}" class="px-0 py-4 text-center border-r border-white/[0.03] last:border-r-0">
                                    @php
                                        $scheduledDates = $plan->scheduled_dates ?? [];
                                        $cellDate       = $scheduledDates[$monthValue] ?? null;
                                        $cellKey        = "{$plan->id}_{$monthValue}";
                                    @endphp
                                    <div class="relative mx-auto w-7 flex flex-col items-center gap-0.5 group/cell">
                                        {{-- Main cell dot: click toggles activation --}}
                                        <div
                                            @if($editMode) wire:click="toggleMonth({{ $plan->id }}, '{{ $monthValue }}')" @endif
                                            class="w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-300
                                                    {{ $status === 'completed' ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400' : '' }}
                                                    {{ $status === 'overdue' ? 'bg-red-500/10 border border-red-500/30 text-red-500 animate-pulse' : '' }}
                                                    {{ $status === 'pending' ? 'bg-yellow-400/10 border border-yellow-500/30 text-yellow-400' : '' }}
                                                    {{ $status === 'none' ? 'opacity-5' : '' }}
                                                    {{ $editMode ? 'cursor-pointer hover:scale-110 border-dashed border-2' : '' }}">
                                            @if($status === 'completed')
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                                                    <path d="M5 13l4 4L19 7" />
                                                </svg>
                                            @elseif($status === 'pending')
                                            <div class="w-1 h-1 bg-yellow-400 rounded-full"></div>
                                            @elseif($status === 'overdue')
                                            <span class="text-[10px] font-black">!</span>
                                            @else
                                            <div class="h-px w-1 bg-white/20"></div>
                                            @endif
                                        </div>

                                        {{-- Tooltip Informativo (Hover Card) --}}
                                        @if($status === 'completed' && !$editMode)
                                        <div class="absolute z-[60] bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 bg-ep-dark/95 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-[0_20px_50px_rgba(0,0,0,0.5)] opacity-0 invisible group-hover/cell:opacity-100 group-hover/cell:visible transition-all duration-300 pointer-events-none translate-y-2 group-hover/cell:translate-y-0">
                                            <div class="flex items-center justify-between mb-3 border-b border-white/5 pb-2">
                                                <span class="text-[8px] font-black text-ep-primary uppercase tracking-widest italic">{{ $info['short'] }} {{ $info['year'] }}</span>
                                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[7px] font-black rounded-full border border-emerald-500/30 uppercase italic">Ejecutado</span>
                                            </div>

                                            <div class="space-y-2">
                                                {{-- 1. Fecha de Ejecución --}}
                                                <div class="flex items-center justify-between px-3 py-2 bg-white/5 rounded-xl border border-white/5">
                                                    <div class="flex flex-col">
                                                        <span class="text-[6px] font-black text-white/30 uppercase tracking-widest leading-none mb-1">Ejecución</span>
                                                        <span class="text-[9px] font-black text-white italic tracking-tight leading-none">{{ $data['details']['execution_date'] ?? 'Pendiente' }}</span>
                                                    </div>
                                                    <svg class="w-3.5 h-3.5 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2.5"/></svg>
                                                </div>

                                                {{-- 2. Tiempo de Demora --}}
                                                <div class="flex items-center justify-between px-3 py-2 bg-white/5 rounded-xl border border-white/5">
                                                    <div class="flex flex-col">
                                                        <span class="text-[6px] font-black text-white/30 uppercase tracking-widest leading-none mb-1">Duración</span>
                                                        <span class="text-[9px] font-black text-indigo-400 italic tracking-tight leading-none">
                                                            @if(($data['details']['duration'] ?? 0) > 0)
                                                                {{ $data['details']['duration'] }} <span class="text-[7px] font-bold">MIN</span>
                                                            @else
                                                                S/D
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <svg class="w-3.5 h-3.5 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg>
                                                </div>

                                                {{-- 3. Asistentes (Solo Capacitación) --}}
                                                @if(($data['details']['type'] ?? '') === 'capacitación' && ($data['details']['attendees'] ?? 0) > 0)
                                                <div class="flex items-center justify-between px-3 py-2 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                                                    <span class="text-[7px] font-black text-blue-400 uppercase italic">Asistentes Registrados</span>
                                                    <span class="text-[9px] font-black text-white">{{ $data['details']['attendees'] }}</span>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            {{-- Flechita del Tooltip --}}
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-ep-dark/95"></div>
                                        </div>
                                        @endif

                                        {{-- Date badge below dot — visible on activated cells --}}
                                        @if($status !== 'none')
                                        <button
                                            @if($editMode) wire:click.stop="openDatePicker({{ $plan->id }}, '{{ $monthValue }}')" @endif
                                            title="{{ $cellDate ? 'Fecha: '.date('d/m', strtotime($cellDate)).' — Click para editar' : ($editMode ? 'Sin fecha — Click para asignar' : '') }}"
                                            class="text-[9px] font-black leading-none px-1.5 py-0.5 rounded-md transition-all
                                                   {{ $cellDate ? 'text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 hover:text-white cursor-pointer border border-indigo-500/20' : ($editMode ? 'text-white/25 hover:text-white/60 cursor-pointer' : 'text-transparent cursor-default') }}">
                                            {{ $cellDate ? date('d/m', strtotime($cellDate)) : ($editMode ? '···' : '') }}
                                        </button>
                                        @endif

                                        {{-- Inline date picker overlay --}}
                                        @if($editingCell === $cellKey)
                                        <div class="absolute z-50 bottom-full mb-1 left-1/2 -translate-x-1/2 w-48 bg-[#1e1e2d] border border-indigo-500/40 rounded-xl p-3 shadow-2xl"
                                             wire:click.stop>
                                            <span class="text-[8px] font-black text-indigo-400 uppercase italic block mb-2">Fecha de ejecución</span>
                                            <input type="date"
                                                   wire:model="editingDate"
                                                   min="{{ \Carbon\Carbon::createFromFormat('Y-m', $monthValue)->startOfMonth()->format('Y-m-d') }}"
                                                   max="{{ \Carbon\Carbon::createFromFormat('Y-m', $monthValue)->endOfMonth()->format('Y-m-d') }}"
                                                   class="text-xs w-full bg-black/40 rounded-lg px-2 py-1.5 text-white border border-white/10 [color-scheme:dark] focus:outline-none focus:border-indigo-500/60">
                                            @error('editingDate')
                                            <p class="text-red-400 text-[7px] mt-1">{{ $message }}</p>
                                            @enderror
                                            <div class="flex gap-1 mt-2">
                                                <button wire:click.stop="saveScheduledDate"
                                                        class="flex-1 text-[8px] font-black uppercase italic bg-indigo-600 hover:bg-indigo-500 rounded-lg py-1 text-white transition-all">
                                                    GUARDAR
                                                </button>
                                                <button wire:click.stop="$set('editingCell', null)"
                                                        class="text-[8px] font-black uppercase italic bg-white/5 hover:bg-white/10 rounded-lg px-2 py-1 text-white/60 transition-all">
                                                    ✕
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                @endforeach
                                @if($editMode)
                                <td class="text-center">
                                    <button wire:click="deleteActivity({{ $plan->id }})" class="text-red-500/20 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" />
                                        </svg>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="py-20 text-center text-white/10 font-black italic tracking-widest uppercase">No hay actividades registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE VIEW --}}
                <div class="md:hidden divide-y divide-white/5">
                    @forelse($plans as $plan)
                    <div class="p-4 bg-black/10">
                        <div class="text-[10px] font-black text-white uppercase italic truncate">{{ $plan->activity_name }}</div>
                        <div class="grid grid-cols-6 gap-2 mt-3">
                            @foreach($dynamicMonths as $monthValue => $info)
                            @php $status = $complianceData[$plan->id][$monthValue]['status'] ?? 'none'; @endphp
                            <div class="aspect-square rounded-md border flex flex-col items-center justify-center text-[8px] font-black
                                        {{ $status === 'completed' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500' : 'bg-white/5 border-white/5 text-white/20' }}">
                                {{ $info['short'] }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-white/10 text-xs">Sin actividades</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>