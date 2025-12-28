<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Subcategorías de {{ $category->name }}</h2>
                <p class="text-gray-600 mt-1">Código: <span class="font-mono">{{ $category->code }}</span></p>
            </div>
            <a href="{{ route('categories.index') }}" class="text-blue-600 hover:text-blue-800">
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
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            {{ $showForm ? 'Cancelar' : '+ Nueva Subcategoría' }}
        </button>
    </div>

    <!-- Formulario para nueva subcategoría -->
    @if($showForm)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Nueva Subcategoría</h3>
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subcategories as $subcategory)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($editingId === $subcategory->id)
                                <input 
                                    wire:model="editCode" 
                                    type="text" 
                                    class="w-full rounded-md border-gray-300 shadow-sm text-sm"
                                >
                            @else
                                <span class="font-mono text-sm">{{ $subcategory->code }}</span>
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
                                <span class="font-medium">{{ $subcategory->name }}</span>
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
                                <span class="text-sm text-gray-600">{{ $subcategory->description }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($editingId === $subcategory->id)
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
                                        wire:click="edit({{ $subcategory->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900">
                                        Editar
                                    </button>
                                    <button 
                                        wire:click="delete({{ $subcategory->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta subcategoría?"
                                        class="text-red-600 hover:text-red-900">
                                        Eliminar
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No hay subcategorías creadas. Haz clic en "Nueva Subcategoría" para agregar una.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
