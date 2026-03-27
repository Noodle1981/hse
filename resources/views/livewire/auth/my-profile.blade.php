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

            <!-- Transmutador (Role Switcher) -->
            @php
                $baseRol = auth()->user()->rol;
                $isBaseAdmin = in_array($baseRol, [\App\Models\User::ROLE_GESTOR, \App\Models\User::ROLE_SUPER_ADMIN, 'administrador', 'admin']);
            @endphp

            @if($isBaseAdmin)
            <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Modo de Interfaz
                </h3>
                
                <div class="space-y-3">
                    @php
                        $activeRole = session('active_role', auth()->user()->rol);
                        $isGestorActive = $activeRole === \App\Models\User::ROLE_GESTOR || $activeRole === \App\Models\User::ROLE_SUPER_ADMIN;
                        $isTecnicoActive = $activeRole === \App\Models\User::ROLE_TECNICO;
                    @endphp

                    <!-- Gestor Option -->
                    <a href="{{ route('switch-role', \App\Models\User::ROLE_GESTOR) }}" 
                       class="flex items-center gap-3 p-3 rounded-xl border-2 transition-all group {{ $isGestorActive ? 'border-ep-primary bg-indigo-50/50' : 'border-gray-50 hover:border-gray-200 hover:bg-gray-50' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all {{ $isGestorActive ? 'bg-ep-primary text-white shadow-md' : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs font-black {{ $isGestorActive ? 'text-indigo-900' : 'text-gray-600' }}">Gestión</span>
                            <span class="text-[10px] font-medium {{ $isGestorActive ? 'text-indigo-600' : 'text-gray-400' }}">Panel Administrativo</span>
                        </div>
                        @if($isGestorActive)
                            <div class="ml-auto w-2 h-2 bg-ep-primary rounded-full shadow-sm"></div>
                        @endif
                    </a>

                    <!-- Técnico Option -->
                    <a href="{{ route('switch-role', \App\Models\User::ROLE_TECNICO) }}" 
                       class="flex items-center gap-3 p-3 rounded-xl border-2 transition-all group {{ $isTecnicoActive ? 'border-ep-primary bg-indigo-50/50' : 'border-gray-50 hover:border-gray-200 hover:bg-gray-50' }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-all {{ $isTecnicoActive ? 'bg-ep-primary text-white shadow-md' : 'bg-gray-100 text-gray-400 group-hover:bg-gray-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 011-1h1a2 2 0 100-4H7a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs font-black {{ $isTecnicoActive ? 'text-indigo-900' : 'text-gray-600' }}">Técnico</span>
                            <span class="text-[10px] font-medium {{ $isTecnicoActive ? 'text-indigo-600' : 'text-gray-400' }}">Interfaz Operativa</span>
                        </div>
                        @if($isTecnicoActive)
                            <div class="ml-auto w-2 h-2 bg-ep-primary rounded-full shadow-sm"></div>
                        @endif
                    </a>
                </div>
                
                <p class="mt-4 text-[9px] text-gray-400 leading-tight italic">
                    * Cambia el modo para acceder a funciones administrativas o de inspección técnica.
                </p>
            </div>
            @endif
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
