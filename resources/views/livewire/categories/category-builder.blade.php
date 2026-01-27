<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $category ? 'Editar Categoría' : 'Nueva Categoría' }}
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Código <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="code" 
                        type="text" 
                        placeholder="hys"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('code') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="name" 
                        type="text" 
                        placeholder="Higiene y Seguridad Laboral"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('name') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea 
                        wire:model="description" 
                        rows="3"
                        placeholder="Descripción de la categoría..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('description') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Color <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input 
                            wire:model.live="color" 
                            type="color" 
                            class="h-10 w-20 rounded border-gray-300"
                        >
                        <input 
                            wire:model="color" 
                            type="text" 
                            placeholder="#6B7280"
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>
                    @error('color') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Icono -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Icono (Seleccionar)
                    </label>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 border rounded-md p-2 max-h-48 overflow-y-auto">
                        @foreach($this->icons as $key => $svg)
                            <button type="button" 
                                wire:click="$set('icon', '{{ $key }}')"
                                class="flex flex-col items-center justify-center p-2 rounded border transition-all duration-200
                                       {{ $icon === $key 
                                          ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200 text-blue-600' 
                                          : 'border-gray-100 hover:bg-gray-50 text-gray-500 hover:text-gray-700' }}"
                                title="{{ $key }}">
                                <svg class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    {!! $svg !!}
                                </svg>
                                <span class="text-[10px] truncate w-full text-center">{{ $key }}</span>
                            </button>
                        @endforeach
                    </div>
                    @error('icon') 
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Orden -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Orden
                    </label>
                    <input 
                        wire:model="order" 
                        type="number" 
                        min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('order') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label class="flex items-center mt-6">
                        <input 
                            wire:model="isActive" 
                            type="checkbox" 
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm font-medium text-gray-700">Categoría activa</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('gestor.categories.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-[#1E1E2D] text-white rounded-md hover:bg-[#2d2d3f] disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>
                        {{ $category ? 'Actualizar' : 'Crear' }} Categoría
                    </span>
                    <span wire:loading>
                        Guardando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
