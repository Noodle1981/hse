<div class="w-full mx-auto">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic mb-1 leading-none">
                            Cumplimiento <span class="text-white">Plan Anual</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                            Ciclo Operativo {{ $year }}
                        </h2>
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

    <div class="px-4 sm:px-6 lg:px-8 py-10 text-white selection:bg-ep-primary/30">
        <div class="max-w-[98rem] mx-auto">
            
            {{-- INDICATORS REMOVED: NOW CENTRALIZED IN MAIN DASHBOARD --}}

            {{-- MASTER CONTROLS --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                {{-- Selector de Sede --}}
                <div class="lg:col-span-8 bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative flex flex-col md:flex-row items-center justify-between gap-10">
                    <div class="flex items-center gap-6 w-full md:w-auto">
                        <div class="h-14 w-14 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-ep-primary shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <span class="text-[9px] font-black text-ep-primary uppercase tracking-[0.4em] italic mb-1 block">Selección de Activos</span>
                            <select wire:model.live="sedeId" class="w-full bg-transparent border-none text-xl font-black text-white italic uppercase tracking-tight focus:ring-0 cursor-pointer [color-scheme:dark] [&>option]:bg-[#1e1e2d] [&>option]:text-white">
                                @foreach($sedes as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 w-full md:w-auto">
                        @if(auth()->user()->isAdmin())
                            <button wire:click="$toggle('editMode')" class="h-14 px-8 rounded-2xl font-black text-[10px] uppercase tracking-widest italic transition-all shadow-xl group/edit flex items-center gap-3 {{ $editMode ? 'bg-indigo-600 text-white' : 'bg-white/5 border border-white/10 text-white hover:bg-white/10' }}">
                                <svg class="w-4 h-4 {{ $editMode ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2.5"/></svg>
                                {{ $editMode ? 'Cerrar Planificación' : 'Modo Planificación' }}
                            </button>
                        @endif
                        <button wire:click="downloadPdf" class="flex-1 md:flex-none h-14 px-10 bg-white text-black rounded-2xl font-black text-[10px] uppercase tracking-widest italic hover:scale-105 transition-all shadow-2xl flex items-center justify-center group/pdf">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Plan PDF
                        </button>
                    </div>
                </div>

                {{-- STATUS DE SEDE REMOVED: CONSOLIDATED IN MAIN DASHBOARD --}}
            </div>

            {{-- FORMULARIO DE PLANIFICACIÓN --}}
            @if($editMode && auth()->user()->isAdmin())
            <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-[3.5rem] p-10 mb-12 shadow-2xl animate-fade-in relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] mb-10 italic flex items-center">
                    <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
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
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden mb-12">
                <div class="px-10 py-8 border-b border-white/5 flex flex-wrap items-center justify-between gap-10 bg-black/20">
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

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/40">
                                <th class="sticky left-0 z-30 bg-black/60 backdrop-blur-xl px-12 py-8 text-left border-r border-white/5">
                                    <span class="text-[10px] font-black text-ep-primary uppercase tracking-[0.4em] italic">ACTIVIDAD / CÓDIGO</span>
                                </th>
                                @foreach($months as $name => $num)
                                    <th class="px-4 py-8 text-center min-w-[80px]">
                                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.5em] italic">{{ substr(strtoupper($name), 0, 3) }}</span>
                                    </th>
                                @endforeach
                                @if($editMode) <th class="px-6 py-8 text-right bg-black/40"></th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @php $currentCategory = ''; @endphp
                            @forelse($plans as $plan)
                                @if($currentCategory !== $plan->category)
                                    <tr class="bg-indigo-600/5">
                                        <td colspan="15" class="px-12 py-4 text-[9px] font-black text-indigo-400 uppercase tracking-[0.4em] italic">
                                            <div class="flex items-center gap-4">
                                                <div class="w-4 h-4 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center">
                                                    <span class="text-[8px]">{{ substr($plan->category, 0, 1) }}</span>
                                                </div>
                                                {{ strtoupper($plan->category) }}
                                            </div>
                                        </td>
                                    </tr>
                                    @php $currentCategory = $plan->category; @endphp
                                @endif
                                <tr class="group/row hover:bg-white/[0.03] transition-all duration-700">
                                    <td class="sticky left-0 z-20 bg-[#1e1e2d] group-hover/row:bg-[#252535] px-12 py-8 transition-colors border-r border-white/5">
                                        <div class="flex items-center gap-6">
                                            <div class="w-1.5 h-10 {{ $plan->category === 'Capacitación' ? 'bg-blue-500' : ($plan->category === 'Inspección' ? 'bg-orange-500' : 'bg-ep-primary') }} rounded-full shadow-lg group-hover/row:h-12 transition-all duration-700"></div>
                                            <div>
                                                <div class="text-base font-black text-white italic uppercase tracking-tight leading-none mb-2 group-hover/row:text-ep-primary transition-colors">{{ $plan->activity_name }}</div>
                                                <div class="text-[10px] text-indigo-100/20 font-bold uppercase tracking-widest italic group-hover/row:text-indigo-100/40 transition-colors leading-none">{{ $plan->activity_code ?: 'PENDIENTE DE CÓDIGO' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @foreach($months as $name => $num)
                                        @php 
                                            $data = $complianceData[$plan->id][$name] ?? ['status' => 'none', 'count' => 0];
                                            $status = $data['status'];
                                            $cnt = $data['count'];
                                        @endphp
                                        <td class="px-2 py-8 text-center align-middle">
                                            <div 
                                                @if($editMode) wire:click="toggleMonth({{ $plan->id }}, '{{ $name }}')" @endif
                                                class="relative mx-auto w-12 h-12 rounded-[1.2rem] flex items-center justify-center transition-all duration-500 group/cell overflow-hidden
                                                {{ $status === 'completed' ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 shadow-[inset_0_0_15px_rgba(16,185,129,0.1)] scale-110' : '' }}
                                                {{ $status === 'overdue' ? 'bg-red-500/10 border border-red-500/30 text-red-500 animate-pulse' : '' }}
                                                {{ $status === 'pending' ? 'bg-yellow-400/10 border border-yellow-500/30 text-yellow-400' : '' }}
                                                {{ $status === 'none' ? 'opacity-10 grayscale' : '' }}
                                                {{ $editMode ? 'cursor-pointer hover:scale-125 hover:border-white/40 border-dashed border-2 border-white/5' : 'cursor-default' }}">
                                                
                                                @if($status !== 'none')
                                                    @if($status === 'completed')
                                                        <svg class="w-6 h-6 drop-shadow-[0_0_8px_#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                                                        @if($cnt > 1)
                                                            <span class="absolute top-1 right-1 bg-emerald-500 text-white text-[7px] font-black h-4 w-4 flex items-center justify-center rounded-lg shadow-lg">{{ $cnt }}</span>
                                                        @endif
                                                    @elseif($status === 'pending')
                                                        <div class="flex flex-col items-center">
                                                            <span class="text-[9px] font-black">PLAN</span>
                                                            <span class="w-4 h-1 bg-yellow-400/40 rounded-full mt-0.5"></span>
                                                        </div>
                                                    @elseif($status === 'overdue')
                                                        <span class="text-lg font-black drop-shadow-[0_0_8px_#ef4444]">!</span>
                                                    @endif
                                                @else
                                                     <div class="h-1 w-1 bg-white/20 rounded-full"></div>
                                                @endif

                                                {{-- GLOW EFFECT --}}
                                                @if($status !== 'none')
                                                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent pointer-events-none"></div>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
        
                                    @if($editMode)
                                    <td class="px-10 py-8 text-right bg-black/10">
                                        <button wire:click="deleteActivity({{ $plan->id }})" class="p-4 bg-red-500/10 text-red-500 rounded-2xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all shadow-lg active:scale-90">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="px-10 py-40 text-center">
                                        <div class="h-24 w-24 bg-white/5 rounded-[3rem] border border-white/5 flex items-center justify-center mx-auto mb-8 text-indigo-100/10">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <h3 class="text-lg font-black text-indigo-100/20 uppercase tracking-[0.5em] italic mb-3">MATRIZ DESIERTA</h3>
                                        <p class="text-xs text-indigo-100/10 font-bold uppercase tracking-widest italic max-w-md mx-auto leading-relaxed">No se han detectado compromisos planificados para esta sede en el ciclo operativo {{ $year }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
        .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(99, 102, 241, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(99, 102, 241, 0.4); }

        .animate-fade-in-down { animation: fade-in-down 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 1s ease-out forwards; }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</div>
