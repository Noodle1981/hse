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
                        <h1 class="text-[10px] font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Control <span class="text-white">Central</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Núcleo de Administración Operativa
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all group">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg group-hover:scale-110 transition-transform">
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

    <div class="px-4 sm:px-6 lg:px-8 py-12 text-white">
        <div class="max-w-[95rem] mx-auto space-y-12 animate-fade-in">
            
            <!-- Central Intelligence Block -->
            <div class="relative overflow-hidden bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl group/hub">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-ep-primary/10 rounded-full blur-[100px] opacity-0 group-hover/hub:opacity-100 transition-opacity duration-1000"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-black text-white italic tracking-tight mb-2">HUB DE MANDO <span class="text-ep-primary">{{ $consultoraName }}</span></h2>
                        <p class="text-sm text-white/40 max-w-xl font-medium tracking-wide">Acceso unificado a la gestión de activos, identidades y recursos corporativos. Seleccione un módulo para iniciar operaciones de alto nivel.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_#10b981]"></div>
                        <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic leading-none">Sistema Online</span>
                    </div>
                </div>
            </div>

            <!-- Operational Modules Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Usuarios Module -->
                <a href="{{ route('gestor.users.index') }}" 
                   class="group/card relative bg-ep-dark/30 backdrop-blur-md border border-white/5 rounded-[2.5rem] p-8 hover:bg-white/5 transition-all duration-500 shadow-xl overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-500/20 to-purple-900/40 rounded-3xl flex items-center justify-center mb-8 border border-purple-500/30 group-hover/card:scale-110 transition-transform duration-500 shadow-[0_0_30px_rgba(168,85,247,0.2)]">
                            <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-black text-white italic tracking-tight mb-3 group-hover/card:text-purple-400 transition-colors uppercase">
                            Usuarios
                        </h3>
                        
                        <p class="text-xs text-white/40 mb-8 max-w-[200px] leading-relaxed font-bold tracking-wide italic">
                            GESTIÓN CENTRALIZADA DE IDENTIDADES Y MATRIZ DE PERMISOS
                        </p>
                        
                        <div class="flex items-center text-purple-500 text-[10px] font-black uppercase tracking-widest gap-2 bg-purple-500/10 px-6 py-3 rounded-full border border-purple-500/20 group-hover/card:bg-purple-500 group-hover/card:text-white transition-all duration-300">
                            INICIAR TERMINAL
                            <svg class="w-4 h-4 transform group-hover/card:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Técnicos Module -->
                <a href="{{ route('gestor.technicians.index') }}" 
                   class="group/card relative bg-ep-dark/30 backdrop-blur-md border border-white/5 rounded-[2.5rem] p-8 hover:bg-white/5 transition-all duration-500 shadow-xl overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-500/20 to-indigo-900/40 rounded-3xl flex items-center justify-center mb-8 border border-indigo-500/30 group-hover/card:scale-110 transition-transform duration-500 shadow-[0_0_30px_rgba(99,102,241,0.2)]">
                            <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-black text-white italic tracking-tight mb-3 group-hover/card:text-indigo-400 transition-colors uppercase">
                            Escuadrón
                        </h3>
                        
                        <p class="text-xs text-white/40 mb-8 max-w-[200px] leading-relaxed font-bold tracking-wide italic uppercase">
                            CONTROL DE TÉCNICOS Y ASIGNACIÓN DE RECURSOS EN CAMPO
                        </p>
                        
                        <div class="flex items-center text-indigo-500 text-[10px] font-black uppercase tracking-widest gap-2 bg-indigo-500/10 px-6 py-3 rounded-full border border-indigo-500/20 group-hover/card:bg-indigo-500 group-hover/card:text-white transition-all duration-300">
                            DESPLEGAR UNIDADES
                            <svg class="w-4 h-4 transform group-hover/card:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Empresas Module -->
                <a href="{{ route('gestor.companies.index') }}" 
                   class="group/card relative bg-ep-dark/30 backdrop-blur-md border border-white/5 rounded-[2.5rem] p-8 hover:bg-white/5 transition-all duration-500 shadow-xl overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500/20 to-blue-900/40 rounded-3xl flex items-center justify-center mb-8 border border-blue-500/30 group-hover/card:scale-110 transition-transform duration-500 shadow-[0_0_30px_rgba(59,130,246,0.2)]">
                            <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-2xl font-black text-white italic tracking-tight mb-3 group-hover/card:text-blue-400 transition-colors uppercase">
                            Corporativo
                        </h3>
                        
                        <p class="text-xs text-white/40 mb-8 max-w-[200px] leading-relaxed font-bold tracking-wide italic uppercase">
                            INTELIGENCIA EMPRESARIAL Y GESTIÓN DE SEDES GLOBALES
                        </p>
                        
                        <div class="flex items-center text-blue-500 text-[10px] font-black uppercase tracking-widest gap-2 bg-blue-500/10 px-6 py-3 rounded-full border border-blue-500/20 group-hover/card:bg-blue-500 group-hover/card:text-white transition-all duration-300">
                            RECURSOS CLIENTE
                            <svg class="w-4 h-4 transform group-hover/card:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Footer Message -->
            <div class="text-center pt-8 animate-fade-in opacity-0" style="animation-delay: 1s; animation-fill-mode: forwards;">
                <p class="text-[9px] font-black text-white/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Ecosystem Core • Global Administration Matrix • Pro-Efficiency HUD</p>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fade-in 1s ease-out forwards; }
        .animate-fade-in-down { animation: fade-in-down 0.8s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div>

