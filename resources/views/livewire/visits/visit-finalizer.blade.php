<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Finalizar Visita
                        </h3>
                        <div class="mt-2 text-center">
                            <p class="text-sm text-gray-500 mb-4">
                                Por favor confirme que ha completado todas las actividades.
                            </p>

                            <div class="bg-gray-50 rounded-lg p-3 grid grid-cols-3 gap-2 mb-4 border border-gray-100">
                                <div class="text-center">
                                    <span class="block text-xl font-bold text-gray-800">{{ $checklistsCount }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">Checklists</span>
                                </div>
                                <div class="text-center border-l border-gray-200">
                                    <span class="block text-xl font-bold text-blue-600">{{ $trainingsCount }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">Capacit.</span>
                                </div>
                                <div class="text-center border-l border-gray-200">
                                    <span class="block text-xl font-bold text-orange-600">{{ $eppCount }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">Entregas</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label for="observations" class="block text-sm font-medium text-gray-700">Observaciones Generales</label>
                                <textarea 
                                    id="observations" 
                                    wire:model="generalObservations"
                                    rows="3" 
                                    class="mt-1 shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Resumen de la visita, incidencias, notas..."
                                ></textarea>
                                @error('generalObservations') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="signature" class="block text-sm font-medium text-gray-700">Firma del Responsable</label>
                                <input 
                                    type="text" 
                                    id="signature" 
                                    wire:model="signature"
                                    class="mt-1 shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Escriba su nombre completo para firmar"
                                >
                                <p class="text-xs text-gray-500 mt-1">Al escribir su nombre acepta la validez de los datos registrados.</p>
                                @error('signature') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button 
                    type="button" 
                    wire:click="finalize"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Finalizar y Cerrar
                </button>
                <button 
                    type="button" 
                    wire:click="$dispatch('closeFinalizeModal')"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
