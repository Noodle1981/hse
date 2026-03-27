<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-ep-dark to-gray-900" x-data="{ showResetModal: false }">
    <div class="max-w-md w-full mx-4">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-ep-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-white font-bold text-1xl">XamaSafe</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Plataforma de Higiene y Seguridad</h1>
            <p class="text-gray-400">Ingrese sus credenciales para continuar</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <form wire:submit="login">
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico
                    </label>
                    <input type="email" 
                           id="email"
                           wire:model="email" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all
                                  @error('email') border-red-500 @enderror"
                           placeholder="correo@ejemplo.com"
                           autofocus>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6" x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" 
                               id="password"
                               wire:model="password" 
                               class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all
                                      @error('password') border-red-500 @enderror"
                               placeholder="••••••••">
                        
                        <button type="button" 
                                @click="show = !show" 
                                tabindex="-1"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            <!-- Eye Icon (when show is false) -->
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon (when show is true) -->
                            <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.978 9.978 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input type="checkbox" 
                           id="remember"
                           wire:model="remember" 
                           class="w-4 h-4 text-ep-primary border-gray-300 rounded focus:ring-ep-primary">
                    <label for="remember" class="ml-2 text-sm text-gray-700">
                        Recordarme
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-ep-primary hover:bg-ep-primary-dark text-white font-semibold py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mb-4">
                    Iniciar Sesión
                </button>

                <div class="text-center">
                    <button type="button" @click="showResetModal = true" class="text-xs text-gray-500 hover:text-ep-primary transition-colors font-semibold underline decoration-gray-300 underline-offset-4">
                        ¿Olvidaste tu contraseña?
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Reset Modal -->
        <div x-show="showResetModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.away="showResetModal = false" 
                 class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden transform transition-all">
                <div class="p-6">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Recuperar Contraseña</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">Por motivos de seguridad y privacidad, sigue estos pasos:</p>
                    
                    <div class="space-y-4">
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Si eres Técnico</p>
                            <p class="text-sm text-gray-700 leading-tight">Contacta directamente a tu <span class="font-bold text-ep-primary">Gestor Administrativo</span> para que blanquee tu clave desde el panel de usuarios.</p>
                        </div>
                        
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Si eres Gestor / Admin</p>
                            <p class="text-sm text-gray-700 leading-tight">Contacta al Soporte Técnico oficial de <span class="font-bold text-ep-primary">XamaSafe</span> enviando un mail a:</p>
                            <p class="text-xs font-mono mt-1 text-gray-500">soporte@xamasafe.com.ar</p>
                        </div>
                    </div>
                    
                    <button @click="showResetModal = false" class="mt-6 w-full py-2 bg-gray-900 text-white rounded-lg font-bold text-sm hover:bg-black transition-colors">
                        Entendido
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-400 text-sm mt-6">
            XamaSafe - Sistema de Checklists Dinámicos V1.2.0.0
        </p>
    </div>
</div>
