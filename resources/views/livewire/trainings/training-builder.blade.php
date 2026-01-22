<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('trainings.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-ep-primary transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $trainingId ? 'Editar' : 'Nueva' }} Capacitación</h1>
        </div>

        <form wire:submit="save" class="space-y-8">
            <!-- Información Básica -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Título de la Capacitación</label>
                        <input type="text" wire:model="title" 
                               class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all"
                               placeholder="Ej: Seguridad en Alturas, Prevención de Incendios...">
                        @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Empresa / Cliente</label>
                        <select wire:model.live="empresa_id" 
                                class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                            <option value="">Seleccione una empresa</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        @error('empresa_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sede / Ubicación</label>
                        <select wire:model.live="sede_id" 
                                class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all {{ !$empresa_id ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ !$empresa_id ? 'disabled' : '' }}>
                            <option value="">Seleccione una sede</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                            @endforeach
                        </select>
                        @error('sede_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Modalidad</label>
                        <select wire:model="type" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual / Online</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fecha y Hora</label>
                        <input type="datetime-local" wire:model="date" 
                               class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                        @error('date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Duración (minutos)</label>
                        <input type="number" wire:model="duration" 
                               class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all"
                               placeholder="60">
                        @error('duration') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Técnico Responsable</label>
                        <select wire:model="user_id" 
                                class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all {{ !$sede_id ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ !$sede_id ? 'disabled' : '' }}>
                            <option value="">Seleccione un responsable</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}">{{ $tecnico->apellidos }}, {{ $tecnico->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1 italic">El técnico que supervisa o lleva a cabo la capacitación.</p>
                        @error('user_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Instructor / Responsable Externo</label>
                        <input type="text" wire:model="trainer_name" 
                               class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all"
                               placeholder="Nombre del capacitador (opcional)">
                        @error('trainer_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Estado</label>
                        <select wire:model="status" class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                            <option value="scheduled">Programada</option>
                            <option value="completed">Finalizada</option>
                            <option value="cancelled">Cancelada</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Descripción / Objetivos</label>
                        <textarea wire:model="description" rows="4" 
                                  class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all"
                                  placeholder="Contenido a tratar y objetivos de la capacitación..."></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('trainings.index') }}" 
                   class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-ep-primary text-white rounded-xl shadow-lg shadow-ep-primary/30 hover:bg-ep-primary-dark transition-all font-bold text-sm">
                    {{ $trainingId ? 'Actualizar' : 'Guardar' }} Capacitación
                </button>
            </div>
        </form>
    </div>
</div>
