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
                            {{ $empresa ? 'Edición de Activo' : 'Creación de Activo' }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('gestor.companies.index') }}" 
                       class="hidden xs:flex bg-white/5 text-white/50 hover:text-white hover:bg-white/10 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest italic border border-white/5 transition-all items-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver a Inventario
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

    <div class="px-4 sm:px-6 lg:px-8 py-8 md:py-16 text-white min-h-[calc(100vh-4rem)] flex items-center justify-center">
        <div class="max-w-4xl w-full mx-auto space-y-8 animate-fade-in-up">
            
            <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-600 via-blue-400 to-transparent"></div>
                
                <div class="p-8 sm:p-12">
                    <div class="flex items-center gap-6 mb-12 border-b border-white/5 pb-8">
                        <div class="h-16 w-16 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 shadow-[0_0_15px_rgba(59,130,246,0.15)] flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl sm:text-3xl font-black text-white italic uppercase tracking-tight leading-none mb-2">
                                {{ $empresa ? 'Actualizar Registro' : 'Alta de Entidad Corporativa' }}
                            </h2>
                            <p class="text-[10px] font-bold text-indigo-100/40 uppercase tracking-[0.2em] italic">
                                Completar las credenciales fiscales y operativas del cliente
                            </p>
                        </div>
                    </div>

                    <form wire:submit.prevent="save" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Nombre -->
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-3">
                                    Identidad Comercial <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        wire:model="nombre" 
                                        type="text" 
                                        placeholder="Nombre Público de la Entidad"
                                        class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/20 italic uppercase"
                                    >
                                    @error('nombre') 
                                        <div class="absolute -bottom-6 left-2 flex items-center gap-1 text-red-500 text-[9px] font-black uppercase tracking-widest italic glow-text-red">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3"/></svg>
                                            {{ $message }}
                                        </div> 
                                    @enderror
                                </div>
                            </div>

                            <!-- CUIT -->
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-3">
                                    Registro Fiscal (CUIT) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        wire:model="cuit" 
                                        type="text" 
                                        placeholder="20-12345678-9"
                                        maxlength="13"
                                        class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm font-black text-white tracking-[0.2em] focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/20 italic"
                                    >
                                    @error('cuit') 
                                        <div class="absolute -bottom-6 left-2 flex items-center gap-1 text-red-500 text-[9px] font-black uppercase tracking-widest italic glow-text-red">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3"/></svg>
                                            {{ $message }}
                                        </div> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Razón Social -->
                            <div class="relative group md:col-span-2">
                                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-3">
                                    Razón Social Completa <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input 
                                        wire:model="razonSocial" 
                                        type="text" 
                                        placeholder="Nombre legal completo de la organización"
                                        class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/20 italic uppercase"
                                    >
                                    @error('razonSocial') 
                                        <div class="absolute -bottom-6 left-2 flex items-center gap-1 text-red-500 text-[9px] font-black uppercase tracking-widest italic glow-text-red">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3"/></svg>
                                            {{ $message }}
                                        </div> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-3">
                                    Dirección Fiscal
                                </label>
                                <div class="relative">
                                    <input 
                                        wire:model="direccion" 
                                        type="text" 
                                        placeholder="Av. Principal 1234, Ciudad"
                                        class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/20 italic uppercase"
                                    >
                                    @error('direccion') 
                                        <div class="absolute -bottom-6 left-2 flex items-center gap-1 text-red-500 text-[9px] font-black uppercase tracking-widest italic glow-text-red">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3"/></svg>
                                            {{ $message }}
                                        </div> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="relative group">
                                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] italic mb-3">
                                    Correo Electrónico
                                </label>
                                <div class="relative">
                                    <input 
                                        wire:model="email" 
                                        type="email" 
                                        placeholder="contacto@empresa.com"
                                        class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-white/20 italic"
                                    >
                                    @error('email') 
                                        <div class="absolute -bottom-6 left-2 flex items-center gap-1 text-red-500 text-[9px] font-black uppercase tracking-widest italic glow-text-red">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3"/></svg>
                                            {{ $message }}
                                        </div> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="mt-12 pt-8 border-t border-white/5 flex flex-col-reverse sm:flex-row justify-end gap-6 items-center">
                            <a href="{{ route('gestor.companies.index') }}" 
                               class="text-[10px] font-black text-white/40 uppercase tracking-[0.3em] italic hover:text-white transition-colors w-full sm:w-auto text-center py-4">
                                Abortar Proceso
                            </a>
                            
                            <!-- Botón Tactical Pill -->
                            <button 
                                type="submit" 
                                wire:loading.attr="disabled"
                                class="px-8 py-5 bg-blue-600 w-full sm:w-auto text-white text-[11px] font-black uppercase italic tracking-[0.3em] rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-blue-600/20 relative overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                
                                <span wire:loading.remove class="flex items-center justify-center gap-3 relative z-10">
                                    {{ $empresa ? 'Actualizar Base de Datos' : 'Registrar Entidad' }}
                                </span>
                                
                                <span wire:loading class="flex items-center justify-center gap-3 relative z-10">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando Información...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>

    <style>
        .glow-text-red {
            text-shadow: 0 0 8px rgba(239, 68, 68, 0.4);
        }
        .animate-fade-in-up { 
            animation: fade-in-up 0.5s ease-out forwards; 
        }
        @keyframes fade-in-up { 
            from { opacity: 0; transform: translateY(30px); } 
            to { opacity: 0.99; transform: translateY(0); } 
        }
    </style>
</div>
