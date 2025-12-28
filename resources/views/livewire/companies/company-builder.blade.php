<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $empresa ? 'Editar Empresa' : 'Nueva Empresa' }}
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="nombre" 
                        type="text" 
                        placeholder="Nombre comercial"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('nombre') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- CUIT -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        CUIT <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="cuit" 
                        type="text" 
                        placeholder="20-12345678-9"
                        maxlength="13"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('cuit') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Razón Social -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Razón Social <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="razonSocial" 
                        type="text" 
                        placeholder="Razón social completa"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('razonSocial') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('companies.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>
                        {{ $empresa ? 'Actualizar' : 'Crear' }} Empresa
                    </span>
                    <span wire:loading>
                        Guardando...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
