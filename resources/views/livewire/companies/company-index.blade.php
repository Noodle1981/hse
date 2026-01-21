<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Empresas</h2>
        <a href="{{ route('companies.create') }}" 
           class="hover:bg-opacity-90 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center gap-2 shadow-sm"
           style="background-color: #292938;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nueva Empresa
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
            <input 
                wire:model.live="search" 
                type="text" 
                placeholder="Buscar por nombre, razón social o CUIT..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Razón Social</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CUIT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sedes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($companies as $company)
                    <tr>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $company->nombre }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $company->razon_social }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $company->cuit }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $company->sedes_count }} sedes
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('companies.locations', $company) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium pr-3 border-r border-gray-200"
                                   title="Gestionar Sedes">
                                    Sedes
                                </a>
                                <a href="{{ route('companies.edit', $company) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150 p-1 hover:bg-indigo-50 rounded"
                                   title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                <button wire:click="delete({{ $company->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta empresa?"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-150 p-1 hover:bg-red-50 rounded"
                                        title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron empresas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $companies->links() }}
        </div>
    </div>
</div>
