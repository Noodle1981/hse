<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ auth()->user()->rol === 'Administrador' ? route('hallazgos.index') : route('mis-hallazgos') }}" 
                       class="text-gray-600 hover:text-gray-900">
                        ← Volver
                    </a>
                </div>
                <h1 class="text-3xl font-bold text-ep-dark">Hallazgo #{{ $hallazgo->visit_counter }} • {{ $hallazgo->titulo }}</h1>
                <p class="text-gray-600 mt-2">ID Global #{{ $hallazgo->id }} • Creado el {{ $hallazgo->created_at->format('d/m/Y H:i') }} por {{ $hallazgo->user->nombres }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('hallazgos.pdf', $hallazgo) }}" 
                   class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar PDF
                </a>
                @if(auth()->user()->rol === 'Administrador')
                    <a href="{{ route('hallazgos.edit', $hallazgo) }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Editar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna Principal --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Información Principal --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-ep-dark mb-4">Información del Hallazgo</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Criticidad</label>
                        <div class="mt-1">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($hallazgo->criticidad === 'critico') bg-red-100 text-red-800
                                @elseif($hallazgo->criticidad === 'alto') bg-orange-100 text-orange-800
                                @elseif($hallazgo->criticidad === 'medio') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($hallazgo->criticidad) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Estado</label>
                        <div class="mt-1">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($hallazgo->estado === 'cerrado') bg-green-100 text-green-800
                                @elseif($hallazgo->estado === 'verificado') bg-blue-100 text-blue-800
                                @elseif($hallazgo->estado === 'resuelto') bg-cyan-100 text-cyan-800
                                @elseif($hallazgo->estado === 'en_proceso') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $hallazgo->estado)) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Sede</label>
                        <p class="mt-1 text-gray-900">{{ $hallazgo->sede->nombre_sede }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Plazo de Corrección</label>
                        <p class="mt-1 {{ $hallazgo->estaVencido() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                            {{ $hallazgo->plazo_correccion?->format('d/m/Y') ?? '-' }}
                            @if($hallazgo->estaVencido())
                                <span class="text-xs">⚠️ VENCIDO</span>
                            @endif
                        </p>
                    </div>

                    @if($hallazgo->ubicacion_especifica)
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-600">Ubicación Específica</label>
                        <p class="mt-1 text-gray-900">📍 {{ $hallazgo->ubicacion_especifica }}</p>
                    </div>
                    @endif

                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-600">Descripción</label>
                        <p class="mt-1 text-gray-900">{{ $hallazgo->descripcion }}</p>
                    </div>

                    @if($hallazgo->responsable_correccion)
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-600">Responsable de Corrección</label>
                        <p class="mt-1 text-gray-900">{{ $hallazgo->responsable_correccion }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Timeline de Seguimientos --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-ep-dark">Seguimientos</h2>
                    <button wire:click="$set('showSeguimientoForm', true)"
                            class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark text-sm">
                        + Agregar Seguimiento
                    </button>
                </div>

                @if($showSeguimientoForm)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold mb-3">Nuevo Seguimiento</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Estado</label>
                                <select wire:model="nuevoEstado" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="resuelto">Resuelto</option>
                                    <option value="verificado">Verificado</option>
                                    <option value="cerrado">Cerrado</option>
                                </select>
                                @error('nuevoEstado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Comentario</label>
                                <textarea wire:model="comentarioSeguimiento" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                          placeholder="Describa el seguimiento..."></textarea>
                                @error('comentarioSeguimiento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="agregarSeguimiento" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Guardar
                                </button>
                                <button wire:click="$set('showSeguimientoForm', false)" 
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    @forelse($hallazgo->seguimientos as $seguimiento)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-ep-primary text-white flex items-center justify-center font-bold">
                                    {{ substr($seguimiento->user->nombres, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-semibold">{{ $seguimiento->user->nombres }}</p>
                                            <p class="text-sm text-gray-600">{{ $seguimiento->fecha_seguimiento->format('d/m/Y H:i') }}</p>
                                        </div>
                                        @if($seguimiento->estado_anterior !== $seguimiento->estado_nuevo)
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                                {{ ucfirst($seguimiento->estado_anterior) }} → {{ ucfirst($seguimiento->estado_nuevo) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-700">{{ $seguimiento->comentario }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No hay seguimientos registrados</p>
                    @endforelse
                </div>
            </div>

            {{-- Galería de Fotos --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-ep-dark">Evidencia Fotográfica</h2>
                    <button wire:click="$set('showFotoForm', true)"
                            class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark text-sm">
                        + Subir Foto
                    </button>
                </div>

                @if($showFotoForm)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="font-semibold mb-3">Subir Nueva Foto</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Foto</label>
                                <select wire:model="tipoFoto" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="antes">Antes de la corrección</option>
                                    <option value="durante">Durante la corrección</option>
                                    <option value="despues">Después de la corrección</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto (máx 5MB)</label>
                                <input type="file" wire:model="nuevaFoto" accept="image/*" class="w-full">
                                @error('nuevaFoto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                                <input type="text" wire:model="descripcionFoto" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                       placeholder="Descripción de la foto">
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="subirFoto" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Subir
                                </button>
                                <button wire:click="$set('showFotoForm', false)" 
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($hallazgo->fotos as $foto)
                        <div class="relative group border rounded-lg overflow-hidden bg-gray-100">
                            <img src="{{ $foto->url }}" 
                                 alt="{{ $foto->descripcion }}"
                                 class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 bg-black bg-opacity-70 text-white text-[10px] uppercase font-bold rounded">
                                    {{ ucfirst($foto->tipo) }}
                                </span>
                            </div>
                            <button wire:click="eliminarFoto({{ $foto->id }})"
                                    wire:confirm="¿Eliminar esta foto?"
                                    class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @if($foto->descripcion)
                                <div class="absolute bottom-0 left-0 right-0 p-2 bg-black bg-opacity-50 text-white text-xs">
                                    {{ $foto->descripcion }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500">
                            No hay fotos cargadas
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Acciones Rápidas --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-ep-dark mb-4">Cambiar Estado</h3>
                <div class="space-y-2">
                    @if($hallazgo->estado !== 'en_proceso')
                        <button wire:click="cambiarEstadoRapido('en_proceso')" 
                                class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 text-sm">
                            → En Proceso
                        </button>
                    @endif
                    @if($hallazgo->estado !== 'resuelto')
                        <button wire:click="cambiarEstadoRapido('resuelto')" 
                                class="w-full px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 text-sm">
                            ✓ Resuelto
                        </button>
                    @endif
                    @if($hallazgo->estado !== 'cerrado')
                        <button wire:click="cambiarEstadoRapido('cerrado')" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            ✓✓ Cerrado
                        </button>
                    @endif
                </div>
            </div>

            {{-- Información Adicional --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-ep-dark mb-4">Información Adicional</h3>
                <div class="space-y-3 text-sm">
                </div>
            </div>
        </div>
    </div>

    {{-- Toast de éxito --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    @endif
</div>
