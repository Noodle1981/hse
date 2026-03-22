<div x-data="{ openModal: false, modalImage: '', modalDesc: '', showGestion: @entangle('showGestionForm') }">
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 text-green-800 rounded-r-lg shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- Banner de Estado --}}
    @if($incidente->estado === 'pendiente')
        <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded-r-lg flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-medium">Atención: El incidente se encuentra **Pendiente** de carga de Licencia Médica / Cierre.</span>
            </div>
            @if(auth()->user()->isAdmin())
                <button @click="showGestion = true" class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-bold rounded-lg transition-colors">
                    Gestionar Ahora
                </button>
            @endif
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.index') : route('tecnico.incidentes.index') }}" 
               class="text-ep-primary hover:underline text-sm font-semibold flex items-center gap-1">
               ← Volver al Listado
            </a>
            <h1 class="text-3xl font-bold text-ep-dark mt-2">Detalle de Incidente #{{ $incidente->id }}</h1>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isAdmin())
                <button @click="showGestion = true" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Gestionar
                </button>
            @endif
            <a href="{{ auth()->user()->isAdmin() ? route('gestor.incidentes.pdf', $incidente) : route('tecnico.incidentes.pdf', $incidente) }}" 
               class="px-4 py-2 bg-ep-primary hover:bg-ep-primary-dark text-white font-semibold rounded-lg shadow flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar PDF
            </a>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Columna Detalle --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Información de Contexto --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-xl text-ep-dark mb-4 border-b pb-2">Información General</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 font-medium">Empresa Cliente</span>
                        <span class="text-gray-800 font-semibold text-base">{{ $incidente->sede->empresa->nombre ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 font-medium">Sede</span>
                        <span class="text-gray-800 font-semibold text-base">{{ $incidente->sede->nombre_sede ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 font-medium">Fecha de Ocurrencia</span>
                        <span class="text-gray-800 font-semibold text-base">{{ $incidente->fecha_ocurrencia->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 font-medium">Reportado por</span>
                        <span class="text-gray-800 font-semibold text-base">{{ $incidente->user->nombres ?? '' }} {{ $incidente->user->apellidos ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- Detalle del Incidente --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-bold text-xl text-ep-dark border-b pb-2">Descripción del Suceso</h3>
                <div>
                    <span class="block text-gray-500 font-medium mb-1">Tipo de Incidente</span>
                    <span class="px-2.5 py-1 rounded text-xs font-bold uppercase
                        @if($incidente->tipo === 'accidente') bg-red-100 text-red-800
                        @elseif($incidente->tipo === 'incidente') bg-orange-100 text-orange-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ str_replace('_', ' ', $incidente->tipo) }}
                    </span>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium mb-1">Nivel de Gravedad</span>
                    <span class="px-2.5 py-1 rounded text-xs font-bold uppercase
                        @if($incidente->gravedad === 'fatal') bg-red-100 text-red-800
                        @elseif($incidente->gravedad === 'grave') bg-orange-100 text-orange-800
                        @elseif($incidente->gravedad === 'moderado') bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ $incidente->gravedad }}
                    </span>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium mb-1">Descripción de los hechos</span>
                    <p class="p-3 bg-gray-50 rounded-lg border text-gray-700 whitespace-pre-line">{{ $incidente->descripcion }}</p>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium mb-1">Medidas Tomadas / Respuesta Inmediata</span>
                    <p class="p-3 bg-gray-50 rounded-lg border text-gray-700 whitespace-pre-line">{{ $incidente->medidas_tomadas }}</p>
                </div>
            </div>

            {{-- Fotos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-xl text-ep-dark mb-4 border-b pb-2">Evidencia Fotográfica</h3>
                @if($incidente->fotos->isEmpty())
                    <p class="text-gray-500 text-sm italic">No hay fotos registradas para este incidente.</p>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($incidente->fotos as $foto)
                            <div class="group relative cursor-pointer" 
                                 @click="openModal = true; modalImage = '{{ $foto->getUrl() }}'; modalDesc = '{{ $foto->descripcion ?? 'Evidencia del incidente' }}'">
                                <div class="aspect-square bg-gray-100 rounded-lg border overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <img src="{{ $foto->getUrl() }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                @if($foto->descripcion)
                                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $foto->descripcion }}</p>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 flex items-center justify-center transition-all">
                                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Columna Lateral (KPIs o Extras) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center space-y-4">
                <h4 class="font-bold text-ep-dark text-lg border-b pb-1">Daños & Afectados</h4>
                <div>
                    <span class="label-sm text-gray-500 text-xs uppercase font-semibold">Personas Afectadas</span>
                    <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $incidente->personas_afectadas }}</p>
                </div>
                <div class="border-t pt-3">
                    <span class="label-sm text-gray-500 text-xs uppercase font-semibold">Días Perdidos</span>
                    <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $incidente->dias_perdidos ?? '0' }}</p>
                </div>
            </div>
            
            @if($incidente->hallazgo_id)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-ep-dark mb-2">Hallazgo Relacionado</h4>
                    <p class="text-sm text-gray-600">{{ $incidente->hallazgo->descripcion ?? 'Sin descripción' }}</p>
                    <a href="{{ auth()->user()->isAdmin() ? route('gestor.hallazgos.show', $incidente->hallazgo_id) : route('tecnico.hallazgos.show', $incidente->hallazgo_id) }}" 
                       class="inline-block mt-3 text-sm text-ep-primary hover:underline font-semibold">
                        Ver Hallazgo →
                    </a>
                </div>
            @endif
        </div>

        <!-- Ventana Modal de Previsualización -->
        <div x-show="openModal" 
             class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-80 flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="openModal = false">
            <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-lg shadow-2xl overflow-hidden flex flex-col" @click.away="openModal = false">
                <button @click="openModal = false" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 bg-white rounded-full p-1.5 shadow-md z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="overflow-hidden flex items-center justify-center bg-gray-100 flex-1">
                    <img :src="modalImage" class="max-w-full max-h-[70vh] object-contain">
                </div>
                <div class="p-4 bg-white border-t border-gray-200 flex justify-between items-center">
                    <p class="text-sm text-gray-700 flex-1 mr-4 font-medium" x-text="modalDesc"></p>
                    <a :href="modalImage" download class="px-4 py-2 bg-ep-primary hover:bg-ep-primary-dark text-white text-sm font-semibold rounded-lg flex items-center gap-2">
                        Descargar
                    </a>
                </div>
            </div>
        </div>

        </div>

        <!-- Modal de Gestión Administrativa (Admin) -->
        <div x-show="showGestion" 
             class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="showGestion = false">
            <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6" @click.away="showGestion = false">
                <button @click="showGestion = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                
                <h3 class="text-xl font-bold text-ep-dark mb-4">Gestionar Incidente</h3>
                
                <form wire:submit.prevent="actualizarIncidente" class="space-y-4">
                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado de la Gestión</label>
                        <select wire:model="nuevoEstado" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-ep-primary focus:ring-ep-primary">
                            <option value="pendiente">Pendiente</option>
                            <option value="cerrado">Cerrado / Resuelto</option>
                        </select>
                    </div>

                    <!-- Días Perdidos -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Días Perdidos (Suma Total)</label>
                        <input type="number" min="0" wire:model="dias_perdidos" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-ep-primary focus:ring-ep-primary">
                        @error('dias_perdidos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Medidas Tomadas -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Notas de Cierre / Detalle de Licencias</label>
                        <textarea wire:model="medidas_tomadas" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-ep-primary focus:ring-ep-primary" placeholder="Ej: Juan Pérez con 5 días (Cert #1234)..."></textarea>
                        @error('medidas_tomadas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="showGestion = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-ep-primary hover:bg-ep-primary-dark text-white rounded-lg text-sm font-medium shadow">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
