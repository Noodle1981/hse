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
                        <h1 class="text-[10px] font-black text-blue-400 uppercase tracking-widest italic leading-none mb-1">
                            Consola <span class="text-white">Corporativa</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Gestión de Activos Cliente
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('gestor.companies.create') }}" 
                       class="hidden xs:flex bg-ep-primary text-white hover:brightness-110 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest italic border border-white/10 transition-all items-center gap-2 shadow-lg shadow-ep-primary/20 group/btn">
                        <svg class="w-4 h-4 group-hover/btn:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M12 4v16m8-8H4"/>
                        </svg>
                        Nueva Empresa
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
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white">
        <div class="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
            
            {{-- HUB DE FILTRADO TÁCTICO --}}
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-500/30 to-transparent"></div>
                
                <div class="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                    <div class="w-full lg:w-1/2 relative group">
                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-blue-400 uppercase tracking-[0.3em] italic z-10">Buscador Corporativo</span>
                        <input wire:model.live="search" 
                               type="text" 
                               placeholder="BUSCAR POR NOMBRE, RAZÓN SOCIAL O CUIT..."
                               class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/5 italic uppercase">
                    </div>

                    <div class="flex items-center gap-6 bg-black/20 px-8 py-4 rounded-2xl border border-white/5">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" wire:model.live="showDeleted" class="sr-only peer">
                            <div class="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500/50"></div>
                            <span class="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-red-400 transition-colors">Historial de Bajas</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- NOTIFICACIONES --}}
            @if (session()->has('message'))
                <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 p-5 rounded-[2rem] flex items-center gap-4 animate-fade-in shadow-xl backdrop-blur-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-[10px] font-black uppercase italic tracking-widest">{{ session('message') }}</p>
                </div>
            @endif

            {{-- MATRIZ CORPORATIVA --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-500/20 to-transparent"></div>
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-black/20 text-left">
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Entidad / Marca</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Razón Operativa</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Registro Fiscal (CUIT)</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Activos (Sedes)</th>
                                <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($companies as $company)
                                <tr class="group/row hover:bg-white/5 transition-all duration-500 relative {{ $company->trashed() ? 'bg-red-500/5 opacity-50' : '' }}">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs font-black group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(59,130,246,0.1)]">
                                                {{ substr($company->nombre, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-blue-400 transition-colors {{ $company->trashed() ? 'line-through' : '' }}">
                                                    {{ $company->nombre }}
                                                </div>
                                                @if($company->trashed()) <span class="text-[8px] font-black text-red-500 uppercase tracking-widest mt-1.5 inline-block animate-pulse">Entidad Desactivada</span> @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-[11px] font-bold text-white/40 italic uppercase tracking-widest leading-none">{{ $company->razon_social }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="inline-flex px-4 py-2 rounded-xl text-[10px] font-black text-indigo-400/60 uppercase tracking-widest italic bg-black/40 border border-white/5">
                                            {{ $company->cuit }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest italic border {{ $company->sedes_count > 0 ? 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10' : 'text-white/10 border-white/5 bg-white/5' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            {{ $company->sedes_count }} Sedes
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover/row:translate-x-0 group-hover/row:opacity-100 transition-all duration-500">
                                            @if($company->trashed())
                                                <button wire:click="restore({{ $company->id }})" class="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="3"/></svg>
                                                    Reactivar
                                                </button>
                                            @else
                                                <a href="{{ route('gestor.companies.locations', $company) }}" 
                                                    class="h-10 px-5 bg-blue-500/10 border border-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="3"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="3"/></svg>
                                                    Gestionar Sedes
                                                </a>
                                                <a href="{{ route('gestor.companies.edit', $company) }}" 
                                                    class="h-10 w-10 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
                                                </a>
                                                <button wire:click="confirmDelete({{ $company->id }}, '{{ $company->nombre }}')" 
                                                        class="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">No se detectan entidades corporativas <br> <span class="text-[9px] tracking-widest text-white/3 italic">Inicie el registro de empresas para desplegar activos</span></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-6 border-t border-white/5 bg-black/20 pagination-tactical">
                    {{ $companies->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE BAJA CORPORATIVA --}}
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
            <div class="absolute inset-0 bg-black/85 backdrop-blur-md" wire:click="cancelDelete"></div>
            <div class="bg-ep-dark border border-white/10 rounded-[3rem] p-12 max-w-xl w-full relative z-10 shadow-2xl text-center overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1.5 bg-red-600 shadow-[0_0_20px_rgba(220,38,38,0.5)]"></div>
                <div class="w-24 h-24 bg-red-600/20 rounded-[2rem] flex items-center justify-center mx-auto mb-8 text-red-500 border border-red-500/20">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-white uppercase italic tracking-widest mb-4">Revocación Corporativa</h3>
                <p class="text-sm text-white/40 mb-12 font-bold leading-relaxed italic uppercase">
                    ESTÁ A PUNTO DE DAR DE BAJA A: <br> <span class="text-xl text-red-400 mt-2 block">{{ $companyToDeleteName }}</span> <br>
                    EL REGISTRO Y SUS SEDES SE MANTENDRÁN EN EL ARCHIVO HISTÓRICO, PERO DEJAREIS DE OPERAR CON ESTA ENTIDAD.
                </p>
                <div class="flex justify-center gap-6">
                    <button wire:click="cancelDelete" class="flex-1 py-5 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                        Abortar
                    </button>
                    <button wire:click="delete" class="flex-1 py-5 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg shadow-red-600/30 hover:brightness-110 active:scale-95">
                        Dar de Baja
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .pagination-tactical nav div:first-child { display: none; }
        .pagination-tactical nav span, .pagination-tactical nav a {
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: rgba(255,255,255,0.2) !important;
            border-radius: 12px !important; margin: 0 4px !important; padding: 8px 14px !important;
            font-weight: 900 !important; font-size: 10px !important; transition: all 0.3s !important;
        }
        .pagination-tactical nav span[aria-current="page"] {
            border-color: rgba(59,130,246,0.3) !important;
            color: #3b82f6 !important;
            background: rgba(59,130,246,0.1) !important;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59,130,246,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(59,130,246,0.3); }
    </style>
</div>
