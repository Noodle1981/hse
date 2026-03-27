<div class="px-4 sm:px-6 lg:px-8 pt-8 w-full max-w-9xl mx-auto">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="text-lg md:text-xl font-medium text-white tracking-tight">
                        Gestión de <span class="text-ep-primary italic">Empresas</span>
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('gestor.companies.create') }}" 
                       class="hidden xs:flex bg-ep-primary text-white hover:bg-ep-primary-dark px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva Empresa
                    </a>

                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                        <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                            {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                        </div>
                        <div class="h-4 w-px bg-white/10 mx-1"></div>
                        <span class="text-[10px] font-bold text-indigo-100">{{ auth()->user()->nombres }}</span>
                    </a>
                </div>
            </div>
        </header>
    @endsection
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div></div> {{-- Spacer for layout alignment if needed --}}
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center w-full md:w-auto">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model.live="showDeleted" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-red-600"></div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300 whitespace-nowrap">Ver Eliminados</span>
            </label>
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

    <div class="bg-white rounded-lg shadow overflow-x-auto">
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
                            <span class="font-medium text-gray-900 {{ $company->trashed() ? 'line-through text-red-500' : '' }}">
                                {{ $company->nombre }}
                                @if($company->trashed()) <span class="text-xs text-red-500 ml-1">(Baja)</span> @endif
                            </span>
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
                                @if($company->trashed())
                                    <button wire:click="restore({{ $company->id }})"
                                            class="text-green-600 hover:text-green-900 transition-colors duration-150 p-1 hover:bg-green-50 rounded"
                                            title="Restaurar Empresa">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ route('gestor.companies.locations', $company) }}" 
                                    class="text-blue-600 hover:text-blue-900 text-sm font-medium pr-3 border-r border-gray-200"
                                    title="Gestionar Sedes">
                                        Sedes
                                    </a>
                                    <a href="{{ route('gestor.companies.edit', $company) }}" 
                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150 p-1 hover:bg-indigo-50 rounded"
                                    title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </a>
                                    <button wire:click="confirmDelete({{ $company->id }}, '{{ $company->nombre }}')" 
                                            class="text-red-600 hover:text-red-900 transition-colors duration-150 p-1 hover:bg-red-50 rounded"
                                            title="Dar de baja">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endif
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
                        ¿Dar de baja a la empresa?
                    </h3>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Estás a punto de dar de baja a <span class="font-bold text-gray-700">{{ $companyToDeleteName }}</span>.
                        <br><br>
                        ⚠️ <span class="font-semibold">Nota Importante:</span> La empresa NO se eliminará permanentemente. Se conservará el historial de sus sedes y datos, pero dejará de estar activa en los listados principales.
                    </p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="cancelDelete" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors font-medium">
                            Salir
                        </button>
                        <button wire:click="delete" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors font-bold shadow-sm">
                            Dar de Baja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
