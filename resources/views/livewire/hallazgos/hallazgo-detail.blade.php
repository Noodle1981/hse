<div>
    {{-- Header --}}
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

                    <h1 class="text-lg md:text-xl font-medium text-white tracking-tight italic">
                        Hallazgo <span class="text-ep-primary">#{{ $hallazgo->visit_counter }}</span> • {{ $hallazgo->titulo }}
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.hallazgos.index') : route('tecnico.hallazgos.index') }}" 
                       class="hidden xs:flex bg-white/10 text-white hover:bg-white/20 px-3 py-1 rounded-full text-[10px] font-black border border-white/10 transition-all items-center gap-1 uppercase tracking-widest">
                        <svg class="w-3.5 h-3.5 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver
                    </a>

                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.hallazgos.pdf', $hallazgo) : route('tecnico.hallazgos.pdf', $hallazgo) }}" 
                       class="hidden sm:flex bg-ep-primary text-white hover:brightness-110 px-3 py-1 rounded-full text-[10px] font-black border border-white/10 transition-all items-center gap-1 uppercase tracking-widest shadow-lg shadow-ep-primary/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        PDF
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->hasRole(['Técnico', 'tecnico']))
                        <a href="{{ auth()->user()->isAdmin() ? route('gestor.hallazgos.edit', $hallazgo) : route('tecnico.hallazgos.edit', $hallazgo) }}" 
                           class="bg-white/5 text-white hover:bg-white/10 px-3 py-1 rounded-full text-[10px] font-black border border-white/10 transition-all items-center gap-1 uppercase tracking-widest">
                            <svg class="w-3.5 h-3.5 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Editar
                        </a>
                    @endif
                </div>
            </div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-6">

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
                        <p class="mt-1 text-gray-900">{{ $hallazgo->ubicacion_especifica }}</p>
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
            <div class="bg-white rounded-lg shadow p-6" x-data="{ openModal: false, modalImage: '', modalDesc: '' }">
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
                                 @click="openModal = true; modalImage = '{{ $foto->url }}'; modalDesc = '{{ $foto->descripcion }}'"
                                 class="w-full h-48 object-cover cursor-pointer transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 bg-black bg-opacity-70 text-white text-[10px] uppercase font-bold rounded">
                                    {{ ucfirst($foto->tipo) }}
                                </span>
                            </div>
                            <button @click="$dispatch('open-confirm-modal', { 
                                        id: {{ $foto->id }}, 
                                        title: '¿Eliminar Foto?', 
                                        message: '¿Estás seguro de eliminar esta evidencia fotográfica? Esta acción no se puede deshacer.',
                                        action: 'eliminarFoto',
                                        confirmLabel: 'Eliminar',
                                        confirmColor: 'bg-red-600 shadow-red-200'
                                    })"
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

                <!-- Modal de Previsualización -->
                <div x-show="openModal" 
                     class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-80 flex items-center justify-center p-4"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="display: none;"
                     @keydown.escape.window="openModal = false">
                    <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col" @click.away="openModal = false">
                        <!-- Botón Cerrar -->
                        <button @click="openModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 bg-white rounded-full p-1.5 shadow-md z-10 transition-transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        
                        <!-- Imagen -->
                        <div class="overflow-hidden flex items-center justify-center bg-gray-100 flex-1">
                            <img :src="modalImage" class="max-w-full max-h-[70vh] object-contain">
                        </div>
                        
                        <!-- Pie de Modal (Descripción y Descarga) -->
                        <div class="p-4 bg-white border-t border-gray-200 flex justify-between items-center">
                            <p class="text-sm text-gray-700 flex-1 mr-4 font-medium" x-text="modalDesc || 'Sin descripción'"></p>
                            <a :href="modalImage" download class="flex-shrink-0 px-4 py-2 bg-ep-primary hover:bg-ep-primary-dark text-white text-sm font-semibold rounded-lg flex items-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Descargar
                            </a>
                        </div>
                    </div>
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
                        <button wire:click="prepararSeguimiento('en_proceso')" 
                                class="w-full px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                            → En Proceso
                        </button>
                    @endif
                    @if($hallazgo->estado !== 'resuelto')
                        <button wire:click="prepararSeguimiento('resuelto')" 
                                class="w-full px-4 py-2 bg-cyan-600 text-white rounded-xl hover:bg-cyan-700 text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                            ✓ Resuelto
                        </button>
                    @endif
                    @if($hallazgo->estado !== 'cerrado')
                        <button wire:click="prepararSeguimiento('cerrado')" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
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

    <x-modal-confirm />
</div>
