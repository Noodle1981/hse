<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ auth()->user()->hasRole('Administrador') ? 'Control Global de Entregas' : 'Mis Entregas de EPP' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ auth()->user()->hasRole('Administrador') ? 'Registro histórico de todos los elementos entregados por técnicos.' : 'Registro de elementos que has entregado al personal.' }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('epp.catalog') }}" 
                   class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 hover:bg-gray-50 hover:text-ep-primary transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Catálogo EPP
                </a>
                <a href="{{ route('epp.deliver') }}" 
                   class="inline-flex items-center px-6 py-3 bg-ep-primary text-white font-bold rounded-xl shadow-lg shadow-ep-primary/30 hover:bg-ep-primary-dark transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nueva Entrega
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm transition-all" 
                           placeholder="Buscar por empleado...">
                </div>

                <select wire:model.live="empresaFilter" 
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all">
                    <option value="">Todas las Empresas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>

                <select wire:model.live="sedeFilter" 
                        {{ !$empresaFilter ? 'disabled' : '' }}
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all {{ !$empresaFilter ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <option value="">Todas las Sedes</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Listado de Entregas -->
        <div class="space-y-6">
            @forelse($deliveries as $delivery)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-6">
                            <!-- Info Empleado -->
                            <div class="flex items-center">
                                <div class="w-14 h-14 rounded-2xl bg-ep-primary/5 flex items-center justify-center text-ep-primary mr-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-gray-900">{{ $delivery->employee->full_name }}</div>
                                    <div class="flex items-center text-xs text-gray-500 mt-1">
                                        <span class="font-bold text-ep-primary mr-2">{{ $delivery->employee->empresa->nombre }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $delivery->employee->sede->nombre_sede }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Entrega -->
                            <div class="flex flex-wrap gap-8 items-center lg:justify-end flex-1">
                                <div class="text-center md:text-left">
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Fecha de Entrega</div>
                                    <div class="text-sm font-bold text-gray-700">
                                        @if($delivery instanceof \App\Models\VisitEppDelivery)
                                            {{ $delivery->created_at->format('d/m/Y H:i') }}
                                        @else
                                            {{ $delivery->delivery_date->format('d/m/Y H:i') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-center md:text-left">
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Entregado por</div>
                                    <div class="text-sm font-bold text-gray-700">
                                        @if($delivery instanceof \App\Models\VisitEppDelivery)
                                            {{ $delivery->visit->user->nombres ?? 'Sistema' }}
                                        @else
                                            {{ $delivery->user->nombres ?? 'Sistema' }}
                                        @endif
                                    </div>
                                </div>
                                @if($delivery instanceof \App\Models\EppDelivery && $delivery->signature)
                                    <div class="text-center">
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Firma Digital</div>
                                        <img src="{{ $delivery->signature }}" class="h-10 w-24 object-contain bg-gray-50 rounded p-1 mx-auto" alt="Firma">
                                    </div>
                                @endif
                                <div>
                                    @if($delivery instanceof \App\Models\VisitEppDelivery)
                                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            Entrega en Visita
                                        </span>
                                    @else
                                        <a href="{{ route('epp.delivery.pdf.preview', $delivery->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-ep-primary text-ep-primary font-bold rounded-lg hover:bg-ep-primary hover:text-white transition-colors text-xs mr-2">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Ver
                                        </a>
                                        <a href="{{ route('epp.delivery.pdf', $delivery->id) }}" class="inline-flex items-center px-4 py-2 bg-ep-primary text-white font-bold rounded-lg hover:bg-ep-primary-dark transition-colors text-xs shadow-sm shadow-ep-primary/20">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            PDF
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Items Entregados -->
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Elementos Entregados</div>
                            <div class="flex flex-wrap gap-2">
                                @if($delivery instanceof \App\Models\VisitEppDelivery)
                                    @foreach($delivery->epp_items as $item)
                                        <div class="flex items-center bg-gray-100 px-3 py-1.5 rounded-xl border border-gray-200">
                                            <span class="w-5 h-5 bg-ep-primary text-white rounded-full flex items-center justify-center text-[10px] font-bold mr-2">{{ $item['qty'] ?? 1 }}</span>
                                            <span class="text-xs font-bold text-gray-700">{{ $item['name'] }}</span>
                                            @if(isset($item['size']))
                                                <span class="ml-2 text-[10px] text-gray-400 px-1.5 py-0.5 rounded-lg border border-gray-200 uppercase">{{ $item['size'] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($delivery->items as $item)
                                        <div class="flex items-center bg-gray-100 px-3 py-1.5 rounded-xl border border-gray-200">
                                            <span class="w-5 h-5 bg-ep-primary text-white rounded-full flex items-center justify-center text-[10px] font-bold mr-2">{{ $item->pivot->cantidad }}</span>
                                            <span class="text-xs font-bold text-gray-700">{{ $item->nombre }}</span>
                                            <span class="ml-2 text-[10px] text-gray-400 px-1.5 py-0.5 rounded-lg border border-gray-200 uppercase">
                                                @if($item->pivot->tipo_entrega == 'inicial') Entrega Inicial
                                                @elseif($item->pivot->tipo_entrega == 'reposicion_rotura') Rotura
                                                @else Vencimiento
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if($delivery instanceof \App\Models\EppDelivery && $delivery->observaciones)
                                <div class="mt-4 p-4 bg-gray-50 rounded-xl text-xs text-gray-600 italic">
                                    "{{ $delivery->observaciones }}"
                                </div>
                            @elseif($delivery instanceof \App\Models\VisitEppDelivery && $delivery->notes)
                                <div class="mt-4 p-4 bg-gray-50 rounded-xl text-xs text-gray-600 italic">
                                    "{{ $delivery->notes }}"
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-20 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <p class="text-gray-500 font-bold">No se registran entregas con los filtros actuales.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $deliveries->links() }}
        </div>
    </div>
</div>
