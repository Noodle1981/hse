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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" wire:model.live="search" 
                       placeholder="Buscar por nombre, email o matrícula..."
                       class="w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="flex gap-2">
                <select wire:model.live="roleFilter" class="flex-1 rounded-md border-gray-300 shadow-sm">
                    <option value="all">Todos los roles</option>
                    <option value="Administrador">Administradores</option>
                    <option value="Técnico">Técnicos</option>
                </select>
                <button wire:click="toggleForm" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md whitespace-nowrap">
                    {{ $showForm ? 'Cancelar' : '+ Nuevo Usuario' }}
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
                            <option value="Técnico">Técnico</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                        @error('rol') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Matrícula</label>
                        <input type="text" wire:model="matricula" 
                               class="w-full rounded-md border-gray-300 shadow-sm"
                               placeholder="MAT-001">
                        @error('matricula') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
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
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove>{{ $editingId ? 'Actualizar' : 'Crear' }} Usuario</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
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
                            <div class="font-medium text-gray-900">
                                {{ $user->apellidos }}, {{ $user->nombres }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 {{ $user->rol === 'Administrador' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }} rounded-full text-xs font-medium">
                                {{ $user->rol }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->matricula ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 {{ $user->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full text-xs font-medium">
                                {{ ucfirst($user->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex gap-2">
                                <button wire:click="edit({{ $user->id }})"
                                        class="text-indigo-600 hover:text-indigo-900">
                                    Editar
                                </button>
                                @if($user->id !== auth()->id())
                                    <button wire:click="delete({{ $user->id }})"
                                            wire:confirm="¿Estás seguro de eliminar este usuario?"
                                            class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
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
</div>
