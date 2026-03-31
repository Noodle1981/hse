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
                            Especialización: <span class="text-white">{{ $category->name }}</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Gestor de Subcategorías (Protocolos Nivel 2)
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('gestor.categories.index') }}" 
                       class="h-10 px-5 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-2xl text-[9px] font-black uppercase tracking-widest italic flex items-center gap-2 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Volver a Categorías
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto space-y-10 animate-fade-in">
            
            {{-- ALERTAS DE SISTEMA --}}
            @if (session()->has('message'))
                <div class="p-5 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 text-[10px] font-black uppercase tracking-widest italic rounded-r-2xl animate-fade-in flex items-center gap-4">
                    <svg class="w-5 h-5 shadow-emerald-500/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    {{ session('message') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                {{-- COLUMNA: FORMULARIO DE INYECCIÓN (ESTÁTICO/FLOTANTE) --}}
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden sticky top-8">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                        
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-sm font-black text-white italic uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">⊕</span>
                                Nueva Subcategoría
                            </h2>
                            <button wire:click="toggleForm" class="text-[9px] font-black text-white/20 hover:text-white uppercase tracking-widest italic transition-colors">
                                {{ $showForm ? 'Cerrar Laboratorio' : 'Abrir Laboratorio' }}
                            </button>
                        </div>

                        @if($showForm)
                            <form wire:submit="save" class="space-y-6 animate-fade-in-down">
                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Código Táctico (Slug)</span>
                                    <input type="text" wire:model="newCode" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic uppercase" placeholder="ej: potencia-electrica">
                                    @error('newCode') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                                </div>

                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Nombre Comercial</span>
                                    <input type="text" wire:model="newName" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic" placeholder="Ej: Electricidad de Potencia">
                                    @error('newName') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                                </div>

                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Misión Técnica</span>
                                    <textarea wire:model="newDescription" rows="3" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/10 transition-all placeholder-white/5 italic" placeholder="Descripción opcional del alcance técnico..."></textarea>
                                </div>

                                <div class="flex gap-4 pt-4">
                                    <button type="submit" class="flex-1 h-14 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-xl shadow-indigo-600/20 hover:brightness-110 active:scale-95 transition-all">Consolidar Inyección</button>
                                </div>
                            </form>
                        @else
                            <div class="py-20 text-center border-2 border-dashed border-white/5 rounded-[2rem] group hover:border-indigo-500/30 transition-all duration-700 cursor-pointer" wire:click="toggleForm">
                                <p class="text-xs font-black text-white/10 uppercase tracking-[0.4em] italic mb-2">Ingeniería en Espera</p>
                                <p class="text-[9px] text-white/5 italic">Haga clic para activar el inyector de especialidades</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- COLUMNA: MATRIZ DE ESPECIALIDADES --}}
                <div class="lg:col-span-8">
                    <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                        
                        <div class="px-10 py-8 border-b border-white/5 bg-black/20 flex items-center justify-between">
                            <h2 class="text-sm font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">L2</span>
                                Matriz de Subcategorías (Nivel 2)
                            </h2>
                            <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic">{{ count($subcategories) }} Especialidades</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-black/10 text-left">
                                        <th class="px-10 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-left">Código / Identidad</span></th>
                                        <th class="px-10 py-6 text-center"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Estado</span></th>
                                        <th class="px-10 py-6 text-right"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Comandos</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($subcategories as $subcategory)
                                        <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                            <td class="px-10 py-8">
                                                @if($editingId === $subcategory->id)
                                                    <div class="grid grid-cols-2 gap-6 p-4 bg-indigo-500/5 rounded-2xl border border-indigo-500/20 animate-fade-in">
                                                        <div class="relative group/edit">
                                                            <span class="absolute -top-2 left-3 bg-[#1e1e2d] px-2 text-[7px] font-black text-indigo-400 uppercase italic z-10 tracking-widest">Código</span>
                                                            <input wire:model="editCode" type="text" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-xs font-bold text-white uppercase italic focus:ring-1 focus:ring-indigo-500">
                                                        </div>
                                                        <div class="relative group/edit">
                                                            <span class="absolute -top-2 left-3 bg-[#1e1e2d] px-2 text-[7px] font-black text-indigo-400 uppercase italic z-10 tracking-widest">Nombre</span>
                                                            <input wire:model="editName" type="text" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-xs font-bold text-white italic focus:ring-1 focus:ring-indigo-500">
                                                        </div>
                                                        <div class="col-span-2 relative group/edit">
                                                            <span class="absolute -top-2 left-3 bg-[#1e1e2d] px-2 text-[7px] font-black text-indigo-400 uppercase italic z-10 tracking-widest">Descripción</span>
                                                            <input wire:model="editDescription" type="text" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-xs font-bold text-white italic focus:ring-1 focus:ring-indigo-500">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-6">
                                                        <div class="px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-[10px] font-black text-indigo-400 italic font-mono uppercase tracking-[0.1em] shadow-inner">
                                                            {{ $subcategory->code }}
                                                        </div>
                                                        <div>
                                                            <div class="text-base font-black text-white italic uppercase tracking-tight leading-none mb-1">{{ $subcategory->name }}</div>
                                                            <div class="text-[10px] text-white/30 font-medium italic line-clamp-1 max-w-sm">{{ $subcategory->description ?: 'Especialidad estándar' }}</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-10 py-8 text-center">
                                                @if($subcategory->is_active)
                                                    <span class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.1)]">Activa</span>
                                                @else
                                                    <span class="px-4 py-2 bg-white/5 border border-white/10 text-white/30 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl">Inactiva</span>
                                                @endif
                                            </td>
                                            <td class="px-10 py-8 text-right">
                                                @if($editingId === $subcategory->id)
                                                    <div class="flex justify-end gap-4 animate-fade-in">
                                                        <button wire:click="update" class="h-10 px-6 bg-emerald-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-lg active:scale-95">Guardar</button>
                                                        <button wire:click="cancelEdit" class="h-10 px-6 bg-white/5 text-white/40 rounded-xl text-[9px] font-black uppercase tracking-widest italic hover:text-white transition-all">Descartar</button>
                                                    </div>
                                                @else
                                                    <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                                        <button wire:click="edit({{ $subcategory->id }})" class="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-ep-primary hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95 group/editbtn" title="Refinar Especialidad">
                                                            <svg class="w-4 h-4 group-hover/editbtn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </button>
                                                        <button wire:click="toggleStatus({{ $subcategory->id }})" class="h-11 w-11 {{ $subcategory->is_active ? 'bg-red-600/10 border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white' : 'bg-emerald-600/10 border-emerald-500/20 text-emerald-400 hover:bg-emerald-600 hover:text-white' }} border rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" title="{{ $subcategory->is_active ? 'Bloquear' : 'Desbloquear' }}">
                                                            @if($subcategory->is_active)
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                            @else
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            @endif
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">No se detectaron especialidades vinculadas <br> <span class="text-[9px] tracking-widest text-white/3 italic">Ajuste la matriz en el laboratorio lateral</span></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="px-10 py-6 border-t border-white/5 bg-black/20">
                            <p class="text-[9px] font-black text-white/10 uppercase tracking-[0.5em] italic">XamaSafe Sub-Category Engine v4.0 • Distributed Node</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center pb-20 mt-12 animate-fade-in opacity-0" style="animation-delay: 0.5s; animation-fill-mode: forwards;">
                <p class="text-[9px] font-black text-white/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Protocol Lab • Master Gestor Control • Data Structure L2</p>
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
    </style>
</div>
