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
                            Jerarquía de <span class="text-white">Datos</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Consola Maestra de Categorías
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4 lg:gap-6">
                    <a href="{{ route('gestor.checklists.index') }}" 
                       class="hidden sm:flex h-10 px-4 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-2xl text-[9px] font-black uppercase tracking-widest italic items-center gap-2 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                        Volver a Checklists
                    </a>

                    <a href="{{ route('gestor.categories.create') }}" 
                       class="flex h-10 px-6 bg-ep-primary text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_15px_rgba(246,146,57,0.3)] group/btn">
                        <svg class="w-4 h-4 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden lg:inline">Nueva Categoría</span>
                        <span class="lg:hidden">+</span>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto overflow-hidden">
            
            {{-- TOOLBAR TÁCTICO: FILTROS --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 lg:p-10 mb-10 shadow-2xl relative animate-fade-in-down overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ep-primary/20 to-transparent"></div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
                    
                    <div class="md:col-span-8 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Buscador de Estructuras</span>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-ep-primary">
                                <svg class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" wire:model.live="search" 
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic"
                                   placeholder="Filtrar por Nombre o Código Maestro de Categoría...">
                        </div>
                    </div>

                    <div class="md:col-span-4 flex justify-end">
                        <label class="flex items-center gap-4 cursor-pointer group/toggle">
                            <div class="relative">
                                <input type="checkbox" wire:model.live="showInactive" class="sr-only">
                                <div class="w-10 h-5 bg-white/10 rounded-full transition-colors group-hover/toggle:bg-white/20 border border-white/5"></div>
                                <div class="absolute left-1 top-1 w-3 h-3 bg-white/40 rounded-full transition-all peer-checked:translate-x-5 peer-checked:bg-ep-primary"></div>
                            </div>
                            <input type="checkbox" wire:model.live="showInactive" class="hidden">
                            <span class="text-[10px] font-black uppercase tracking-widest italic {{ $showInactive ? 'text-ep-primary' : 'text-white/20' }}">Visualizar Inactivas</span>
                        </label>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-8 p-5 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 text-[10px] font-black uppercase tracking-widest italic rounded-r-2xl animate-fade-in flex items-center gap-4">
                    <svg class="w-5 h-5 shadow-emerald-500/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    {{ session('message') }}
                </div>
            @endif

            {{-- MASTER TABLE: MATRIZ DE CATEGORÍAS --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-8 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Cód. Maestro</span></th>
                                <th class="px-8 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Categoría</span></th>
                                <th class="px-8 py-6"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Identidad Visual</span></th>
                                <th class="px-8 py-6 text-center"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Jerarquías</span></th>
                                <th class="px-8 py-6 text-center"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Estado</span></th>
                                <th class="px-8 py-6 text-right"><span class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Operaciones</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($categories as $category)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative">
                                    <td class="px-8 py-8">
                                        <div class="inline-flex items-center px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl text-[10px] font-black text-indigo-400 italic font-mono uppercase tracking-[0.1em] shadow-inner">
                                            {{ $category->code }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="flex items-center gap-5">
                                            @if($category->icon)
                                                <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 group-hover/row:text-ep-primary group-hover/row:border-ep-primary/30 transition-all duration-700 shadow-xl overflow-hidden">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        {!! App\Livewire\Categories\CategoryBuilder::getIcons()[$category->icon] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />' !!}
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-base font-black text-white italic uppercase tracking-tight leading-none mb-2">{{ $category->name }}</div>
                                                <div class="text-[10px] text-white/30 font-medium italic line-clamp-1 max-w-xs">{{ $category->description ?: 'Sin descripción técnica' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-2xl shadow-lg border border-white/10 relative overflow-hidden group/color" style="background-color: {{ $category->color }}">
                                                <div class="absolute inset-0 bg-gradient-to-tr from-black/20 to-transparent"></div>
                                            </div>
                                            <span class="text-[10px] font-black text-white/30 font-mono uppercase italic tracking-widest">{{ $category->color }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        {{-- RESCATE DE ENLACE: BOTÓN TÁCTICO DE SUBCATEGORÍAS --}}
                                        <a href="{{ route('gestor.categories.subcategories', $category) }}" 
                                           class="inline-flex items-center gap-3 px-5 py-2.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-2xl hover:bg-blue-600 hover:text-white transition-all shadow-lg active:scale-95 group/sub">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full group-hover/sub:animate-ping shadow-[0_0_8px_#3b82f6]"></div>
                                            <span class="text-[10px] font-black uppercase tracking-widest italic">{{ $category->subcategories_count }} Subcategorías</span>
                                            <svg class="w-4 h-4 ml-1 group-hover/sub:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </a>
                                    </td>
                                    <td class="px-8 py-8 text-center">
                                        @if($category->is_active)
                                            <span class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.1)]">Activa</span>
                                        @else
                                            <span class="px-4 py-2 bg-white/5 border border-white/10 text-white/30 text-[9px] font-black uppercase tracking-[0.2em] italic rounded-xl">Inactiva</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-8 text-right">
                                        <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                            <a href="{{ route('gestor.categories.edit', $category) }}" 
                                               class="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-ep-primary hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95 group/editbtn" 
                                               title="Editar Identidad">
                                                <svg class="w-4 h-4 group-hover/editbtn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <button wire:click="toggleStatus({{ $category->id }})" 
                                                    class="h-11 w-11 {{ $category->is_active ? 'bg-red-600/10 border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white' : 'bg-emerald-600/10 border-emerald-500/20 text-emerald-400 hover:bg-emerald-600 hover:text-white' }} border rounded-xl flex items-center justify-center transition-all shadow-lg active:scale-95" 
                                                    title="{{ $category->is_active ? 'Desactivar' : 'Activar' }}">
                                                @if($category->is_active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-32 text-center text-white/10 uppercase font-black text-sm tracking-[0.4em] italic">No se encontraron matrices de datos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-6 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="text-[9px] font-black text-white/10 uppercase tracking-[0.5em] italic">XamaSafe Hierarchy Engine v4.0</div>
                    <div class="pagination-tactical">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>

            <div class="text-center pb-20 mt-12">
                <p class="text-[9px] font-black text-white/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Ecosystem • Hierarchy Distribution Hub • Level 5 Secure Access</p>
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
            border-color: rgba(246,146,57,0.3) !important;
            color: #f69239 !important;
            background: rgba(246,146,57,0.1) !important;
        }
    </style>
</div>
