<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Constancias de Visitas</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Registro completo de todas las constancias de visitas realizadas por los técnicos.
                </p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm transition-all" 
                           placeholder="Buscar...">
                </div>

                <select wire:model.live="statusFilter" 
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all">
                    <option value="">Todos los Estados</option>
                    <option value="pending">Pendiente Firma</option>
                    <option value="completed">Completada</option>
                </select>

                <select wire:model.live="technicianFilter" 
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all">
                    <option value="">Todos los Técnicos</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->nombres }} {{ $tech->apellidos }}</option>
                    @endforeach
                </select>

                <select wire:model.live="empresaFilter" 
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all">
                    <option value="">Todas las Empresas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <select wire:model.live="sedeFilter" 
                        {{ !$empresaFilter ? 'disabled' : '' }}
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all {{ !$empresaFilter ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <option value="">Todas las Sedes</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                    @endforeach
                </select>

                <select wire:model.live="tipoVisitaFilter" 
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all">
                    <option value="">Todos los Tipos</option>
                    @foreach($tiposVisita as $tipo)
                        <option value="{{ $tipo }}">{{ $tipo }}</option>
                    @endforeach
                </select>

                <div class="flex gap-3">
                    <input type="date" wire:model.live="fromDate" 
                           class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all"
                           placeholder="Desde">
                    <input type="date" wire:model.live="toDate" 
                           class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all"
                           placeholder="Hasta">
                </div>
            </div>
        </div>

        <!-- Listado de Constancias -->
        <div class="space-y-6">
            @forelse($certificates as $certificate)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-6">
                            <!-- Info Constancia -->
                            <div class="flex items-center">
                                <div class="w-14 h-14 rounded-2xl bg-ep-primary/5 flex items-center justify-center text-ep-primary mr-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <div class="text-lg font-bold text-gray-900">{{ $certificate->tipo_visita }}</div>
                                    <div class="flex items-center text-xs text-gray-500 mt-1">
                                        <span class="font-bold text-ep-primary mr-2">{{ $certificate->sede->empresa->nombre }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $certificate->sede->nombre_sede }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Detalles -->
                            <div class="flex flex-wrap gap-8 items-center lg:justify-end flex-1">
                                <div class="text-center md:text-left">
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Técnico</div>
                                    <div class="text-sm font-bold text-gray-700">{{ $certificate->user->nombres ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center md:text-left">
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Fecha</div>
                                    <div class="text-sm font-bold text-gray-700">{{ $certificate->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Estado</div>
                                    @if($certificate->firma_responsable)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Completada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                            Pendiente Firma
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('visit-certificates.pdf.preview', $certificate->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-ep-primary text-ep-primary font-bold rounded-lg hover:bg-ep-primary hover:text-white transition-colors text-xs mr-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('visit-certificates.pdf.download', $certificate->id) }}" class="inline-flex items-center px-4 py-2 bg-ep-primary text-white font-bold rounded-lg hover:bg-ep-primary-dark transition-colors text-xs shadow-sm shadow-ep-primary/20">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        @if($certificate->observacion)
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Observaciones</div>
                                <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-600">
                                    {{ $certificate->observacion }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-20 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-gray-500 font-bold">No se encontraron constancias con los filtros actuales.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $certificates->links() }}
        </div>
    </div>
</div>
