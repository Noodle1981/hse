<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.consultants') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Usuarios de {{ $consultant->nombre }}</h2>
            </div>
            <p class="text-sm text-gray-500 ml-7">Administra los usuarios pertenecientes a esta consultora.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg shadow-sm hover:bg-indigo-700 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nuevo Usuario
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Usuario</th>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Rol</th>
                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                    <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ substr($user->nombres, 0, 1) }}{{ substr($user->apellidos, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-bold text-gray-900">{{ $user->nombres }} {{ $user->apellidos }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->isGestor() ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->rol }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($user->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                        <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold transition-colors">
                            Editar
                        </button>
                        <button onclick="confirm('¿Estás seguro de eliminar este usuario?') || event.stopImmediatePropagation()" wire:click="delete({{ $user->id }})" class="text-red-600 hover:text-red-900 text-xs font-bold transition-colors">
                            Eliminar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay usuarios</h3>
                        <p class="mt-1 text-sm text-gray-500">Comienza creando un usuario para esta consultora.</p>
                        <div class="mt-6">
                            <button wire:click="create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Crear Usuario
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 opacity-75" wire:click="$set('showModal', false)"></div>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden max-w-lg w-full z-10">
                <form wire:submit.prevent="save">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">{{ $editingId ? 'Editar Usuario' : 'Nuevo Usuario' }}</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombres</label>
                                <input type="text" wire:model="nombres" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @error('nombres') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Apellidos</label>
                                <input type="text" wire:model="apellidos" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @error('apellidos') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Email</label>
                            <input type="email" wire:model="email" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('email') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">
                                Contraseña
                                @if($editingId) <span class="text-gray-300 font-medium normal-case">(Opcional para mantener actual)</span> @endif
                            </label>
                            <input type="password" wire:model="password" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('password') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Rol</label>
                            <select wire:model="rol" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="gestor">Gestor</option>
                                <option value="tecnico">Técnico</option>
                            </select>
                            @error('rol') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Cancelar</button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg shadow-sm hover:bg-indigo-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
