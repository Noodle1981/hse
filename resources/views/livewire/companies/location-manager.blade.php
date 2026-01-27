<div>
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Sedes de {{ $empresa->nombre }}</h2>
                <p class="text-gray-600 mt-1">CUIT: <span class="font-mono">{{ $empresa->cuit }}</span></p>
            </div>
            <a href="{{ route('gestor.companies.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Volver a empresas
            </a>
        </div>
        
        <div class="flex justify-end">
             <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="showDeleted" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600"></div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Ver Eliminados</span>
            </label>
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
            class="hover:bg-opacity-90 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center gap-2 shadow-sm"
            style="background-color: #292938;">
            @if($showForm)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Cancelar
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Crear Sede
            @endif
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
                            list="tipos-list"
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
                            Provincia
                        </label>
                        <select 
                            wire:model="newProvinciaId" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Seleccione una provincia...</option>
                            @foreach($existingProvincias as $provincia)
                                <option value="{{ $provincia->id }}">{{ $provincia->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Localidad
                        </label>
                        <input 
                            wire:model="newLocalidad" 
                            type="text" 
                            list="localidades-list"
                            placeholder="Ciudad, Provincia"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Coordenadas (GPS)
                        </label>
                        <div class="flex gap-2">
                            <input 
                                wire:model="newLatitud" 
                                type="text" 
                                placeholder="Latitud (-34.6037)"
                                class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            <input 
                                wire:model="newLongitud" 
                                type="text" 
                                placeholder="Longitud (-58.3816)"
                                class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>
                        @error('newLatitud') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('newLongitud') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

                    <!-- Datalists para autocompletado -->
                    <datalist id="tipos-list">
                        @foreach($existingTipos as $tipo)
                            <option value="{{ $tipo }}">
                        @endforeach
                    </datalist>

                    <datalist id="localidades-list">
                        @foreach($existingLocalidades as $localidad)
                            <option value="{{ $localidad }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button 
                        type="button"
                        wire:click="toggleForm"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="hover:bg-opacity-90 text-white px-4 py-2 rounded-md transition-colors duration-200 shadow-sm disabled:opacity-50"
                        style="background-color: #292938;">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Técnicos</th>
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
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm mb-1"
                                    placeholder="Nombre"
                                >
                            @else
                                <span class="font-medium {{ $sede->trashed() ? 'line-through text-red-500' : '' }}">
                                    {{ $sede->nombre_sede }}
                                    @if($sede->trashed()) <span class="text-xs text-red-500 ml-1">(Baja)</span> @endif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($editingId === $sede->id)
                                <input 
                                    wire:model="editTipoSede" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                    placeholder="Tipo"
                                >
                            @else
                                <span class="text-sm text-gray-600">{{ $sede->tipo_sede ?: '-' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($editingId === $sede->id)
                                <div class="space-y-2">
                                    <input wire:model="editDireccion" type="text" class="w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Dirección">
                                    <input wire:model="editLocalidad" type="text" class="w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Localidad">
                                    <select wire:model="editProvinciaId" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="">Provincia...</option>
                                        @foreach($existingProvincias as $provincia)
                                            <option value="{{ $provincia->id }}">{{ $provincia->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex gap-1">
                                        <input wire:model="editLatitud" type="text" class="w-1/2 rounded-md border-gray-300 shadow-sm text-sm" placeholder="Lat">
                                        <input wire:model="editLongitud" type="text" class="w-1/2 rounded-md border-gray-300 shadow-sm text-sm" placeholder="Lon">
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-gray-600">
                                    <div>{{ $sede->direccion ?: 'Sin dirección' }}</div>
                                    <div>
                                        {{ $sede->localidad ?: '' }}
                                        {{ ($sede->localidad && $sede->provincia) ? ',' : '' }}
                                        {{ $sede->provincia ? $sede->provincia->nombre : '' }}
                                    </div>
                                    @if($sede->latitud && $sede->longitud)
                                        <div class="text-xs text-gray-400 mt-1">
                                            GPS: {{ number_format($sede->latitud, 4) }}, {{ number_format($sede->longitud, 4) }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($sede->users->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sede->users as $user)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ substr($user->nombres, 0, 1) }}. {{ $user->apellidos }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-500 italic">Sin asignar</span>
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
                                <div class="flex items-center gap-3">
                                    @if($sede->trashed())
                                        <button wire:click="restore({{ $sede->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors duration-150 p-1 hover:bg-green-50 rounded"
                                                title="Restaurar Sede">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                    @else
                                        <button 
                                            wire:click="manageUsers({{ $sede->id }})" 
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium pr-3 border-r border-gray-200"
                                            title="Gestionar Técnicos">
                                            Técnicos
                                        </button>
                                        <button 
                                            wire:click="edit({{ $sede->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150 p-1 hover:bg-indigo-50 rounded"
                                            title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="confirmDelete({{ $sede->id }}, '{{ $sede->nombre_sede }}')" 
                                            class="text-red-600 hover:text-red-900 transition-colors duration-150 p-1 hover:bg-red-50 rounded"
                                            title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay sedes creadas. Haz clic en "Nueva Sede" para agregar una.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal de gestión de usuarios -->
    @if($managingUsersForSede)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Asignar Técnicos</h3>
                    <button wire:click="closeUserManagement" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    @if(count($availableUsers) > 0)
                        <div class="space-y-2">
                            @foreach($availableUsers as $user)
                                <label class="flex items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleUser({{ $user->id }})"
                                        {{ in_array($user->id, $assignedUsers) ? 'checked' : '' }}
                                        class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                    >
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">
                                            {{ $user->apellidos }}, {{ $user->nombres }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">
                            No hay técnicos disponibles. Crea usuarios con rol "Técnico" primero.
                        </p>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button 
                        wire:click="closeUserManagement"
                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
    <!-- Modal de confirmación de eliminación -->
    @if($confirmingDeletion)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900 mb-2">
                        ¿Dar de baja a la sede?
                    </h3>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Estás a punto de dar de baja a <span class="font-bold text-gray-700">{{ $sedeToDeleteName }}</span>.
                        <br><br>
                        ⚠️ <span class="font-semibold">Nota Importante:</span> La sede NO se eliminará permanentemente para preservar el historial de inspecciones y reportes.
                    </p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelDelete" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors font-medium">
                            Salir
                        </button>
                        <button wire:click="delete" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors font-bold shadow-sm">
                            Dar de Baja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
