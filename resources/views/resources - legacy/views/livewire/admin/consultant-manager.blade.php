<div class="space-y-10 bg-[#11111d] text-white min-h-screen">
    {{-- Tactical Header: Setup de Empresas --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in">
        <div class="relative">
            <div class="absolute -left-4 top-0 bottom-0 w-1 bg-ep-primary rounded-full shadow-[0_0_15px_#f69239]"></div>
            <h1 class="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none mb-2">System Architecture</h1>
            <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter leading-none">Setup de <span class="text-indigo-400">Consultoras</span></h2>
            <p class="text-[10px] font-bold text-white/30 uppercase tracking-widest mt-3 italic">Gestión de identidades corporativas y escalabilidad de red.</p>
        </div>
        
        <div class="flex items-center gap-3">
            @if($consultoras->isEmpty())
                <button wire:click="create" class="group relative px-8 py-4 bg-ep-primary text-white text-xs font-black uppercase tracking-widest italic rounded-2xl shadow-[0_10px_40px_rgba(246,146,57,0.2)] hover:shadow-[0_15px_50px_rgba(246,146,57,0.4)] transition-all transform hover:-translate-y-1 active:scale-95 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <div class="relative flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Nueva Consultora Master
                    </div>
                </button>
            @endif
        </div>
    </div>

    @if($consultoras->count() > 0)
        <div class="grid grid-cols-1 gap-12 animate-fade-in-up">
            @foreach($consultoras as $c)
                <div class="group/card relative bg-white/5 backdrop-blur-xl rounded-[3rem] border border-white/5 shadow-2xl overflow-hidden hover:border-ep-primary/30 transition-all duration-700">
                    {{-- Card Glow Effect --}}
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-ep-primary/5 rounded-full blur-[100px] group-hover/card:bg-ep-primary/10 transition-colors duration-1000"></div>
                    
                    {{-- Header Section: Identity --}}
                    <div class="relative z-10 bg-black/40 px-10 py-12 border-b border-white/5">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                            <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                                <div class="relative group/logo">
                                    <div class="absolute inset-0 bg-ep-primary/20 rounded-3xl blur-2xl opacity-0 group-hover/logo:opacity-100 transition-opacity"></div>
                                    <div class="relative bg-[#1a1a2e] p-6 rounded-[2rem] border border-white/10 shadow-2xl transition-transform group-hover/logo:scale-105 duration-500">
                                        @if($c->logo_path)
                                            <img src="{{ asset('storage/' . $c->logo_path) }}?v={{ time() }}" alt="Logo" class="h-24 w-auto object-contain brightness-110">
                                        @else
                                            <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="Logo" class="h-24 w-auto object-contain brightness-110">
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mb-4">
                                        <span class="bg-ep-primary/20 border border-ep-primary/30 text-ep-primary text-[10px] font-black uppercase tracking-[0.2em] italic px-4 py-1.5 rounded-full shadow-[0_0_15px_rgba(246,146,57,0.1)]">Unidad de Gestión Master</span>
                                        @if($c->estado === 'activo')
                                            <span class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] italic px-4 py-1.5 rounded-full">Operativa</span>
                                        @endif
                                    </div>
                                    <h3 class="text-4xl md:text-5xl font-black text-white italic tracking-tighter uppercase mb-4">{{ $c->nombre }}</h3>
                                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-8">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[9px] font-black text-white/20 uppercase tracking-widest italic">Digital Identity ID</span>
                                            <span class="text-sm font-black text-indigo-300 italic tracking-widest">{{ $c->cuit }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto justify-center lg:justify-end">
                                <a href="{{ route('admin.consultants.edit', $c->id) }}" class="flex-1 lg:flex-none flex items-center justify-center px-8 py-4 bg-white/5 hover:bg-white/10 backdrop-blur-md text-white text-[11px] font-black uppercase tracking-widest italic rounded-2xl border border-white/10 transition-all group/btn">
                                    <svg class="w-5 h-5 mr-3 text-white/40 group-hover/btn:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Configuración
                                </a>
                                <a href="{{ route('admin.consultants.users', $c->id) }}" class="flex-1 lg:flex-none flex items-center justify-center px-8 py-4 bg-indigo-500 text-white text-[11px] font-black uppercase tracking-widest italic rounded-2xl shadow-[0_10px_30px_rgba(99,102,241,0.2)] hover:shadow-[0_15px_40px_rgba(99,102,241,0.4)] transition-all transform hover:-translate-y-1 group/btn">
                                    <svg class="w-5 h-5 mr-3 text-indigo-200 group-hover/btn:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    Gestionar Staff
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Data Matrix --}}
                    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-white/5">
                        {{-- Matrix Cell: Licencias --}}
                        <div class="p-10 group/cell hover:bg-white/5 transition-colors">
                            <div class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-8 flex items-center gap-3">
                                <div class="w-1.5 h-1.5 bg-ep-primary rounded-full shadow-[0_0_8px_#f69239]"></div>
                                Capacidad Humana
                            </div>
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <div class="text-5xl font-black text-white italic tracking-tighter leading-none mb-4">{{ $c->users_count }}</div>
                                    @php $pct = min(100, ($c->users_count / ($c->max_tecnicos ?: 1)) * 100); @endphp
                                    <div class="h-1.5 w-32 bg-black/40 rounded-full border border-white/5 overflow-hidden">
                                        <div class="h-full bg-ep-primary shadow-[0_0_10px_#f69239]" <?php echo 'style="width: ' . $pct . '%;"'; ?>></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black text-white/20 uppercase block mb-1">Limite Licencia</span>
                                    <span class="text-xl font-black text-ep-primary italic">{{ $c->max_tecnicos }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Matrix Cell: Empresas --}}
                        <div class="p-10 group/cell hover:bg-white/5 transition-colors">
                            <div class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-8 flex items-center gap-3">
                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full shadow-[0_0_8px_#3b82f6]"></div>
                                Cartera Corporativa
                            </div>
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <div class="text-5xl font-black text-white italic tracking-tighter leading-none mb-4">{{ $c->empresas_count }}</div>
                                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest italic">Empresas en Gestión</p>
                                </div>
                                <svg class="w-10 h-10 text-white/5 group-hover/cell:text-blue-500/20 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                        </div>

                        {{-- Matrix Cell: Contacto --}}
                        <div class="p-10 group/cell hover:bg-white/5 transition-colors md:col-span-2">
                            <div class="text-[9px] font-black text-white/20 uppercase tracking-[0.3em] italic mb-8 flex items-center gap-3">
                                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_#10b981]"></div>
                                Canal de Comunicaciones
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                <div class="flex items-center gap-6 group/item">
                                    <div class="h-12 w-12 bg-white/5 rounded-2xl flex items-center justify-center border border-white/5 group-hover/item:border-emerald-500/40 group-hover/item:bg-emerald-500/10 transition-all shadow-xl">
                                        <svg class="w-6 h-6 text-white/20 group-hover/item:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-white/20 uppercase italic mb-1 tracking-widest">Enlace Digital</p>
                                        <p class="text-xs font-black text-white group-hover/item:text-emerald-400 transition-colors uppercase italic tracking-tight">{{ $c->email ?? 'no_link@sector.com' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 group/item">
                                    <div class="h-12 w-12 bg-white/5 rounded-2xl flex items-center justify-center border border-white/5 group-hover/item:border-emerald-500/40 group-hover/item:bg-emerald-500/10 transition-all shadow-xl">
                                        <svg class="w-6 h-6 text-white/20 group-hover/item:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-white/20 uppercase italic mb-1 tracking-widest">Coordenadas</p>
                                        <p class="text-xs font-black text-white group-hover/item:text-emerald-400 transition-colors uppercase italic tracking-tight truncate max-w-[150px]">{{ $c->locality ?? 'Sector Sin Asignar' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 animate-fade-in-up" style="animation-delay: 0.6s">
            {{ $consultoras->links() }}
        </div>
    @else
        <div class="relative overflow-hidden bg-black/40 backdrop-blur-xl rounded-[4rem] border-2 border-dashed border-white/5 p-20 text-center animate-pulse-border">
            <div class="mx-auto h-24 w-24 bg-gradient-to-br from-ep-primary/10 to-indigo-900/10 rounded-[2rem] flex items-center justify-center border border-white/5 mb-8 shadow-2xl">
                <svg class="h-12 w-12 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-2xl font-black text-white italic uppercase tracking-widest mb-4">Núcleo Vacío Detectado</h3>
            <p class="text-indigo-100/30 text-sm font-bold uppercase tracking-[0.2em] mb-12 max-w-md mx-auto">Proceda al registro de la Consultora Master para iniciar el despliegue del ecosistema de seguridad.</p>
            <button wire:click="create" class="inline-flex items-center px-12 py-5 bg-ep-primary text-white text-xs font-black uppercase tracking-widest italic rounded-2xl shadow-[0_10px_50px_rgba(246,146,57,0.3)] hover:scale-105 transition-all active:scale-95">
                Iniciar Registro de Setup
            </button>
        </div>
    @endif

    <style>
        .animate-fade-in { animation: fade-in 1s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.8s ease-out forwards; }
        .animate-pulse-border { animation: pulse-border 3s infinite alternate; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse-border { from { border-color: rgba(255,255,255,0.05); } to { border-color: rgba(246,146,57,0.3); } }
    </style>
</div>
