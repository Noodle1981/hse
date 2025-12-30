<div>
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-ep-dark">
                {{ $hallazgoId ? 'Editar Hallazgo' : 'Nuevo Hallazgo' }}
            </h1>
            <p class="text-gray-600 mt-2">Complete los siguientes pasos para {{ $hallazgoId ? 'actualizar' : 'registrar' }} el hallazgo</p>
        </div>

        {{-- Progress Steps --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                @for($i = 1; $i <= $totalSteps; $i++)
                    <div class="flex items-center {{ $i < $totalSteps ? 'flex-1' : '' }}">
                        <div class="flex flex-col items-center">
                            <button wire:click="goToStep({{ $i }})"
                                    class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-colors
                                    {{ $currentStep >= $i ? 'bg-ep-primary text-white' : 'bg-gray-200 text-gray-600' }}
                                    {{ $currentStep == $i ? 'ring-4 ring-ep-primary ring-opacity-30' : '' }}">
                                {{ $i }}
                            </button>
                            <span class="text-xs mt-2 text-center
                                {{ $currentStep == $i ? 'text-ep-primary font-semibold' : 'text-gray-600' }}">
                                @if($i == 1) Información
                                @elseif($i == 2) Clasificación
                                @elseif($i == 3) Origen
                                @else Riesgo
                                @endif
                            </span>
                        </div>
                        @if($i < $totalSteps)
                            <div class="flex-1 h-1 mx-2 {{ $currentStep > $i ? 'bg-ep-primary' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        {{-- Form Content --}}
        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit.prevent="save">
                {{-- Paso 1: Información Básica --}}
                @if($currentStep == 1)
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-ep-dark mb-4">Información Básica</h2>

                        {{-- Título --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Título del Hallazgo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="titulo"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary"
                                   placeholder="Ej: Escalones oxidados en escalera principal">
                            @error('titulo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Descripción --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción Detallada <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="descripcion"
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary"
                                      placeholder="Describa el hallazgo en detalle..."></textarea>
                            @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Ubicación Específica --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ubicación Específica
                            </label>
                            <input type="text" 
                                   wire:model="ubicacion_especifica"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary"
                                   placeholder="Ej: Escalera principal, 2do piso, sector norte">
                            @error('ubicacion_especifica') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Sede --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sede <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="sede_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                                <option value="">Seleccione una sede</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                            @error('sede_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                {{-- Paso 2: Clasificación --}}
                @if($currentStep == 2)
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-ep-dark mb-4">Clasificación y Plazo</h2>

                        {{-- Criticidad --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nivel de Criticidad <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="criticidad" value="bajo" class="peer sr-only">
                                    <div class="p-4 border-2 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50">
                                        <div class="text-2xl mb-1">🟢</div>
                                        <div class="font-semibold">Bajo</div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="criticidad" value="medio" class="peer sr-only">
                                    <div class="p-4 border-2 rounded-lg text-center transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                        <div class="text-2xl mb-1">🟡</div>
                                        <div class="font-semibold">Medio</div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="criticidad" value="alto" class="peer sr-only">
                                    <div class="p-4 border-2 rounded-lg text-center transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50">
                                        <div class="text-2xl mb-1">🟠</div>
                                        <div class="font-semibold">Alto</div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model="criticidad" value="critico" class="peer sr-only">
                                    <div class="p-4 border-2 rounded-lg text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50">
                                        <div class="text-2xl mb-1">🔴</div>
                                        <div class="font-semibold">Crítico</div>
                                    </div>
                                </label>
                            </div>
                            @error('criticidad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Estado Inicial <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="estado"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="resuelto">Resuelto</option>
                                <option value="verificado">Verificado</option>
                                <option value="cerrado">Cerrado</option>
                            </select>
                            @error('estado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Plazo de Corrección --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Plazo de Corrección <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   wire:model="plazo_correccion"
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary">
                            @error('plazo_correccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Responsable de Corrección --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Responsable de la Corrección
                            </label>
                            <input type="text" 
                                   wire:model="responsable_correccion"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary"
                                   placeholder="Ej: Departamento de Mantenimiento">
                            @error('responsable_correccion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                {{-- Paso 3: Origen (Opcional) --}}
                @if($currentStep == 3)
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-ep-dark mb-4">Origen del Hallazgo (Opcional)</h2>
                        <p class="text-gray-600 mb-4">Si este hallazgo fue detectado durante una inspección o visita, puede vincularlo aquí.</p>

                        {{-- Tipo de Origen --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Origen
                            </label>
                            <select wire:model.live="origen_type"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                                <option value="">Manual (sin vincular)</option>
                                <option value="App\Models\DynamicInspection">Inspección de Checklist</option>
                                <option value="App\Models\GeneradorConstancia">Constancia de Visita</option>
                            </select>
                        </div>

                        {{-- Selección de Inspección --}}
                        @if($origen_type === 'App\Models\DynamicInspection')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Seleccionar Inspección
                                </label>
                                <select wire:model="origen_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                                    <option value="">Seleccione una inspección</option>
                                    @foreach($inspecciones as $inspeccion)
                                        <option value="{{ $inspeccion->id }}">
                                            {{ $inspeccion->checklist->title ?? 'Sin título' }} - 
                                            {{ $inspeccion->completed_at?->format('d/m/Y') ?? 'En proceso' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('origen_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- Selección de Constancia --}}
                        @if($origen_type === 'App\Models\GeneradorConstancia')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Seleccionar Constancia de Visita
                                </label>
                                <select wire:model="origen_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                                    <option value="">Seleccione una constancia</option>
                                    @foreach($constancias as $constancia)
                                        <option value="{{ $constancia->id }}">
                                            {{ $constancia->sede->nombre_sede ?? 'Sin sede' }} - 
                                            {{ $constancia->created_at->format('d/m/Y') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('origen_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Paso 4: Riesgo (Opcional) --}}
                @if($currentStep == 4)
                    <div class="space-y-4">
                        <h2 class="text-xl font-bold text-ep-dark mb-4">Vinculación con Matriz de Riesgos (Opcional)</h2>
                        <p class="text-gray-600 mb-4">Puede vincular este hallazgo con un riesgo existente en la matriz de riesgos.</p>

                        {{-- Selección de Riesgo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Riesgo Asociado
                            </label>
                            <select wire:model="risk_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                                <option value="">Sin vincular a riesgo</option>
                                @foreach($risks as $risk)
                                    <option value="{{ $risk->id }}">
                                        {{ $risk->hazard }} ({{ $risk->type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('risk_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-1">Información</p>
                                    <p>Al vincular con un riesgo, podrá hacer seguimiento conjunto del hallazgo y el riesgo asociado. Esto es útil para gestión preventiva.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Botones de Navegación --}}
                <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                    <div>
                        @if($currentStep > 1)
                            <button type="button" 
                                    wire:click="previousStep"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                ← Anterior
                            </button>
                        @else
                            <a href="{{ route('hallazgos.index') }}"
                               class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors inline-block">
                                Cancelar
                            </a>
                        @endif
                    </div>

                    <div>
                        @if($currentStep < $totalSteps)
                            <button type="button" 
                                    wire:click="nextStep"
                                    class="px-6 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark transition-colors">
                                Siguiente →
                            </button>
                        @else
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                💾 {{ $hallazgoId ? 'Actualizar' : 'Crear' }} Hallazgo
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
