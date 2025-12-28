<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Empresas</h2>
        <a href="{{ route('companies.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            + Nueva Empresa
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
                            <div class="flex gap-2">
                                <a href="{{ route('companies.locations', $company) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    Sedes
                                </a>
                                <a href="{{ route('companies.edit', $company) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    Editar
                                </a>
                                <button wire:click="delete({{ $company->id }})" 
                                        wire:confirm="¿Estás seguro de eliminar esta empresa?"
                                        class="text-red-600 hover:text-red-900">
                                    Eliminar
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
