<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Personal</h1>
                <p class="mt-1 text-sm text-gray-500 text-balance">Administre los empleados de cada sede para el registro de EPP.</p>
            </div>
            <button wire:click="create" 
                    class="inline-flex items-center px-6 py-3 bg-ep-primary text-white font-bold rounded-xl shadow-lg shadow-ep-primary/30 hover:bg-ep-primary-dark transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Empleado
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" 
                           class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm transition-all" 
                           placeholder="Buscar por nombre o DNI...">
                </div>

                <select wire:model.live="empresaFilter" 
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all">
                    <option value="">Todas las Empresas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>

                <select wire:model.live="sedeFilter" 
                        {{ !$empresaFilter ? 'disabled' : '' }}
                        class="block w-full border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm p-3 transition-all {{ !$empresaFilter ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <option value="">Todas las Sedes</option>
                    @foreach($sedesFilter as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabla de Empleados -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Documento / CUIL</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Puesto</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Empresa / Sede</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold mr-3">
                                        {{ substr($emp->name, 0, 1) }}{{ substr($emp->last_name, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">{{ $emp->full_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div>DNI: {{ $emp->dni ?: 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $emp->cuil }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                {{ $emp->puesto ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-bold text-ep-primary">{{ $emp->empresa->nombre }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $emp->sede->nombre_sede }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <button wire:click="edit({{ $emp->id }})" class="text-ep-primary hover:text-ep-primary-dark transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No se encontraron empleados cargados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $employees->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Formulario -->
    @if($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showForm', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <form wire:submit="save">
                        <div class="bg-white px-8 pt-8 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">{{ $employeeId ? 'Editar' : 'Nuevo' }} Empleado</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nombre</label>
                                    <input type="text" wire:model="name" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Apellido</label>
                                    <input type="text" wire:model="last_name" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                    @error('last_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">DNI</label>
                                    <input type="text" wire:model="dni" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                    @error('dni') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">CUIL</label>
                                    <input type="text" wire:model="cuil" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                    @error('cuil') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Puesto / Cargo</label>
                                    <input type="text" wire:model="puesto" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                    @error('puesto') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Empresa</label>
                                    <select wire:model.live="empresa_id" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                        <option value="">Seleccione...</option>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('empresa_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Sede</label>
                                    <select wire:model="sede_id" {{ !$empresa_id ? 'disabled' : '' }} class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all {{ !$empresa_id ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        <option value="">Seleccione...</option>
                                        @foreach($formSedes as $sede)
                                            <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                        @endforeach
                                    </select>
                                    @error('sede_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-8 py-6 flex flex-row-reverse gap-3">
                            <button type="submit" class="px-6 py-2.5 bg-ep-primary text-white font-bold rounded-xl shadow-lg shadow-ep-primary/20 hover:bg-ep-primary-dark transition-all text-sm">
                                {{ $employeeId ? 'Actualizar' : 'Guardar' }} Empleado
                            </button>
                            <button type="button" wire:click="$set('showForm', false)" class="px-6 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-all text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
