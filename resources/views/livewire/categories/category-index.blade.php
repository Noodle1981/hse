<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Categorías</h2>
        <a href="{{ route('categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            + Nueva Categoría
        </a>
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

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <div class="flex gap-4">
                <input 
                    wire:model.live="search" 
                    type="text" 
                    placeholder="Buscar categorías..."
                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showInactive" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-600">Mostrar inactivas</span>
                </label>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subcategorías</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm">{{ $category->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($category->icon)
                                    <span class="mr-2">🔹</span>
                                @endif
                                <span class="font-medium">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded" style="background-color: {{ $category->color }}"></div>
                                <span class="text-sm text-gray-600">{{ $category->color }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $category->subcategories_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Activa
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactiva
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('categories.subcategories', $category) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    Subcategorías
                                </a>
                                <a href="{{ route('categories.edit', $category) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    Editar
                                </a>
                                <button wire:click="delete({{ $category->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta categoría?"
                                        class="text-red-600 hover:text-red-900">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron categorías
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>
