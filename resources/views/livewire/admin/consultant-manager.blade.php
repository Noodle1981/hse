<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Mi Consultora</h2>
            <p class="text-gray-500 mt-1">Gestión de la identidad de la firma y licencias.</p>
        </div>
        @if($consultoras->isEmpty())
            <button wire:click="create" class="inline-flex items-center px-6 py-3 bg-ep-primary text-white text-base font-bold rounded-xl shadow-lg hover:bg-ep-primary-dark transition-all transform hover:scale-105">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Registrar Consultora
            </button>
        @endif
    </div>

    @if($consultoras->count() > 0)
        @foreach($consultoras as $c)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <!-- Header / Banner -->
                <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-8 py-8 md:py-10 text-white">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                            <div class="bg-white p-3 rounded-xl shadow-sm shrink-0">
                                @if($c->logo_path)
                                    <img src="{{ asset('storage/' . $c->logo_path) }}?v={{ time() }}" alt="Logo" class="h-24 w-auto object-contain">
                                @else
                                    <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="Logo" class="h-24 w-auto object-contain">
                                @endif
                            </div>
                            
                            <!-- Text Info -->
                            <div>
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="bg-ep-primary text-white text-xs font-black uppercase tracking-widest px-2 py-1 rounded">Consultora Principal</span>
                                </div>
                                <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-2">{{ $c->nombre }}</h1>
                                <div class="flex items-center space-x-6 text-gray-300 text-sm md:text-base">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                        CUIT: <span class="font-mono text-white ml-2">{{ $c->cuit }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.consultants.edit', $c->id) }}" class="flex items-center px-5 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white rounded-xl transition-all border border-white/20">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar Datos
                            </a>
                            <a href="{{ route('admin.consultants.users', $c->id) }}" class="flex items-center px-5 py-2.5 bg-ep-primary hover:bg-ep-primary-dark text-white font-bold rounded-xl shadow-lg transition-all hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Gestionar Usuarios
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    <!-- Stats Section -->
                    <div class="p-8 bg-gray-50/50">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Métricas Operativas</h3>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Licencias de Técnicos</p>
                                    <p class="text-3xl font-black text-gray-900">{{ $c->users_count }} <span class="text-gray-300 text-xl font-medium">/ {{ $c->max_tecnicos }}</span></p>
                                </div>
                                <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Empresas Gestionadas</p>
                                    <p class="text-3xl font-black text-gray-900">{{ $c->empresas_count }}</p>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="p-8">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Información de Contacto</h3>
                        <div class="space-y-4">
                            @if($c->email)
                            <div class="flex items-center group">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg mr-4 group-hover:bg-indigo-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Email Institucional</p>
                                    <p class="text-gray-900 font-medium">{{ $c->email }}</p>
                                </div>
                            </div>
                            @endif

                            @if($c->phone)
                            <div class="flex items-center group">
                                <div class="p-2 bg-green-50 text-green-600 rounded-lg mr-4 group-hover:bg-green-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Teléfono / Celular</p>
                                    <p class="text-gray-900 font-medium">{{ $c->phone }}</p>
                                </div>
                            </div>
                            @endif

                            @if($c->fiscal_address)
                            <div class="flex items-start group">
                                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg mr-4 mt-1 group-hover:bg-orange-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Domicilio Fiscal</p>
                                    <p class="text-gray-900 font-medium leading-snug">
                                        {{ $c->fiscal_address }}
                                        @if($c->locality || $c->province)
                                            <br>
                                            <span class="text-gray-500">{{ $c->locality }}{{ $c->locality && $c->province ? ',' : '' }} {{ $c->province }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="p-8">
                         <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Descripción General</h3>
                         <div class="prose prose-sm text-gray-600">
                             @if($c->description)
                                <p>{{ $c->description }}</p>
                             @else
                                <p class="text-gray-400 italic">Sin descripción registrada.</p>
                             @endif
                         </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Empty State personalized -->
        <div class="text-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-200">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Registre su Consultora</h3>
            <p class="mt-1 text-sm text-gray-500">Es necesario configurar la entidad principal para comenzar a operar.</p>
        </div>
    @endif


