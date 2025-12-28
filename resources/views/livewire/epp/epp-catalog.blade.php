<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Catálogo de EPP</h1>
                <p class="mt-1 text-sm text-gray-500 text-balance">Gestione los elementos de protección personal disponibles para entrega.</p>
            </div>
            <button wire:click="create" 
                    class="inline-flex items-center px-6 py-3 bg-ep-primary text-white font-bold rounded-xl shadow-lg shadow-ep-primary/30 hover:bg-ep-primary-dark transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo EPP
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
            <div class="relative max-w-lg">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" wire:model.live="search" 
                       class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary sm:text-sm transition-all" 
                       placeholder="Buscar por nombre o tipo...">
            </div>
        </div>

        <!-- Tabla de Elementos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Elemento</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Vda. Útil</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Normatividad</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-ep-primary/10 flex items-center justify-center text-ep-primary mr-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $item->nombre }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($item->descripcion, 50) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                    {{ $item->tipo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ $item->vida_util_meses ? $item->vida_util_meses . ' meses' : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->codigo_norma ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <button wire:click="edit({{ $item->id }})" class="text-ep-primary hover:text-ep-primary-dark transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button wire:confirm="¿Está seguro de eliminar este elemento?" wire:click="delete({{ $item->id }})" class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8"/></svg>
                                    <p class="text-gray-500 font-medium">No se encontraron elementos en el catálogo.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $items->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Formulario -->
    @if($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showForm', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="save">
                        <div class="bg-white px-8 pt-8 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">{{ $itemId ? 'Editar' : 'Nuevo' }} Elemento de Protección</h3>
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Elemento</label>
                                    <input type="text" wire:model="nombre" 
                                           class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                    @error('nombre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipo / Categoría</label>
                                        <select wire:model="tipo" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                                            <option value="">Seleccione...</option>
                                            <option value="Cabeza">Cabeza</option>
                                            <option value="Auditiva">Auditiva</option>
                                            <option value="Ocular">Ocular</option>
                                            <option value="Respiratoria">Respiratoria</option>
                                            <option value="Manos">Manos</option>
                                            <option value="Calzado">Calzado</option>
                                            <option value="Cuerpo">Cuerpo</option>
                                            <option value="Caídas">Caídas</option>
                                        </select>
                                        @error('tipo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Vida Útil (Meses)</label>
                                        <input type="number" wire:model="vida_util_meses" 
                                               class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all" 
                                               placeholder="Opcional">
                                        @error('vida_util_meses') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Normativa / Código</label>
                                    <input type="text" wire:model="codigo_norma" 
                                           class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all"
                                           placeholder="Ej: IRAM 3610">
                                    @error('codigo_norma') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Descripción</label>
                                    <textarea wire:model="description" rows="3" 
                                              class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all"></textarea>
                                    @error('descripcion') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-8 py-6 flex flex-row-reverse gap-3">
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-ep-primary text-white font-bold rounded-xl shadow-lg shadow-ep-primary/20 hover:bg-ep-primary-dark transition-all text-sm">
                                Guardar Elemento
                            </button>
                            <button type="button" wire:click="$set('showForm', false)"
                                    class="px-6 py-2.5 border border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-all text-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
