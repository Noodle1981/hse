<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="close"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                         <svg class="h-6 w-6 text-ep-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Entrega de EPP
                        </h3>
                        
                        <div class="mt-4 space-y-4">
                            <!-- Employee Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Empleado Destinatario</label>
                                <select wire:model="employeeId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-ep-primary focus:border-ep-primary sm:text-sm rounded-md">
                                    <option value="">-- Seleccionar Empleado --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->name }} ({{ $employee->dni }})</option>
                                    @endforeach
                                </select>
                                @error('employeeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Add Item Form -->
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-800 mb-2">Agregar Ítem</h4>
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-12 sm:col-span-5">
                                        <select wire:model="newItemId" class="block w-full text-xs border-gray-300 rounded-md">
                                            <option value="">-- EPP --</option>
                                            @foreach($eppItemsCatalog as $item)
                                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-6 sm:col-span-2">
                                        <input type="number" wire:model="newItemQty" placeholder="Cant." class="block w-full text-xs border-gray-300 rounded-md">
                                    </div>
                                    <div class="col-span-6 sm:col-span-2">
                                        <input type="text" wire:model="newItemSize" placeholder="Talla" class="block w-full text-xs border-gray-300 rounded-md">
                                    </div>
                                    <div class="col-span-12 sm:col-span-3 flex items-center">
                                        <button wire:click="addItem" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs py-2 px-2 rounded">
                                            + Agregar
                                        </button>
                                    </div>
                                </div>
                                @error('newItemId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Items List -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ítems a Entregar</label>
                                @if(count($selectedItems) > 0)
                                    <div class="border rounded-md overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($selectedItems as $index => $item)
                                                    <tr>
                                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $item['name'] }}</td>
                                                        <td class="px-3 py-2 text-sm text-gray-500">{{ $item['qty'] }} u.</td>
                                                        <td class="px-3 py-2 text-sm text-gray-500">{{ $item['size'] ?? '-' }}</td>
                                                        <td class="px-3 py-2 text-right">
                                                            <button wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700 text-xs">
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 italic text-center p-4 border border-dashed rounded">Ningún ítem seleccionado</p>
                                @endif
                                @error('selectedItems') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                                <textarea wire:model="notes" rows="2" class="mt-1 shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" wire:click="save" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-ep-primary text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ep-primary sm:ml-3 sm:w-auto sm:text-sm">
                    Confirmar Entrega
                </button>
                <button type="button" wire:click="close" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
            
        </div>
    </div>
</div>
