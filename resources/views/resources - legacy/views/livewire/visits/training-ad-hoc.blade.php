<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="close"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Registrar Capacitación
                        </h3>
                        
                        <div class="mt-4 space-y-4">
                            <!-- Template Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Plantilla (Opcional)</label>
                                <select wire:model.live="selectedTemplateId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-ep-primary focus:border-ep-primary sm:text-sm rounded-md">
                                    <option value="">-- Capacitación Ad-hoc --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tema / Título</label>
                                <input type="text" wire:model="title" class="mt-1 shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md" {{ $selectedTemplateId ? 'readonly' : '' }}>
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Descripción / Puntos Tratados</label>
                                <textarea wire:model="description" rows="3" class="mt-1 shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md" {{ $selectedTemplateId ? 'readonly' : '' }}></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Duración (minutos)</label>
                                <input type="number" wire:model="duration_minutes" class="mt-1 shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('duration_minutes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Attendees -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Asistentes (Uno por línea)</label>
                                <textarea wire:model="attendeesText" rows="4" class="mt-1 shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Juan Perez&#10;Maria Gonzalez&#10;..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">Ingrese los nombres de los participantes.</p>
                                @error('attendeesText') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                             <!-- Photo Evidence (Placeholder for now) -->
                            <!--
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Foto Evidencia (Opcional)</label>
                                <input type="file" wire:model="photo_evidence" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                            </div>
                            -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" wire:click="save" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-ep-primary text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ep-primary sm:ml-3 sm:w-auto sm:text-sm">
                    Registrar Capacitación
                </button>
                <button type="button" wire:click="close" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
            
        </div>
    </div>
</div>
