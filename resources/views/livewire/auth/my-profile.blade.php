<div class="px-4 sm:px-6 lg:px-8 pt-8 w-full max-w-9xl mx-auto">
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

                    <h1 class="text-lg md:text-xl font-medium text-white tracking-tight">
                        Mi <span class="text-ep-primary italic">Perfil</span>
                    </h1>
                </div>

                <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                    <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                        {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                    </div>
                    <div class="h-4 w-px bg-white/10 mx-1"></div>
                    <span class="text-[10px] font-bold text-indigo-100">{{ auth()->user()->nombres }}</span>
                </a>
            </div>
        </header>
    @endsection
    <div class="max-w-4xl">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                <div class="w-24 h-24 bg-ep-primary rounded-full mx-auto flex items-center justify-center text-white text-3xl font-black shadow-lg mb-4">
                    {{ substr($nombres, 0, 1) }}
                </div>
                <h3 class="text-xl font-bold text-gray-900">{{ $nombres }} {{ $apellidos }}</h3>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                    {{ auth()->user()->rol === 'gestor' ? 'Gestor' : (auth()->user()->rol === 'tecnico' ? 'Técnico' : 'Administrador') }}
                </p>
                <div class="mt-6 pt-6 border-t border-gray-50 text-left space-y-4">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Email</span>
                        <span class="text-sm text-gray-700 font-medium">{{ $email }}</span>
                    </div>
                    @if($matricula)
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Matrícula</span>
                        <span class="text-sm text-gray-700 font-medium">{{ $matricula }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Forms Column -->
        <div class="md:col-span-2 space-y-8">
            <!-- Basic Info Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Datos Personales
                    </h3>
                </div>
                <div class="p-6">
                    @if (session()->has('message'))
                        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombres</label>
                                <input wire:model="nombres" type="text" class="w-full rounded-lg border-gray-200 bg-gray-50 focus:ring-ep-primary focus:border-ep-primary text-sm text-gray-900">
                                @error('nombres') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Apellidos</label>
                                <input wire:model="apellidos" type="text" class="w-full rounded-lg border-gray-200 bg-gray-50 focus:ring-ep-primary focus:border-ep-primary text-sm text-gray-900">
                                @error('apellidos') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Teléfono</label>
                            <input wire:model="telefono" type="text" class="w-full rounded-lg border-gray-200 bg-gray-50 focus:ring-ep-primary focus:border-ep-primary text-sm text-gray-900" placeholder="Ej: +54 9 123 4567">
                            @error('telefono') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="bg-ep-primary text-white px-6 py-2 rounded-xl text-sm font-bold hover:brightness-110 transition-all shadow-sm">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Seguridad y Contraseña
                    </h3>
                </div>
                <div class="p-6">
                    @if (session()->has('password_message'))
                        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm">
                            {{ session('password_message') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="updatePassword" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Contraseña Actual</label>
                            <input wire:model="current_password" type="password" class="w-full rounded-lg border-gray-200 bg-gray-50 focus:ring-ep-primary focus:border-ep-primary text-sm text-gray-900">
                            @error('current_password') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nueva Contraseña</label>
                                <input wire:model="new_password" type="password" class="w-full rounded-lg border-gray-200 bg-gray-50 focus:ring-ep-primary focus:border-ep-primary text-sm text-gray-900">
                                @error('new_password') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Confirmar Nueva Contraseña</label>
                                <input wire:model="new_password_confirmation" type="password" class="w-full rounded-lg border-gray-200 bg-gray-50 focus:ring-ep-primary focus:border-ep-primary text-sm text-gray-900">
                            </div>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="bg-ep-dark text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-black transition-all shadow-sm">
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
