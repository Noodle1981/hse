<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Sedes de {{ $empresa->nombre }}</h2>
                <p class="text-gray-600 mt-1">CUIT: <span class="font-mono">{{ $empresa->cuit }}</span></p>
            </div>
            <a href="{{ route('companies.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Volver a empresas
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Botón para agregar nueva sede -->
    <div class="mb-4">
        <button 
            wire:click="toggleForm" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            {{ $showForm ? 'Cancelar' : '+ Nueva Sede' }}
        </button>
    </div>

    <!-- Formulario para nueva sede -->
    @if($showForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Nueva Sede</h3>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Sede <span class="text-red-500">*</span>
                        </label>
                        <input 
                            wire:model="newNombreSede" 
                            type="text" 
                            placeholder="Sede Central"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('newNombreSede') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Sede
                        </label>
                        <input 
                            wire:model="newTipoSede" 
                            type="text" 
                            placeholder="Oficina, Planta, Depósito..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección
                        </label>
                        <input 
                            wire:model="newDireccion" 
                            type="text" 
                            placeholder="Calle 123"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Localidad
                        </label>
                        <input 
                            wire:model="newLocalidad" 
                            type="text" 
                            placeholder="Ciudad, Provincia"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono
                        </label>
                        <input 
                            wire:model="newTelefono" 
                            type="text" 
                            placeholder="011-1234-5678"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Correo
                        </label>
                        <input 
                            wire:model="newCorreo" 
                            type="email" 
                            placeholder="sede@empresa.com"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        @error('newCorreo') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button 
                        type="button"
                        wire:click="toggleForm"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove>Crear Sede</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Lista de sedes -->
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dirección</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Localidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sedes as $sede)
                    <tr>
                        <td class="px-6 py-4">
                            @if($editingId === $sede->id)
                                <input 
                                    wire:model="editNombreSede" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="font-medium">{{ $sede->nombre_sede }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($editingId === $sede->id)
                                <input 
                                    wire:model="editTipoSede" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="text-sm text-gray-600">{{ $sede->tipo_sede ?: '-' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($editingId === $sede->id)
                                <input 
                                    wire:model="editDireccion" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="text-sm text-gray-600">{{ $sede->direccion ?: '-' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($editingId === $sede->id)
                                <input 
                                    wire:model="editLocalidad" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="text-sm text-gray-600">{{ $sede->localidad ?: '-' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($editingId === $sede->id)
                                <div class="flex gap-2">
                                    <button 
                                        wire:click="update" 
                                        class="text-green-600 hover:text-green-900">
                                        Guardar
                                    </button>
                                    <button 
                                        wire:click="cancelEdit" 
                                        class="text-gray-600 hover:text-gray-900">
                                        Cancelar
                                    </button>
                                </div>
                            @else
                                <div class="flex gap-2">
                                    <button 
                                        wire:click="edit({{ $sede->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                        Editar
                                    </button>
                                    <button 
                                        wire:click="delete({{ $sede->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta sede?"
                                        class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No hay sedes creadas. Haz clic en "Nueva Sede" para agregar una.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
