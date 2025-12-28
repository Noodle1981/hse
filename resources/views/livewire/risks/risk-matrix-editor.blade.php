<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumbs / Header --}}
        <nav class="flex mb-4 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('risk-matrices.index') }}" class="hover:text-indigo-600 transition-colors">Matrices de Riesgo</a></li>
                <li><svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
                <li class="font-bold text-gray-900">Diseñador: {{ $matrix->title }}</li>
            </ol>
        </nav>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 flex justify-between items-start">
            <div class="flex items-start space-x-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $matrix->title }}</h1>
                    <div class="mt-2 flex flex-wrap gap-y-2 gap-x-4 text-sm text-gray-500">
                        <span class="flex items-center"><svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> {{ $matrix->empresa->nombre }}</span>
                        <span class="flex items-center"><svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> {{ $matrix->sede->nombre_sede }}</span>
                        <span class="flex items-center font-medium @if($matrix->area) text-indigo-600 @endif"><svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg> Area: {{ $matrix->area ?: 'N/D' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="addRisk" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-white hover:bg-indigo-700 shadow-sm transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Nuevo Riesgo
                </button>
            </div>
        </div>

        {{-- Risk Cards List --}}
        <div class="space-y-6">
            @forelse($matrix->risks as $risk)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group">
                    <div class="p-5 flex justify-between items-start border-b border-gray-50 bg-gray-50/30 group-hover:bg-gray-50/80 transition-colors">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded">Riesgo #{{ $loop->iteration }}</span>
                                <h3 class="text-lg font-bold text-gray-900">{{ $risk->hazard }}</h3>
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-4 text-xs">
                                <div><span class="text-gray-400 uppercase tracking-wider font-semibold">Tipo:</span> <span class="text-gray-700 font-medium">{{ $risk->type ?: 'No especificado' }}</span></div>
                                <div><span class="text-gray-400 uppercase tracking-wider font-semibold">Consecuencia:</span> <span class="text-gray-700 font-medium">{{ $risk->consequence ?: 'No especificada' }}</span></div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button wire:click="editRisk({{ $risk->id }})" class="p-2 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button onclick="confirm('¿Eliminar este riesgo y sus controles?') || event.stopImmediatePropagation()" wire:click="deleteRisk({{ $risk->id }})" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Controls Section --}}
                    <div class="p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-widest">Medidas de Control Sugeridas</h4>
                            <button wire:click="addControl({{ $risk->id }})" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-full transition-all flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Añadir Control
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @forelse($risk->controls as $control)
                                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg bg-gray-50/20 group/control hover:border-indigo-200 hover:bg-white transition-all">
                                    <div class="flex items-center space-x-3 overflow-hidden">
                                        <div class="flex-shrink-0 w-2 h-2 rounded-full @if($control->type == 'Eliminación') bg-red-400 @elseif($control->type == 'Ingeniería') bg-blue-400 @else bg-green-400 @endif"></div>
                                        <div class="truncate">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $control->measure }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $control->type }} @if($control->checklist_id) • Vinc.: {{ $control->checklist->registry_code }} @endif</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1 opacity-0 group-hover/control:opacity-100 transition-opacity">
                                        <button wire:click="editControl({{ $control->id }})" class="p-1 text-gray-400 hover:text-indigo-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <button wire:click="deleteControl({{ $control->id }})" class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-2 text-xs text-gray-400 italic py-2">No hay medidas de control definidas para este riesgo.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-xl border-2 border-dashed border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay riesgos definidos</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza añadiendo el primer peligro para esta matriz.</p>
                    <div class="mt-6">
                        <button wire:click="addRisk" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Añadir primer riesgo
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Risk Modal --}}
        @if($showRiskModal)
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form wire:submit.prevent="saveRisk">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">{{ $editingRiskId ? 'Editar Peligro' : 'Nuevo Peligro' }}</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Peligro / Hazard</label>
                                        <input type="text" wire:model="hazard" placeholder="Ej: Caída a distinto nivel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('hazard') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tipo de Riesgo</label>
                                        <input type="text" wire:model="type" placeholder="Ej: Mecánico, Químico..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Consecuencia Posible</label>
                                        <textarea wire:model="consequence" rows="2" placeholder="Ej: Fracturas, contusiones graves..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                        @error('consequence') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Guardar</button>
                                <button type="button" wire:click="$set('showRiskModal', false)" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Control Modal --}}
        @if($showControlModal)
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form wire:submit.prevent="saveControl">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 border-b pb-3 mb-4">{{ $editingControlId ? 'Editar Medida' : 'Nueva Medida de Control' }}</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Medida Preventiva / Control</label>
                                        <input type="text" wire:model="measure" placeholder="Ej: Uso de arnés de seguridad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('measure') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jerarquía de Control</label>
                                        <select wire:model="controlType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="Eliminación">Eliminación</option>
                                            <option value="Sustitución">Sustitución</option>
                                            <option value="Ingeniería">Ingeniería / Colectiva</option>
                                            <option value="Administrativo">Administrativo / Señalética</option>
                                            <option value="EPP">EPP / Individual</option>
                                        </select>
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <label class="block text-sm font-medium text-gray-700">Verificar con Checklist (Opcional)</label>
                                            <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">INTEGRACIÓN</span>
                                        </div>
                                        <select wire:model="checklist_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">No vincular a checklist</option>
                                            @foreach($checklists as $chk)
                                                <option value="{{ $chk->id }}">{{ $chk->registry_code }} - {{ $chk->title }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-[10px] text-gray-400 font-medium">Si vinculas un checklist, los inspectores podrán verificar esta medida automáticamente.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Guardar Control</button>
                                <button type="button" wire:click="$set('showControlModal', false)" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
