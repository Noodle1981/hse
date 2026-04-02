<div class="w-full mx-auto">
    {{-- Header de Historial Premium --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Archivo de <span class="text-white">Inspecciones</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Historial de Operaciones
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary group hover:bg-white/10 transition-all shadow-inner">
                        <span class="text-sm font-black">{{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-[90rem] mx-auto">
            
            {{-- Panel de Filtros Glassmorphism --}}
            <div class="mb-10 animate-fade-in-down">
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/5 to-transparent"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
                        {{-- Búsqueda Rápida --}}
                        <div class="lg:col-span-2 relative group">
                            <label class="block text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mb-3 italic px-1">Filtro de búsqueda inteligente</label>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="search"
                                       placeholder="Checklist, inspector, ID o sede..."
                                       class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-4 text-sm font-bold text-white placeholder-white/5 focus:ring-2 focus:ring-ep-primary/50 transition-all">
                                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-white/20 group-focus-within:text-ep-primary transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mb-3 italic px-1">Estado</label>
                            <select wire:model.live="statusFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] cursor-pointer">
                                <option value="">Todos los Estados</option>
                                <option value="completed">Completados</option>
                                <option value="draft">Borradores</option>
                            </select>
                        </div>

                        {{-- Resultado --}}
                        <div>
                            <label class="block text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mb-3 italic px-1">Resultado de Auditoría</label>
                            <select wire:model.live="resultFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] cursor-pointer">
                                <option value="">Todos los Resultados</option>
                                <option value="Pass">Pass (Pasa)</option>
                                <option value="Fail">Fail (No Pasa)</option>
                            </select>
                        </div>

                        {{-- Empresa / Sede --}}
                        <div>
                            <label class="block text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mb-3 italic px-1">Empresa</label>
                            <select wire:model.live="empresaFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                                <option value="">Todas las Empresas</option>
                                @foreach($empresas as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mb-3 italic px-1">Sede Operativa</label>
                            <select wire:model.live="sedeFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark] disabled:opacity-30" {{ !$empresaFilter ? 'disabled' : '' }}>
                                <option value="">Todas las Sedes</option>
                                @foreach($sedes as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fechas --}}
                        <div>
                            <label class="block text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mb-3 italic px-1">Desde</label>
                            <input type="date" wire:model.live="fromDate" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mb-3 italic px-1">Hasta</label>
                            <input type="date" wire:model.live="toDate" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all [color-scheme:dark]">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Listado de Inspecciones (Tarjetas HUD) --}}
            <div class="space-y-6 animate-fade-in" style="animation-delay: 0.2s">
                @forelse($inspections as $inspection)
                    <div class="group bg-ep-dark/60 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 hover:border-ep-primary/30 transition-all duration-700 shadow-2xl relative overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                        
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8 relative z-10">
                            <div class="flex-1 w-full">
                                <div class="flex flex-wrap items-center gap-4 mb-4">
                                    <div class="h-10 w-10 flex items-center justify-center bg-white/5 rounded-xl border border-white/10 text-ep-primary group-hover:scale-110 group-hover:-rotate-6 transition-transform shadow-inner">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.4em] italic mb-1">ID: #{{ str_pad($inspection->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <h3 class="text-lg font-black text-white italic uppercase tracking-tight group-hover:text-ep-primary transition-colors leading-none">
                                            {{ $inspection->checklist->title }}
                                        </h3>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        {{-- Resultado Auditoría --}}
                                        @if($inspection->result)
                                            @php
                                                $resultClass = $inspection->result === 'Pass' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.2)]' : 'bg-red-500/10 border-red-500/30 text-red-500 glow-text-red';
                                            @endphp
                                            <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.2em] italic border {{ $resultClass }}">
                                                {{ strtoupper($inspection->result) }}
                                            </span>
                                        @endif

                                        {{-- Estado --}}
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest italic border border-white/5 bg-white/5 text-indigo-100/40">
                                            {{ $inspection->status === 'completed' ? 'COMPLETADO' : 'BORRADOR' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-[10px] font-black uppercase tracking-widest italic">
                                    <span class="flex items-center gap-2 text-indigo-100/30 group/meta transition-colors">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10 group-hover/meta:border-ep-primary/30 transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        {{ $inspection->sede->empresa->nombre ?? 'N/A' }} | <span class="text-white/60 ml-1">{{ $inspection->sede->nombre_sede ?? 'N/A' }}</span>
                                    </span>
                                    <span class="flex items-center gap-2 text-indigo-100/30">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        Inspector: <span class="text-white/60 ml-1">{{ $inspection->user->nombres ?? 'User' }}</span>
                                    </span>
                                    <span class="flex items-center gap-2 text-indigo-100/30">
                                        <div class="p-1.5 bg-white/5 rounded-lg border border-white/10">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        {{ $inspection->completed_at ? $inspection->completed_at->format('d/m/Y H:i') . ' HS' : 'EN PROCESO' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="w-full lg:w-auto mt-6 lg:mt-0 flex flex-row lg:flex-col gap-4">
                                <a href="{{ auth()->user()->isAdmin() ? route('gestor.inspections.show', $inspection) : route('tecnico.inspections.show', $inspection) }}"
                                   class="flex-1 lg:flex-none h-12 px-6 flex items-center justify-center bg-white/5 text-white text-[9px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 hover:bg-white/10 transition-all text-center shadow-lg">
                                    Ver Detalle
                                </a>
                                @if($inspection->status === 'completed')
                                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.inspections.pdf.download', $inspection) : route('tecnico.inspections.pdf.download', $inspection) }}" 
                                       class="h-12 w-12 flex items-center justify-center bg-red-600/10 border border-red-500/20 text-red-500 rounded-2xl hover:bg-red-600 hover:text-white transition-all group/pdf shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </a>
                                @endif
                                @if($inspection->status === 'draft' || auth()->user()->isAdmin())
                                    <button wire:click="delete({{ $inspection->id }})"
                                            wire:confirm="¿Expurgar este registro de inspección?"
                                            class="h-12 w-12 flex items-center justify-center bg-white/5 border border-white/10 text-white/20 rounded-2xl hover:bg-red-600/10 hover:text-red-500 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-20 text-center bg-ep-dark/40 rounded-[3rem] border border-white/5 animate-fade-in">
                        <svg class="h-20 w-20 text-indigo-100/5 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h3 class="text-sm font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-2 leading-none">Sin Registros en el Archivo</h3>
                        <p class="text-[11px] text-indigo-100/20 font-bold uppercase tracking-widest italic leading-none">No se han encontrado inspecciones con el criterio actual</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación Premium --}}
            @if($inspections->hasPages())
                <div class="mt-12 px-8 py-4 bg-ep-dark/40 rounded-[2rem] border border-white/5">
                    {{ $inspections->links() }}
                </div>
            @endif
        </div>
        
        <div class="mt-16 text-center">
            <p class="text-[9px] font-black text-indigo-100/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Inspection Archive HUD • Specialist Access Only</p>
        </div>
    </div>

    <style>
        .glow-text-red { text-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }
        .glow-emerald-text { text-shadow: 0 0 10px rgba(16, 185, 129, 0.4); }
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.6s ease-out forwards;
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }
    </style>
</div>

