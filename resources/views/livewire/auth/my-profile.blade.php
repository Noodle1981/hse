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
                            Consola de <span class="text-white">Identidad</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            Gestión de Usuario de Élite
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none mb-1">Nivel de Acceso</span>
                        <span class="text-xs font-black text-white italic uppercase">
                            {{ auth()->user()->rol === 'gestor' ? 'Gestor Master' : (auth()->user()->rol === 'tecnico' ? 'Especialista Técnico' : 'Administrador de Sistema') }}
                        </span>
                    </div>
                    <div class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary shadow-inner">
                        <span class="text-sm font-black">{{ substr($nombres, 0, 1) }}</span>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 bg-[#11111d] min-h-screen text-white selection:bg-ep-primary/30">
        <div class="max-w-[95rem] mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 animate-fade-in-down">
                <!-- Sidebar Info: Tactical ID Card -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-8 text-center shadow-2xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-b from-ep-primary/5 to-transparent pointer-events-none"></div>
                        
                        <div class="relative">
                            <div class="w-32 h-32 bg-ep-primary/10 rounded-[2.5rem] border border-ep-primary/20 mx-auto flex items-center justify-center text-ep-primary text-4xl font-black shadow-inner mb-6 rotate-3 group-hover:rotate-0 transition-all duration-700">
                                {{ substr($nombres, 0, 1) }}
                            </div>
                            <h3 class="text-2xl font-black text-white italic uppercase tracking-tight">{{ $nombres }} {{ $apellidos }}</h3>
                            <p class="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic mt-2">
                                {{ auth()->user()->rol === 'gestor' ? 'Gestor de Seguridad' : (auth()->user()->rol === 'tecnico' ? 'Especialista en Campo' : 'System Admin') }}
                            </p>
                        </div>

                        <div class="mt-10 pt-8 border-t border-white/5 text-left space-y-6">
                            <div class="group/item">
                                <span class="block text-[9px] font-black text-white uppercase tracking-widest italic mb-1 opacity-40">Identificador Digital (Email)</span>
                                <span class="text-sm text-white font-bold italic tracking-wide group-hover/item:text-ep-primary transition-colors">{{ $email }}</span>
                            </div>
                            @if($matricula)
                            <div class="group/item">
                                <span class="block text-[9px] font-black text-white uppercase tracking-widest italic mb-1 opacity-40">Credencial Técnica</span>
                                <div class="inline-flex items-center px-3 py-1 bg-ep-primary/10 border border-ep-primary/20 rounded-lg text-xs font-black text-ep-primary italic">
                                    {{ $matricula }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Transmutador (Role Switcher) Premium -->
                    @php
                        $baseRol = auth()->user()->rol;
                        $isBaseAdmin = in_array($baseRol, [\App\Models\User::ROLE_GESTOR, \App\Models\User::ROLE_SUPER_ADMIN, 'administrador', 'admin']);
                    @endphp

                    @if($isBaseAdmin)
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden group/sw">
                        <h3 class="text-[10px] font-black text-white uppercase tracking-[0.3em] mb-8 italic flex items-center gap-3">
                            <svg class="w-4 h-4 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Módulos de Interfaz
                        </h3>
                        
                        <div class="space-y-4">
                            @php
                                $activeRole = session('active_role', auth()->user()->rol);
                                $isGestorActive = $activeRole === \App\Models\User::ROLE_GESTOR || $activeRole === \App\Models\User::ROLE_SUPER_ADMIN;
                                $isTecnicoActive = $activeRole === \App\Models\User::ROLE_TECNICO;
                            @endphp

                            <!-- Gestor Option -->
                            <a href="{{ route('switch-role', \App\Models\User::ROLE_GESTOR) }}" 
                               class="flex items-center gap-5 p-5 rounded-2xl border transition-all duration-500 group {{ $isGestorActive ? 'bg-ep-primary/20 border-ep-primary shadow-[0_0_20px_rgba(246,146,57,0.1)]' : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20' }}">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all {{ $isGestorActive ? 'bg-ep-primary text-white shadow-xl rotate-0' : 'bg-white/5 text-white/30 group-hover:text-white -rotate-6' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </div>
                                <div class="text-left flex-1">
                                    <span class="block text-sm font-black italic uppercase {{ $isGestorActive ? 'text-white' : 'text-white/40' }}">Módulo Gestión</span>
                                    <span class="text-[9px] font-bold uppercase tracking-widest {{ $isGestorActive ? 'text-ep-primary' : 'text-white/20' }}">Consola Administrativa</span>
                                </div>
                                @if($isGestorActive)
                                    <div class="w-2.5 h-2.5 bg-ep-primary rounded-full animate-pulse shadow-[0_0_10px_#f69239]"></div>
                                @endif
                            </a>

                            <!-- Técnico Option -->
                            <a href="{{ route('switch-role', \App\Models\User::ROLE_TECNICO) }}" 
                               class="flex items-center gap-5 p-5 rounded-2xl border transition-all duration-500 group {{ $isTecnicoActive ? 'bg-ep-primary/20 border-ep-primary shadow-[0_0_20px_rgba(246,146,57,0.1)]' : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20' }}">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-all {{ $isTecnicoActive ? 'bg-ep-primary text-white shadow-xl rotate-0' : 'bg-white/5 text-white/30 group-hover:text-white -rotate-6' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 011-1h1a2 2 0 100-4H7a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                                </div>
                                <div class="text-left flex-1">
                                    <span class="block text-sm font-black italic uppercase {{ $isTecnicoActive ? 'text-white' : 'text-white/40' }}">Módulo Operativo</span>
                                    <span class="text-[9px] font-bold uppercase tracking-widest {{ $isTecnicoActive ? 'text-ep-primary' : 'text-white/20' }}">Interfaz de Campo</span>
                                </div>
                                @if($isTecnicoActive)
                                    <div class="w-2.5 h-2.5 bg-ep-primary rounded-full animate-pulse shadow-[0_0_10px_#f69239]"></div>
                                @endif
                            </a>
                        </div>
                        
                        <p class="mt-6 text-[9px] text-white/10 leading-tight italic uppercase font-bold tracking-widest text-center">
                            * Intercambio de protocolos de visualización persistente
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Forms Column: Tactical Configuration Módulos -->
                <div class="lg:col-span-8 space-y-10">
                    <!-- Basic Info Form -->
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl relative">
                        <div class="px-10 py-6 bg-white/5 border-b border-white/5 flex items-center justify-between">
                            <h3 class="text-xs font-black text-white uppercase tracking-[0.3em] italic flex items-center gap-3">
                                <svg class="w-5 h-5 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Parametrización de Identidad
                            </h3>
                            <div class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981] animate-pulse"></div>
                        </div>
                        <div class="p-10">
                            @if (session()->has('message'))
                                <div class="mb-8 p-5 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 text-[10px] font-black uppercase tracking-widest italic rounded-r-2xl animate-fade-in">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ session('message') }}
                                    </div>
                                </div>
                            @endif

                            <form wire:submit.prevent="updateProfile" class="space-y-8">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Nombres</span>
                                        <input wire:model="nombres" type="text" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all uppercase placeholder-white/5">
                                        @error('nombres') <span class="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Apellidos</span>
                                        <input wire:model="apellidos" type="text" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all uppercase placeholder-white/5">
                                        @error('apellidos') <span class="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Línea de Contacto (Teléfono)</span>
                                    <input wire:model="telefono" type="text" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5" placeholder="Ej: +54 9 123 4567">
                                    @error('telefono') <span class="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{{ $message }}</span> @enderror
                                </div>
                                <div class="pt-4">
                                    <button type="submit" class="w-full sm:w-auto h-14 px-12 bg-ep-primary text-white text-[10px] font-black uppercase tracking-[0.2em] italic rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-ep-primary/20 relative group overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                        <span class="relative">Sincronizar Datos de Perfil</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Change Form -->
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl relative">
                        <div class="px-10 py-6 bg-white/5 border-b border-white/5 flex items-center justify-between">
                            <h3 class="text-xs font-black text-white uppercase tracking-[0.3em] italic flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Protocolo de Seguridad
                            </h3>
                            <div class="p-2 bg-red-500/10 rounded-xl text-red-500 shadow-inner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        </div>
                        <div class="p-10">
                            @if (session()->has('password_message'))
                                <div class="mb-8 p-5 bg-emerald-500/10 border-l-4 border-emerald-500 text-emerald-400 text-[10px] font-black uppercase tracking-widest italic rounded-r-2xl animate-fade-in">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ session('password_message') }}
                                    </div>
                                </div>
                            @endif

                            <form wire:submit.prevent="updatePassword" class="space-y-8">
                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-red-500 uppercase tracking-[0.3em] italic z-10">Contraseña Actual</span>
                                    <input wire:model="current_password" type="password" class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-red-500/40 transition-all">
                                    @error('current_password') <span class="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Nueva Clave</span>
                                        <input wire:model="new_password" type="password" class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-ep-primary/50 transition-all">
                                        @error('new_password') <span class="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Confirmación</span>
                                        <input wire:model="new_password_confirmation" type="password" class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-ep-primary/50 transition-all">
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <button type="submit" class="w-full sm:w-auto h-14 px-12 bg-white text-black text-[10px] font-black uppercase tracking-[0.2em] italic rounded-2xl hover:bg-indigo-100 active:scale-95 transition-all shadow-xl shadow-white/5">
                                        Actualizar Protocolo de Seguridad
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12 pb-20">
                <p class="text-[9px] font-black text-white/5 uppercase tracking-[0.8em] italic leading-relaxed">XamaSafe Identity Console • Technician Access Protocol Level 4</p>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in-down {
            animation: fade-in-down 0.6s ease-out forwards;
        }
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out forwards;
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</div>
