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
                            Laboratorio de <span class="text-white">Protocolos</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Configuración Maestra de Checklists
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4 lg:gap-6">
                    {{-- BOTÓN CRÍTICO: NUEVO CHECKLIST --}}
                    <a href="{{ route('gestor.checklists.create') }}" 
                       class="flex h-10 px-6 bg-ep-primary text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_15px_rgba(246,146,57,0.3)] group/btn">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden sm:inline">Nuevo Protocolo</span>
                        <span class="sm:hidden">+</span>
                    </a>

                    <a href="{{ route('profile') }}" class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary group hover:bg-white/10 transition-all shadow-inner">
                        <span class="text-sm font-black">{{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto overflow-hidden">
            
            {{-- TOOLBAR TÁCTICO: FILTROS --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 lg:p-10 mb-10 shadow-2xl relative animate-fade-in-down overflow-hidden group/filters">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-end relative z-10">
                    
                    <div class="md:col-span-6 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Buscador de Protocolos</span>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-ep-primary">
                                <svg class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search" 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic"
                                   placeholder="Filtrar por Título o Código Técnico...">
                        </div>
                    </div>

                    <div class="md:col-span-3 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Categoría</span>
                        <select wire:model.live="categoryFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                            <option value="">TODAS LAS CATEGORÍAS</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-indigo-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <div class="md:col-span-3 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Estado Operativo</span>
                        <select wire:model.live="statusFilter" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                            <option value="all">TODOS LOS ESTADOS</option>
                            <option value="active">PROTOCOLOS ACTIVOS</option>
                            <option value="inactive">PROTOCOLOS INACTIVOS</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-8 p-5 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 text-[10px] font-black uppercase tracking-widest italic rounded-r-2xl animate-fade-in flex items-center gap-4">
                    <svg class="w-5 h-5 shadow-emerald-500/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    {{ session('message') }}
                </div>
            @endif

            {{-- MASTER LIST: MATRIZ DE PROTOCOLOS --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group/matrix mb-20 animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                @if($checklists->isEmpty())
                    <div class="p-32 text-center">
                        <div class="h-24 w-24 bg-white/5 rounded-[2.5rem] border border-white/10 flex items-center justify-center mx-auto mb-8 text-white/10 transform -rotate-6">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h3 class="text-sm font-black text-white/30 uppercase tracking-[0.5em] italic mb-4 leading-none">Cero Protocolos en Sistema</h3>
                        <p class="text-[10px] text-white/10 font-bold uppercase tracking-[0.2em] italic max-w-sm mx-auto leading-relaxed">
                            El laboratorio está listo. Crea tu primer protocolo de inspección para comenzar la ingeniería de seguridad.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full border-collapse">
                            <thead>
                                <tr class="bg-black/20 text-left">
                                    <th class="px-10 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Diseño de Protocolo</span></th>
                                    <th class="px-10 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Categorización Máxima</span></th>
                                    <th class="px-10 py-6 text-center"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Variables</span></th>
                                    <th class="px-10 py-6 text-center"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Estado</span></th>
                                    <th class="px-10 py-6 text-right"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Comandos</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($checklists as $checklist)
                                    <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                        <td class="px-10 py-8 relative">
                                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-10 {{ $checklist->is_active ? 'bg-ep-primary shadow-[0_0_15px_#f69239]' : 'bg-white/10' }} rounded-r-full transition-all opacity-0 group-hover/row:opacity-100"></div>
                                            <div class="flex flex-col">
                                                <div class="text-base font-black text-white italic uppercase tracking-tight leading-none mb-2">{{ $checklist->title }}</div>
                                                @if($checklist->description)
                                                    <div class="text-[10px] text-white/30 font-medium italic truncate max-w-xs mb-2">{{ $checklist->description }}</div>
                                                @endif
                                                @if($checklist->registry_code)
                                                    <div class="inline-flex items-center px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-[9px] font-black text-indigo-400 italic w-fit uppercase tracking-widest leading-none">
                                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                        CODE: {{ $checklist->registry_code }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-10 py-8">
                                            <div class="flex flex-col gap-3">
                                                @if($checklist->categoryRelation)
                                                    <div class="inline-flex items-center gap-3">
                                                        <div class="w-2.5 h-2.5 rounded-full shadow-lg" style="background-color: {{ $checklist->categoryRelation->color }}; box-shadow: 0 0 8px {{ $checklist->categoryRelation->color }}"></div>
                                                        <span class="text-[10px] font-black text-white uppercase tracking-widest italic">{{ $checklist->categoryRelation->name }}</span>
                                                    </div>
                                                @endif
                                                @if($checklist->subcategoryRelation)
                                                    <div class="text-[9px] text-white/30 font-black uppercase tracking-widest italic pl-5 border-l border-white/10">
                                                        {{ $checklist->subcategoryRelation->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-10 py-8 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <div class="text-2xl font-black text-white italic leading-none mb-1">{{ $checklist->questions_count }}</div>
                                                <div class="text-[8px] font-black text-white/20 uppercase tracking-widest italic uppercase">Puntos de Control</div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-8 text-center">
                                            @if($checklist->is_draft)
                                                <span class="px-4 py-2 bg-amber-500/10 border border-amber-500/20 text-amber-500 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl shadow-[0_0_15px_rgba(245,158,11,0.1)]">Borrador Maestro</span>
                                            @else
                                                <button wire:click="toggleActive({{ $checklist->id }})"
                                                        class="px-5 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] italic transition-all border flex items-center gap-3 mx-auto active:scale-95
                                                        {{ $checklist->is_active ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'bg-white/5 border-white/10 text-white/30 hover:border-white/20' }}">
                                                    <div class="w-2 h-2 rounded-full {{ $checklist->is_active ? 'bg-emerald-400 shadow-[0_0_8px_#10b981] animate-pulse' : 'bg-white/20' }}"></div>
                                                    {{ $checklist->is_active ? 'ONLINE' : 'OFFLINE' }}
                                                </button>
                                            @endif
                                        </td>
                                        <td class="px-10 py-8 text-right">
                                            <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                                <a href="{{ route('gestor.checklists.edit', $checklist->id) }}" 
                                                   class="h-11 px-6 bg-white/5 border border-white/10 text-white hover:bg-ep-primary hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic flex items-center gap-3 transition-all shadow-lg active:scale-95"
                                                   title="Editar Protocolo">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                    Editar
                                                </a>
                                                <a href="{{ route('gestor.checklists.emulator', $checklist->id) }}" 
                                                   class="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-indigo-600 hover:text-white rounded-xl flex items-center justify-center transition-all group/emu shadow-lg active:scale-95" 
                                                   title="Emular Vista Técnica">
                                                    <svg class="w-4 h-4 group-hover/emu:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                                                </a>
                                                <button wire:click="duplicate({{ $checklist->id }})"
                                                        class="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-emerald-600 hover:text-white rounded-xl flex items-center justify-center transition-all group/dup shadow-lg active:scale-95"
                                                        title="Duplicar Protocolo">
                                                    <svg class="w-4 h-4 group-hover/dup:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
                                                </button>
                                                <button @click="$dispatch('open-confirm-modal', { 
                                                            id: {{ $checklist->id }}, 
                                                            title: 'Modificar Estado de Protocolo', 
                                                            message: '¿Confirma el cambio de disponibilidad para el protocolo: {{ $checklist->title }}?',
                                                            action: 'toggleActive',
                                                            confirmLabel: 'Ejecutar Sincronización',
                                                            confirmColor: 'bg-indigo-600 shadow-indigo-500/20'
                                                        })"
                                                        class="h-11 w-11 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all group/del shadow-lg active:scale-95"
                                                        title="Eliminar / Cambiar Estado">
                                                    <svg class="w-4 h-4 group-hover/del:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- PAGINACIÓN TÁCTICA --}}
                <div class="px-10 py-6 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="text-[9px] font-black text-white/10 uppercase tracking-[0.5em] italic leading-none text-center sm:text-left">
                        Checklist Protocol Engine v4.1 • XamaSafe Ecosystem
                    </div>
                    <div class="pagination-tactical">
                        {{ $checklists->links() }}
                    </div>
                </div>
            </div>

            <div class="text-center pb-20 mt-12 animate-fade-in opacity-0" style="animation-delay: 0.5s; animation-fill-mode: forwards;">
                <p class="text-[9px] font-black text-white/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Protocol Lab • Master Gestor Control Protocol Level 5</p>
            </div>
        </div>
    </div>

    <x-modal-confirm />

    <style>
        .glow-text-orange { text-shadow: 0 0 12px rgba(246,146,57,0.4); }
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        
        /* Pagination Styling */
        .pagination-tactical nav div:first-child { display: none; }
        .pagination-tactical nav span, .pagination-tactical nav a {
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.2) !important;
            border-radius: 12px !important;
            margin: 0 4px !important;
            padding: 8px 14px !important;
            font-weight: 900 !important;
            font-size: 10px !important;
            transition: all 0.3s !important;
        }
        .pagination-tactical nav span[aria-current="page"] {
            border-color: rgba(99,102,241,0.3) !important;
            color: #6366f1 !important;
            background: rgba(99,102,241,0.1) !important;
        }
        .pagination-tactical nav a:hover {
            border-color: rgba(255,255,255,0.2) !important;
            color: white !important;
            background: rgba(255,255,255,0.05) !important;
        }
    </style>
</div>
