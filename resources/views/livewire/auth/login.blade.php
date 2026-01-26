<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-ep-dark to-gray-900">
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
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <input type="password" 
                           id="password"
                           wire:model="password" 
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all
                                  @error('password') border-red-500 @enderror"
                           placeholder="••••••••">
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
                        class="w-full bg-ep-primary hover:bg-ep-primary-dark text-white font-semibold py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Iniciar Sesión
                </button>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs text-gray-500 text-center mb-2 font-semibold">Problemas de ingreso</p>
                <p class="text-xs text-gray-600 text-center">
                    <span class="font-mono">soporte@xamasafe.com.ar</span>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-400 text-sm mt-6">
            XamaSafe - Sistema de Checklists Dinámicos V1.2.0.0
        </p>
    </div>
</div>
