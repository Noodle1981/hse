<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Gestión de Técnicos
        </h2>
    </div>

    <!-- Search -->
    <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
        <div class="relative">
            <input wire:model.live.debounce.300ms="search" 
                   type="text" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-ep-primary focus:border-ep-primary"
                   placeholder="Buscar técnico por nombre, documento o email...">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Técnico
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contacto
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Matrícula / Doc
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Asignaciones
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($technicians as $tech)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-ep-primary/10 flex items-center justify-center text-ep-primary font-bold">
                                            {{ substr($tech->nombres, 0, 1) }}{{ substr($tech->apellidos, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $tech->nombres }} {{ $tech->apellidos }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $tech->titulo ?? 'Técnico' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $tech->email }}</div>
                                <div class="text-sm text-gray-500">{{ $tech->telefono }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ $tech->matricula ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $tech->n_doc }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tech->sedes()->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $tech->sedes()->count() }} Sedes
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="openAssignModal({{ $tech->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-md transition-colors hover:bg-indigo-100"
                                        title="Asignar a Empresa/Sede">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 text-xs font-bold">Asignar</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                No se encontraron técnicos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $technicians->links() }}
        </div>
    </div>

    <!-- Modal de Asignación -->
    @if($showAssignModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeAssignModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4" id="modal-title">
                                Asignaciones para <span class="text-ep-primary">{{ $selectedTechnician->nombres }} {{ $selectedTechnician->apellidos }}</span>
                            </h3>
                            
                            <!-- Messages -->
                            @if (session()->has('message'))
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                    <p>{{ session('message') }}</p>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                    <p>{{ session('error') }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Columna Izquierda: Formulario de Asignación -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Nueva Asignación</h4>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                                            <select wire:model.live="selectedCompanyId" id="company" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-ep-primary focus:border-ep-primary sm:text-sm">
                                                <option value="">Seleccione una Empresa...</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}">{{ $company->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('selectedCompanyId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label for="sede" class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                                            <select wire:model.live="selectedSedeId" id="sede" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-ep-primary focus:border-ep-primary sm:text-sm" {{ empty($sedes) ? 'disabled' : '' }}>
                                                <option value="">Seleccione una Sede...</option>
                                                @foreach($sedes as $sede)
                                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }} ({{ $sede->localidad ?? '-' }})</option>
                                                @endforeach
                                            </select>
                                            @error('selectedSedeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="pt-2">
                                            <button wire:click="assignSede" 
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#292938] text-base font-medium text-white hover:bg-[#1e1e2d] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#292938] sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                                    {{ empty($selectedSedeId) ? 'disabled' : '' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                                </svg>
                                                Asignar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Columna Derecha: Lista de Asignaciones -->
                                <div>
                                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Asignaciones Actuales</h4>
                                    
                                    <div class="bg-white border rounded-lg overflow-y-auto max-h-[300px]">
                                        @if($currentAssignments && $currentAssignments->count() > 0)
                                            <ul class="divide-y divide-gray-200">
                                                @foreach($currentAssignments as $assignment)
                                                    <li class="p-3 flex justify-between items-center hover:bg-gray-50">
                                                        <div>
                                                            <div class="text-sm font-bold text-gray-900">{{ $assignment->empresa->nombre }}</div>
                                                            <div class="text-xs text-gray-500">{{ $assignment->nombre_sede }} ({{ $assignment->localidad ?? 'N/A' }})</div>
                                                        </div>
                                                        <button wire:click="removeAssignment({{ $assignment->id }})" 
                                                                class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition-colors"
                                                                title="Revocar Asignación">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 000-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="p-4 text-center text-sm text-gray-500">
                                                Este técnico no tiene asignaciones activas.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="closeAssignModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
