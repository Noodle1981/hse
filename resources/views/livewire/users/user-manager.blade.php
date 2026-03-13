<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gestión de Usuarios</h1>
        <p class="text-gray-600 mt-1">Administra técnicos y administradores del sistema</p>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtros y Acciones -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
            <div class="w-full md:w-1/3">
                <input type="text" wire:model.live="search" 
                       placeholder="Buscar por nombre, email o matrícula..."
                       class="w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 w-full md:w-auto items-start sm:items-center justify-end">
                <div class="flex items-center mr-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="showDeleted" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Ver Eliminados</span>
                    </label>
                </div>

                <select wire:model.live="roleFilter" class="w-full sm:w-auto rounded-md border-gray-300 shadow-sm">
                    <option value="all">Todos los roles</option>
                    <option value="gestor">Gestores</option>
                    <option value="tecnico">Técnicos</option>
                </select>
                <button wire:click="toggleForm" 
                        class="w-full sm:w-auto justify-center hover:bg-opacity-90 text-white px-4 py-2 rounded-md whitespace-nowrap transition-colors duration-200 flex items-center gap-2 shadow-sm"
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
                        Nuevo Usuario
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    @if($showForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">{{ $editingId ? 'Editar Usuario' : 'Nuevo Usuario' }}</h3>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nombres <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nombres" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                        @error('nombres') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Apellidos <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="apellidos" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                        @error('apellidos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" wire:model="email" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña {{ $editingId ? '(dejar vacío para no cambiar)' : '*' }}
                        </label>
                        <input type="password" wire:model="password" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="rol" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="tecnico">Técnico</option>
                            <option value="gestor">Gestor</option>
                        </select>
                        @error('rol') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Matrícula <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="matricula" 
                               class="w-full rounded-md border-gray-300 shadow-sm"
                               placeholder="MAT-001">
                        @error('matricula') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="telefono" 
                               class="w-full rounded-md border-gray-300 shadow-sm"
                               placeholder="011-1234-5678">
                        @error('telefono') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="estado" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                        @error('estado') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" wire:click="toggleForm"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="hover:bg-opacity-90 text-white px-4 py-2 rounded-md transition-colors duration-200 shadow-sm disabled:opacity-50"
                            style="background-color: #292938;">
                        <span wire:loading.remove>{{ $editingId ? 'Actualizar' : 'Crear' }} Usuario</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matrícula</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 {{ $user->trashed() ? 'line-through text-red-500' : '' }}">
                                {{ $user->apellidos }}, {{ $user->nombres }}
                                @if($user->trashed()) <span class="text-xs text-red-500 ml-1">(Baja)</span> @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 {{ $user->isGestor() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }} rounded-full text-xs font-medium">
                                {{ $user->rol === 'gestor' ? 'Gestor' : 'Técnico' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->matricula ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 {{ ($user->estado === 'activo' && !$user->trashed()) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-xs font-medium">
                                {{ $user->trashed() ? 'Inactivo' : ucfirst($user->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-3">
                                @if($user->trashed())
                                    <button wire:click="restore({{ $user->id }})"
                                            class="text-green-600 hover:text-green-900 transition-colors duration-150 p-1 hover:bg-green-50 rounded"
                                            title="Restaurar Usuario">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="edit({{ $user->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150 p-1 hover:bg-indigo-50 rounded"
                                            title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="confirmDelete({{ $user->id }}, '{{ $user->nombres }} {{ $user->apellidos }}')"
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150 p-1 hover:bg-red-50 rounded"
                                                title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay usuarios. Crea uno nuevo para comenzar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de Confirmación de Baja (Soft Delete) -->
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
                        ¿Dar de baja al usuario?
                    </h3>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Estás a punto de dar de baja a <span class="font-bold text-gray-700">{{ $userToDeleteName }}</span>.
                        <br><br>
                        ⚠️ <span class="font-semibold">Nota Importante:</span> Este usuario NO se eliminará permanentemente de la base de datos para preservar el historial de inspecciones y reportes.
                        <br>
                        Sin embargo, perderá acceso inmediato al sistema.
                    </p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelDelete" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors font-medium">
                            Salir
                        </button>
                        <button wire:click="delete" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors font-bold shadow-sm">
                            Revocar Acceso
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
