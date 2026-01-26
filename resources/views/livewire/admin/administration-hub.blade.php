<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Administración</h1>
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
                        Gestión de técnicos y asignación de permisos
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

            <!-- Empleados Card -->
            <a href="{{ route('gestor.epp.employees.index') }}" 
               class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-ep-primary/30 transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center text-center">
                    <!-- Icon -->
                    <div class="w-20 h-20 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-teal-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-ep-primary transition-colors">
                        Empleados
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4">
                        Empleados de clientes para EPP, entregas y capacitaciones
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
                    <p class="text-sm text-indigo-700 mt-1">Gestiona usuarios, técnicos, empresas clientes y sus empleados desde un solo lugar. Para importar empleados masivamente, accede a la vista de Empleados.</p>
                </div>
            </div>
        </div>
    </div>
</div>
