<div class="py-6">
    @php
        $prefix = auth()->user()->isAdmin() ? 'gestor.' : 'tecnico.';
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Ejecuciones Avanzadas</h1>
            <p class="mt-2 text-sm text-gray-600">Herramientas avanzadas para seguimiento de compromisos y contratos</p>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
            <!-- Contratos Anuales Card -->
            <a href="{{ route($prefix . 'annual-contract.index') }}" 
               class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:border-ep-primary/30 transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center text-center">
                    <!-- Icon -->
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-green-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-ep-primary transition-colors">
                        Contratos Anuales
                    </h3>
                    
                    <!-- Description -->
                    <p class="text-sm text-gray-600 mb-4">
                        Visualiza y gestiona el seguimiento de contratos anuales con métricas y estadísticas de cumplimiento
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

    </div>
</div>
