<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Subcategorías de {{ $category->name }}</h2>
                <p class="text-gray-600 mt-1">Código: <span class="font-mono">{{ $category->code }}</span></p>
            </div>
            <a href="{{ route('gestor.categories.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Volver a categorías
            </a>
        </div>
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

    <!-- Botón para agregar nueva subcategoría -->
    <div class="mb-4">
        <button 
            wire:click="toggleForm" 
            class="bg-[#1E1E2D] hover:bg-[#2d2d3f] text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ $showForm ? 'Cancelar' : 'Nueva Subcategoría' }}
        </button>
    </div>

    <!-- Formulario para nueva subcategoría -->
    @if($showForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6 border-l-4 border-[#1E1E2D]">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Nueva Subcategoría</h3>
            <form wire:submit="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input 
                            wire:model="newCode" 
                            type="text" 
                            placeholder="electricidad"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E1E2D] focus:ring-[#1E1E2D]"
                        >
                        @error('newCode') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input 
                            wire:model="newName" 
                            type="text" 
                            placeholder="Electricidad"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E1E2D] focus:ring-[#1E1E2D]"
                        >
                        @error('newName') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea 
                            wire:model="newDescription" 
                            rows="2"
                            placeholder="Descripción de la subcategoría..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E1E2D] focus:ring-[#1E1E2D]"
                        ></textarea>
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
                        class="px-4 py-2 bg-[#1E1E2D] text-white rounded-md hover:bg-[#2d2d3f]">
                        Crear Subcategoría
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Lista de subcategorías -->
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subcategories as $subcategory)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editingId === $subcategory->id)
                                <input 
                                    wire:model="editCode" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="font-mono text-sm text-gray-600">{{ $subcategory->code }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($editingId === $subcategory->id)
                                <input 
                                    wire:model="editName" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="font-medium text-gray-900">{{ $subcategory->name }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($editingId === $subcategory->id)
                                <input 
                                    wire:model="editDescription" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="text-sm text-gray-600 line-clamp-1" title="{{ $subcategory->description }}">{{ $subcategory->description ?: '-' }}</span>
                            @endif
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap">
                            @if($subcategory->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                    Activa
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                    Inactiva
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($editingId === $subcategory->id)
                                <div class="flex gap-2">
                                    <button 
                                        wire:click="update" 
                                        class="text-green-600 hover:text-green-900 font-medium">
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
                                    <button 
                                        wire:click="edit({{ $subcategory->id }})" 
                                        class="text-blue-600 hover:text-blue-800 transition-colors"
                                        title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    
                                    <button wire:click="toggleStatus({{ $subcategory->id }})" 
                                            class="{{ $subcategory->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }} transition-colors"
                                            title="{{ $subcategory->is_active ? 'Desactivar' : 'Activar' }}">
                                        @if($subcategory->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No hay subcategorías creadas. Haz clic en "Nueva Subcategoría" para agregar una.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
