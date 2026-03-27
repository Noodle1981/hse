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
                        Panel de <span class="text-ep-primary italic">Administración</span>
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
    <!-- Header Summary Block -->
    <div class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-none">Hub Central</h2>
        <p class="mt-2 text-sm text-gray-600">Gestión centralizada de usuarios, empresas y recursos del sistema</p>
    </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Usuarios Card -->
            <a href="{{ route('gestor.users.index') }}" 
               class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-ep-primary/30 transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center text-center">
                    <!-- Icon -->
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-purple-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-ep-primary transition-colors">
                        Usuarios
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4">
                        Gestión de usuarios del sistema y control de accesos
                    </p>
                    
                    <!-- Action Indicator -->
                    <div class="flex items-center text-ep-primary font-semibold text-sm group-hover:gap-2 transition-all">
                        <span>Acceder</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Técnicos Card -->
            <a href="{{ route('gestor.technicians.index') }}" 
               class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-ep-primary/30 transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center text-center">
                    <!-- Icon -->
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-indigo-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-ep-primary transition-colors">
                        Técnicos
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4">
                        Gestión de técnico y asignación de trabajo de campo
                    </p>
                    
                    <!-- Action Indicator -->
                    <div class="flex items-center text-ep-primary font-semibold text-sm group-hover:gap-2 transition-all">
                        <span>Acceder</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>

            <!-- Empresas Card -->
            <a href="{{ route('gestor.companies.index') }}" 
               class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-ep-primary/30 transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center text-center">
                    <!-- Icon -->
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-ep-primary transition-colors">
                        Empresas
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4">
                        Gestión de empresas clientes y sus sedes
                    </p>
                    
                    <!-- Action Indicator -->
                    <div class="flex items-center text-ep-primary font-semibold text-sm group-hover:gap-2 transition-all">
                        <span>Acceder</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>


        </div>

        <!-- Info Footer -->
        <div class="mt-8 bg-indigo-50 border border-indigo-100 rounded-xl p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm text-indigo-900 font-semibold">Panel de Administración</p>
                    <p class="text-sm text-indigo-700 mt-1">Gestiona usuarios, técnicos y empresas clientes desde un solo lugar.</p>
                </div>
            </div>
        </div>
    </div>
</div>
