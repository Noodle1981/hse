<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    Alertas de Reposición
                    @if($counts['alta'] > 0)
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                    @endif
                </h1>
                <p class="mt-1 text-sm text-gray-500">Gestione los EPP con stock crítico o vencimientos próximos impulsados por EPP Exchange.</p>
            </div>
            
            <!-- Resumen Badges -->
            <div class="flex gap-2">
                <button wire:click="setUrgenciaFilter('')" class="px-4 py-2 rounded-xl text-sm font-bold border transition-all {{ $urgenciaFilter === '' ? 'bg-gray-100 border-gray-300 text-gray-800' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    Todas ({{ $counts['total'] }})
                </button>
                <button wire:click="setUrgenciaFilter('alta')" class="px-4 py-2 rounded-xl text-sm font-bold border transition-all {{ $urgenciaFilter === 'alta' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-white border-gray-200 text-red-500 hover:bg-red-50' }}">
                    Alta ({{ $counts['alta'] }})
                </button>
                <button wire:click="setUrgenciaFilter('media')" class="px-4 py-2 rounded-xl text-sm font-bold border transition-all {{ $urgenciaFilter === 'media' ? 'bg-yellow-50 border-yellow-200 text-yellow-700' : 'bg-white border-gray-200 text-yellow-500 hover:bg-yellow-50' }}">
                    Media ({{ $counts['media'] }})
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('message') }}
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Listado de Alertas -->
        <div class="space-y-4">
            @forelse($alerts as $alert)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden" x-data="{ expanded: false }">
                    <!-- Card Header -->
                    <div class="p-6 flex flex-col lg:flex-row gap-6 items-start lg:items-center">
                        <!-- Info Principal -->
                        <div class="flex-1 flex gap-4">
                            <!-- Icono Urgencia -->
                            <div class="mt-1 flex-shrink-0">
                                @if($alert->urgencia === 'alta')
                                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                @elseif($alert->urgencia === 'media')
                                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Textos -->
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $alert->eppItem->nombre ?? 'EPP Desconocido' }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $alert->trigger_type === 'stock_bajo' ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ str_replace('_', ' ', $alert->trigger_type) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $alert->motivo }}</p>
                                <div class="flex items-center gap-4 text-xs font-medium text-gray-400">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        {{ $alert->empresa->nombre ?? 'Empresa no asignada' }} 
                                        {{ $alert->sede ? '('.$alert->sede->nombre_sede.')' : '' }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $alert->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Derecha -->
                        <div class="flex-shrink-0 flex items-center justify-between w-full lg:w-auto gap-8 border-t border-gray-100 lg:border-0 pt-4 lg:pt-0">
                            <div class="text-center">
                                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">A Reponer</span>
                                <span class="text-2xl font-black text-ep-primary">{{ $alert->cantidad_requerida }}</span>
                            </div>

                            @if(in_array($alert->status, ['pendiente', 'cotizada']))
                                <div class="flex gap-2">
                                    <button wire:click="dismissAlert({{ $alert->id }})" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors" title="Descartar Alerta">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    @if($alert->exchange_response)
                                        <button @click="expanded = !expanded" class="px-4 py-2 bg-gray-50 border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors flex items-center gap-2">
                                            <span>Ver Tienda</span>
                                            <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    @else
                                        <button wire:click="markAsOrdered({{ $alert->id }})" class="px-5 py-2 bg-ep-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-ep-primary-dark transition-colors">
                                            Comprar
                                        </button>
                                    @endif
                                </div>
                            @else
                                @if($alert->status === 'comprada')
                                    <div class="flex flex-col items-end">
                                        <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase bg-emerald-100 text-emerald-700 flex items-center gap-1 shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            COMPRA CONFIRMADA
                                        </span>
                                        @if($alert->exchange_order_id)
                                            <span class="text-[10px] text-gray-500 mt-1 mr-1 font-mono bg-gray-100 px-2 py-0.5 rounded border border-gray-200" title="Exchange Order ID">
                                                Ref: {{ $alert->exchange_order_id }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase bg-gray-100 text-gray-600">
                                        {{ $alert->status }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Acordeón EPP Exchange (Mock cotización) -->
                    @if($alert->exchange_response && count($alert->exchange_response['suggested_products'] ?? []) > 0)
                        <div x-show="expanded" x-collapse.duration.300ms class="bg-gray-50 border-t border-gray-100 p-6" style="display: none;">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Sugerencias de EPP Exchange
                                </h4>
                                <span class="text-xs text-gray-400">ID Cotización: {{ $alert->exchange_response['exchange_quote_id'] ?? 'N/A' }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                @foreach($alert->exchange_response['suggested_products'] as $prod)
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex gap-4">
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center text-xs text-gray-400">
                                            Img
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <span class="text-xs font-bold text-indigo-600 mb-0.5">{{ $prod['marca'] }}</span>
                                            <span class="text-sm font-bold text-gray-900 leading-tight mb-1">{{ Str::limit($prod['nombre'], 30) }}</span>
                                            <span class="text-xs font-medium text-emerald-600">${{ number_format($prod['precio_referencia_ars'], 0, ',', '.') }} ARS c/u</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end gap-3">
                                <button @click="expanded = false" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition-colors">
                                    Cerrar
                                </button>
                                <button wire:click="markAsOrdered({{ $alert->id }})" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Confirmar Pedido a EPP Exchange
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">¡Todo en orden!</h3>
                    <p class="text-gray-500">No hay alertas de reposición pendientes ni cotizadas bajo este filtro.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
