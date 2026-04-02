<div class="min-h-screen flex items-center justify-center bg-[#11111d] relative overflow-hidden" x-data="{ showResetModal: false }">
    <!-- Quantum Background Effects -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-ep-primary/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03]"></div>
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.02) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <div class="max-w-md w-full mx-4 relative z-10 animate-fade-in-up">
        <!-- Identity Header (Simplified Style) -->
        <div class="text-center mb-12 group cursor-pointer">
            <div class="flex flex-col items-center gap-2">
                <h1 class="text-5xl font-black text-white italic tracking-tighter leading-none">
                    Xama<span class="text-ep-primary">Safe</span>
                </h1>
            </div>
        </div>

        <!-- Security Portal Card (Glass-morphism) -->
        <div class="bg-white/5 backdrop-blur-xl rounded-[2.5rem] border border-white/10 p-10 shadow-[0_0_50px_rgba(0,0,0,0.5)] relative overflow-hidden group/card shadow-[0_0_30px_rgba(246,146,57,0.05)]">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-ep-primary/50 to-transparent"></div>
            
            <form wire:submit="login" class="space-y-8">
                <!-- Access Identity (Email) -->
                <div class="space-y-2 relative group/input">
                    <label for="email" class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4 italic group-focus-within/input:text-ep-primary transition-colors">
                        Identidad (Email)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-white/20 group-focus-within/input:text-ep-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="email" 
                               id="email"
                               wire:model="email" 
                               class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 focus:border-ep-primary/50 transition-all placeholder-white/5 @error('email') border-red-500/50 @enderror"
                               placeholder="correo@xamasafe.com"
                               autofocus>
                    </div>
                    @error('email')
                        <p class="mt-2 text-[10px] font-black text-red-500 uppercase italic tracking-widest ml-4 animate-pulse">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Access Key (Password) -->
                <div class="space-y-2 relative group/input" x-data="{ show: false }">
                    <label for="password" class="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4 italic group-focus-within/input:text-ep-primary transition-colors">
                        Clave de Enlace
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-white/20 group-focus-within/input:text-ep-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zM10 11V7a2 2 0 114 0v4" />
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" 
                               id="password"
                               wire:model="password" 
                               class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-12 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 focus:border-transparent transition-all placeholder-white/5 @error('password') border-red-500/50 @enderror"
                               placeholder="••••••••">
                        
                        <button type="button" 
                                @click="show = !show" 
                                tabindex="-1"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white/20 hover:text-ep-primary focus:outline-none transition-colors">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-[10px] font-black text-red-500 uppercase italic tracking-widest ml-4 animate-pulse">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Security Options -->
                <div class="flex items-center justify-between px-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" wire:model="remember" class="sr-only peer">
                        <div class="w-5 h-5 border-2 border-white/10 rounded-md bg-black/40 peer-checked:bg-ep-primary peer-checked:border-ep-primary transition-all flex items-center justify-center">
                            <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="ml-3 text-[9px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-white/60 transition-colors">Mantener Operativo</span>
                    </label>
                </div>

                <!-- Activation Button -->
                <button type="submit" 
                        class="w-full relative group/btn overflow-hidden rounded-2xl transition-all active:scale-95">
                    <div class="absolute inset-0 bg-ep-primary shadow-[0_0_20px_rgba(246,146,57,0.3)] transition-all"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-1000"></div>
                    <div class="relative py-5 flex items-center justify-center gap-3">
                        <span class="text-[11px] font-black text-white uppercase tracking-[0.3em] italic">Iniciar Operación</span>
                        <svg class="w-4 h-4 text-white group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </button>

                <!-- Recovery Protocol -->
                <div class="text-center pt-4">
                    <button type="button" @click="showResetModal = true" class="text-[9px] font-black text-white/20 hover:text-ep-primary uppercase tracking-[0.2em] italic transition-all border-b border-transparent hover:border-ep-primary/30 pb-1">
                        ¿Protocolo de Emergencia? (Olvidé mi Clave)
                    </button>
                </div>
            </form>
        </div>

        
    </div>

    <!-- Security Recovery Terminal (Modal) -->
    <template x-if="showResetModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
            <div class="absolute inset-0 bg-black/90 backdrop-blur-md" @click="showResetModal = false"></div>
            
            <div class="bg-ep-dark border border-white/10 rounded-[3rem] p-10 max-w-sm w-full relative z-10 shadow-2xl overflow-hidden animate-fade-in-up">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-ep-primary to-transparent"></div>
                
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-ep-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-6 text-ep-primary border border-ep-primary/20">
                        <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zM10 11V7a2 2 0 114 0v4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-white uppercase italic tracking-widest mb-2">Protocolo de Rescate</h3>
                    <p class="text-[10px] font-bold text-white/30 uppercase tracking-widest italic">Restablecimiento de Identidad</p>
                </div>
                
                <div class="space-y-4">
                    <div class="p-5 bg-black/40 rounded-2xl border border-white/5 group hover:border-indigo-500/30 transition-all">
                        <p class="text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-2 leading-none">Unidades Técnicas</p>
                        <p class="text-xs text-white/60 font-bold italic leading-relaxed uppercase">Contacte a su <span class="text-ep-primary">Gestor Administrativo</span> para blanqueo de credenciales.</p>
                    </div>
                    
                    <div class="p-5 bg-black/40 rounded-2xl border border-white/5 group hover:border-amber-500/30 transition-all">
                        <p class="text-[8px] font-black text-amber-500 uppercase tracking-[0.3em] mb-2 leading-none">Control Gestor / Admin</p>
                        <p class="text-xs text-white/60 font-bold italic leading-relaxed uppercase">Contacte a <span class="text-ep-primary">Soporte Central</span> vía canal encriptado:</p>
                        <p class="text-[10px] font-black mt-2 text-white/40 tracking-wider">SOPORTE@XAMASAFE.COM.AR</p>
                    </div>
                </div>
                
                <button @click="showResetModal = false" class="mt-10 w-full py-4 bg-white/5 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest italic hover:bg-white/10 transition-all border border-white/10">
                    Cerrar Protocolo
                </button>
            </div>
        </div>
    </template>

    <style>
        .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.6s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scan {
            from { transform: translateY(0); }
            to { transform: translateY(192px); }
        }
    </style>
</div>

