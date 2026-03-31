<div class="w-full mx-auto">
    @php
        $prefix = auth()->user()->isAdmin() ? 'gestor.' : 'tecnico.';
        $backRoute = auth()->user()->isAdmin() ? route('gestor.trainings.index') : route('tecnico.dashboard');
    @endphp

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
                        <h1 class="text-[10px] font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Terminal de <span class="text-white">Inyección</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            {{ $trainingId ? 'Editar' : 'Programar' }} Capacitación
                        </h2>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ $backRoute }}" class="hidden xs:flex bg-white/5 text-white/40 hover:text-white hover:bg-white/10 px-4 py-2 rounded-xl text-[10px] font-black border border-white/10 transition-all items-center gap-2 uppercase tracking-widest italic">
                        <svg class="w-4 h-4 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Abortar
                    </a>

                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100 italic">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-10 bg-[#11111d] min-h-screen text-white">
        <div class="max-w-5xl mx-auto space-y-10 animate-fade-in">
            
            <form wire:submit="save" class="space-y-10">
                
                {{-- MÓDULO 1: IDENTIDAD DEL ENTRENAMIENTO --}}
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent"></div>
                    <h3 class="text-lg font-black text-white italic mb-10 uppercase tracking-widest relative z-10 flex items-center gap-4">
                        <span class="w-1.5 h-8 bg-indigo-500 rounded-full shadow-[0_0_15px_rgba(99,102,241,0.5)]"></span>
                        Identidad & Contenidos
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-8 relative z-10">
                        <div class="md:col-span-6 space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Checklist Maestro Asociado</label>
                            <select wire:model.live="dynamic_checklist_id" 
                                    class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase">
                                <option value="">SELECCIONE EL PROTOCOLO DE ENTRENAMIENTO...</option>
                                @foreach($checklists as $checklist)
                                    <option value="{{ $checklist->id }}">{{ $checklist->title }}</option>
                                @endforeach
                            </select>
                            <p class="text-[8px] text-white/10 mt-2 ml-4 uppercase tracking-[0.2em] font-black">Vincule un checklist para dar trazabilidad técnica a los contenidos.</p>
                            @error('dynamic_checklist_id') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-6 space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Título Operativo de la Capacitación</label>
                            <input type="text" wire:model="title" 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all italic uppercase"
                                   placeholder="EJ: SEGURIDAD EN ALTURAS, PREVENCIÓN DE INCENDIOS...">
                            @error('title') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-6 space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Descripción / Objetivos Estratégicos</label>
                            <textarea wire:model="description" rows="3" 
                                      class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all italic uppercase placeholder-white/5"
                                      placeholder="DEFINA LOS OBJETIVOS Y CONTENIDOS TÁCTICOS A TRATAR..."></textarea>
                            @error('description') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- MÓDULO 2: DESPLIEGUE & UBICACIÓN --}}
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/5 to-transparent"></div>
                    <h3 class="text-lg font-black text-white italic mb-10 uppercase tracking-widest relative z-10 flex items-center gap-4">
                        <span class="w-1.5 h-8 bg-ep-primary rounded-full shadow-[0_0_15px_rgba(246,146,57,0.5)]"></span>
                        Despliegue & Responsables
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Sector Corporativo (Empresa)</label>
                            <select wire:model.live="empresa_id" 
                                    class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] italic uppercase">
                                <option value="">SELECCIONE EMPRESA...</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                            @error('empresa_id') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Punto de Despliegue (Sede)</label>
                            <select wire:model.live="sede_id" 
                                    class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] italic uppercase {{ !$empresa_id ? 'opacity-20 cursor-not-allowed' : '' }}"
                                    {{ !$empresa_id ? 'disabled' : '' }}>
                                <option value="">SELECCIONE SEDE...</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                            @error('sede_id') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Unidad Responsable (Técnico XamaSafe)</label>
                            <select wire:model="user_id" 
                                    class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] italic uppercase {{ !$sede_id ? 'opacity-20 cursor-not-allowed' : '' }}"
                                    {{ !$sede_id ? 'disabled' : '' }}>
                                <option value="">SELECCIONE RESPONSABLE...</option>
                                @foreach($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id }}">{{ $tecnico->apellidos }}, {{ $tecnico->nombres }}</option>
                                @endforeach
                            </select>
                            @error('user_id') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Instructor / Responsable Externo</label>
                            <input type="text" wire:model="trainer_name" 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all italic uppercase"
                                   placeholder="NOMBRE DEL CAPACITADOR (OPCIONAL)...">
                            @error('trainer_name') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- MÓDULO 3: CRONOGRAMA & TIPO --}}
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent"></div>
                    <h3 class="text-lg font-black text-white italic mb-10 uppercase tracking-widest relative z-10 flex items-center gap-4">
                        <span class="w-1.5 h-8 bg-amber-500 rounded-full shadow-[0_0_15px_rgba(245,158,11,0.5)]"></span>
                        Cronograma & Modalidad
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative z-10">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Modalidad</label>
                            <select wire:model="type" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all [color-scheme:dark] italic uppercase">
                                <option value="presencial">PRESENCIAL</option>
                                <option value="virtual">VIRTUAL / ONLINE</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Inyección Temporal (Fecha/Hora)</label>
                            <input type="datetime-local" wire:model="date" 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all [color-scheme:dark] italic uppercase">
                            @error('date') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Duración (MIN)</label>
                            <input type="number" wire:model="duration" 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all italic uppercase"
                                   placeholder="60">
                            @error('duration') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-white/30 uppercase tracking-[0.3em] ml-4 italic">Estado Inicial</label>
                            <select wire:model="status" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-amber-500/50 transition-all [color-scheme:dark] italic uppercase">
                                <option value="scheduled">PROGRAMADA</option>
                                <option value="completed">FINALIZADA</option>
                                <option value="cancelled">CANCELADA</option>
                            </select>
                            @error('status') <span class="text-red-500 text-[10px] font-black italic ml-4 mt-2 block animate-pulse uppercase tracking-widest">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- COMANDOS DE ACCIÓN --}}
                <div class="flex items-center justify-end gap-6 pt-10 border-t border-white/5 mt-10">
                    <a href="{{ $backRoute }}" 
                       class="px-10 py-5 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest italic transition-all group/back">
                        <span class="group-hover/back:-translate-x-2 transition-transform inline-block">Abortar Misión</span>
                    </a>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-12 py-5 bg-ep-primary text-white rounded-[1.5rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-[0_0_30px_rgba(246,146,57,0.3)] hover:brightness-110 active:scale-95 group/save relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 translate-y-14 group-hover/save:translate-y-0 transition-transform duration-500"></div>
                        <span wire:loading.remove class="relative z-10 flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                            {{ $trainingId ? 'Confirmar Actualización' : 'Inyectar Entrenamiento' }}
                        </span>
                        <span wire:loading class="relative z-10 animate-pulse">Sincronizando Datos...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        
        /* Custom styles for the datetime picker ICON in Chrome/Webkit */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: invert(1) sepia(100%) saturate(500%) hue-rotate(10deg);
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.3s;
        }
        input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
    </style>
</div>
