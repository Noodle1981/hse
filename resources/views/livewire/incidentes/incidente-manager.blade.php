<div>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-ep-dark">Gestión de Incidentes y Accidentes</h1>
            <button wire:click="$set('showForm', true)"
                    class="px-6 py-3 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark font-semibold">
                + Registrar Incidente
            </button>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Total Incidentes</div>
            <div class="text-2xl font-bold text-ep-dark">{{ $kpis['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Accidentes</div>
            <div class="text-2xl font-bold text-red-600">{{ $kpis['accidentes'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Graves/Fatales</div>
            <div class="text-2xl font-bold text-orange-600">{{ $kpis['graves'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Días Perdidos</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $kpis['dias_perdidos'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-600">Personas Afectadas</div>
            <div class="text-2xl font-bold text-blue-600">{{ $kpis['personas_afectadas'] }}</div>
        </div>
    </div>

    {{-- Formulario de Registro --}}
    @if($showForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-ep-dark mb-4">Registrar Nuevo Incidente</h2>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                        <select wire:model="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="incidente">Incidente</option>
                            <option value="accidente">Accidente</option>
                            <option value="cuasi_accidente">Cuasi Accidente</option>
                        </select>
                        @error('tipo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gravedad *</label>
                        <select wire:model="gravedad" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="leve">Leve</option>
                            <option value="moderado">Moderado</option>
                            <option value="grave">Grave</option>
                            <option value="fatal">Fatal</option>
                        </select>
                        @error('gravedad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sede *</label>
                        <select wire:model="sede_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Seleccione sede</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                            @endforeach
                        </select>
                        @error('sede_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Ocurrencia *</label>
                        <input type="date" wire:model="fecha_ocurrencia" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @error('fecha_ocurrencia') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Personas Afectadas *</label>
                        <input type="number" wire:model="personas_afectadas" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @error('personas_afectadas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Días Perdidos</label>
                        <input type="number" wire:model="dias_perdidos" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @error('dias_perdidos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hallazgo Relacionado (opcional)</label>
                        <select wire:model="hallazgo_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Sin vincular</option>
                            @foreach($hallazgos as $hallazgo)
                                <option value="{{ $hallazgo->id }}">{{ $hallazgo->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                        <textarea wire:model="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medidas Tomadas *</label>
                        <textarea wire:model="medidas_tomadas" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        @error('medidas_tomadas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Guardar
                    </button>
                    <button type="button" wire:click="$set('showForm', false)"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar..." class="px-4 py-2 border border-gray-300 rounded-lg">
            <select wire:model.live="filterTipo" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todos los tipos</option>
                <option value="accidente">Accidente</option>
                <option value="incidente">Incidente</option>
                <option value="cuasi_accidente">Cuasi Accidente</option>
            </select>
            <select wire:model.live="filterGravedad" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todas las gravedades</option>
                <option value="leve">Leve</option>
                <option value="moderado">Moderado</option>
                <option value="grave">Grave</option>
                <option value="fatal">Fatal</option>
            </select>
            <select wire:model.live="filterSede" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Todas las sedes</option>
                @foreach($sedes as $sede)
                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($incidentes->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <p class="text-xl">No hay incidentes registrados</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-ep-dark text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Gravedad</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-center">Afectados</th>
                        <th class="px-4 py-3 text-center">Días Perdidos</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($incidentes as $incidente)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $incidente->fecha_ocurrencia->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($incidente->tipo === 'accidente') bg-red-100 text-red-800
                                    @elseif($incidente->tipo === 'incidente') bg-orange-100 text-orange-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $incidente->tipo)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($incidente->gravedad === 'fatal') bg-red-100 text-red-800
                                    @elseif($incidente->gravedad === 'grave') bg-orange-100 text-orange-800
                                    @elseif($incidente->gravedad === 'moderado') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($incidente->gravedad) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="max-w-xs truncate">{{ $incidente->descripcion }}</div>
                                <div class="text-xs text-gray-500">{{ $incidente->sede->nombre }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $incidente->personas_afectadas }}</td>
                            <td class="px-4 py-3 text-center">{{ $incidente->dias_perdidos ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="delete({{ $incidente->id }})"
                                        wire:confirm="¿Eliminar este incidente?"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t">
                {{ $incidentes->links() }}
            </div>
        @endif
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('message') }}
        </div>
    @endif
</div>
