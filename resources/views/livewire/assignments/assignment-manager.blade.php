<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Asignación de Checklists</h1>
            <p class="text-gray-600 mt-1">Asigna checklists a técnicos en sedes específicas</p>
        </div>
        <a href="{{ route('gestor.checklists.index') }}" 
           class="flex items-center gap-2 text-ep-primary hover:text-ep-primary-dark font-medium transition-colors bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 hover:shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Gestor de Plantillas
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filtros y Acciones -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                <select wire:model.live="selectedEmpresa" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Todas las empresas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
            </div>

            @if($selectedEmpresa)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                    <select wire:model.live="selectedSede" class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Todas las sedes</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select wire:model.live="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="all">Todos</option>
                    <option value="pending">Pendientes</option>
                    <option value="in_progress">En Progreso</option>
                    <option value="completed">Completados</option>
                    <option value="reassigned">Reubicados</option>
                    <option value="overdue">Vencidos</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="toggleForm" 
                        class="w-full bg-ep-dark hover:bg-opacity-90 text-white px-4 py-2 rounded-md">
                    {{ $showForm ? 'Cancelar' : '+ Nueva Asignación' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario de Nueva Asignación -->
    @if($showForm)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border-2 border-ep-primary">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Nueva Asignación de Checklist</h3>
                    <p class="text-sm text-gray-600 mt-1">Completa los siguientes campos para asignar un checklist a un técnico</p>
                </div>
                <button type="button" wire:click="toggleForm" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="save">
                <!-- Paso 1: Selección de Empresa y Sede -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm mr-2">1</span>
                        Selecciona la Empresa y Sede
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Empresa <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="formEmpresaId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary">
                                <option value="">Seleccionar empresa...</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                            @error('formEmpresaId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Sede <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="formSedeId" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                    {{ !$formEmpresaId ? 'disabled' : '' }}>
                                <option value="">{{ $formEmpresaId ? 'Seleccionar sede...' : 'Primero selecciona una empresa' }}</option>
                                @if($formEmpresaId)
                                    @foreach($empresas->find($formEmpresaId)?->sedes ?? [] as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('formSedeId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Selección de Checklist y Técnico -->
                <div class="mb-6 p-4 {{ $formSedeId ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} rounded-lg border">
                    <h4 class="font-semibold {{ $formSedeId ? 'text-green-900' : 'text-gray-500' }} mb-3 flex items-center">
                        <span class="{{ $formSedeId ? 'bg-green-600' : 'bg-gray-400' }} text-white rounded-full w-6 h-6 flex items-center justify-center text-sm mr-2">2</span>
                        Selecciona el Checklist y Técnico
                    </h4>
                    
                    <!-- Filtros de Categoría y Subcategoría -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 pb-4 border-b border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Categoría (Filtro opcional)
                            </label>
                            <select wire:model.live="formCategoryId" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                    {{ !$formSedeId ? 'disabled' : '' }}>
                                <option value="">Todas las categorías</option>
                                @if($formSedeId)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Subcategoría (Filtro opcional)
                            </label>
                            <select wire:model.live="formSubcategoryId" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                    {{ !$formCategoryId ? 'disabled' : '' }}>
                                <option value="">{{ $formCategoryId ? 'Todas las subcategorías' : 'Primero selecciona una categoría' }}</option>
                                @if($formCategoryId)
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Selección de Checklist y Técnico -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Checklist <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="formChecklistId" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                    {{ !$formSedeId ? 'disabled' : '' }}>
                                <option value="">{{ $formSedeId ? 'Seleccionar checklist...' : 'Primero selecciona una sede' }}</option>
                                @if($formSedeId)
                                    @if(empty($checklists))
                                        <option value="" disabled>No hay checklists disponibles con los filtros seleccionados</option>
                                    @else
                                        @foreach($checklists as $checklist)
                                            <option value="{{ $checklist->id }}">
                                                {{ $checklist->title }}
                                                @if($checklist->registry_code)
                                                    ({{ $checklist->registry_code }})
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            @error('formChecklistId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            @if($formSedeId && !empty($checklists))
                                <p class="text-xs text-gray-500 mt-1">{{ count($checklists) }} checklist(s) disponible(s)</p>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Técnico <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="formUserId" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                    {{ !$formSedeId ? 'disabled' : '' }}>
                                <option value="">{{ $formSedeId ? 'Seleccionar técnico...' : 'Primero selecciona una sede' }}</option>
                                @if($formSedeId)
                                    @foreach($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->id }}">
                                            {{ $tecnico->apellidos }}, {{ $tecnico->nombres }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('formUserId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Configuración de Frecuencia y Fecha -->
                <div class="mb-6 p-4 {{ $formChecklistId && $formUserId ? 'bg-purple-50 border-purple-200' : 'bg-gray-50 border-gray-200' }} rounded-lg border">
                    <h4 class="font-semibold {{ $formChecklistId && $formUserId ? 'text-purple-900' : 'text-gray-500' }} mb-3 flex items-center">
                        <span class="{{ $formChecklistId && $formUserId ? 'bg-purple-600' : 'bg-gray-400' }} text-white rounded-full w-6 h-6 flex items-center justify-center text-sm mr-2">3</span>
                        Configura la Frecuencia y Fecha
                    </h4>
                    
                    @if(!$formChecklistId || !$formUserId)
                        <div class="text-sm text-gray-500 italic mb-4">
                            Completa el Paso 2 para configurar la frecuencia y fecha
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Frecuencia <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="formFrequency" 
                                    class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed"
                                    disabled>
                                <option value="once">Única vez</option>
                            </select>
                            <p class="text-xs text-blue-600 mt-1 flex items-start gap-1">
                                <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>Próximamente: frecuencias recurrentes vinculadas a matriz de riesgos</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Fecha de Vencimiento
                            </label>
                            <input type="date" wire:model="formDueDate" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary">
                            @error('formDueDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas Adicionales</label>
                        <textarea wire:model="formNotes" rows="2" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                  placeholder="Agrega cualquier nota o instrucción especial..."></textarea>
                        @error('formNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" wire:click="toggleForm"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg wire:loading class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Crear Asignación</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tabla de Asignaciones -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Checklist</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frecuencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assignments as $assignment)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $assignment->checklist->title }}</div>
                            @if($assignment->checklist->registry_code)
                                <div class="text-sm text-gray-500">{{ $assignment->checklist->registry_code }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $assignment->user->apellidos }}, {{ $assignment->user->nombres }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $assignment->sede->nombre_sede }}</div>
                            <div class="text-xs text-gray-500">{{ $assignment->empresa->nombre }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $assignment->getFrequencyLabel() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $color = $assignment->getStatusBadgeColor();
                                $bgColor = "bg-{$color}-100";
                                $textColor = "text-{$color}-800";
                                $statusLabel = match($assignment->status) {
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En Progreso',
                                    'completed' => 'Completado',
                                    'reassigned' => 'Reubicado',
                                    default => ucfirst($assignment->status)
                                };
                            @endphp
                            <span class="px-2 py-1 {{ $bgColor }} {{ $textColor }} rounded-full text-xs font-medium">
                                @if($assignment->isOverdue())
                                    Vencido
                                @else
                                    {{ $statusLabel }}
                                @endif
                            </span>
                            
                            @if($assignment->visitCompletion && $assignment->visitCompletion->visit)
                                <div class="mt-1">
                                    <span class="text-[10px] text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">
                                        Visita: {{ $assignment->visitCompletion->visit->status_label }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button wire:click="delete({{ $assignment->id }})"
                                    wire:confirm="¿Estás seguro de eliminar esta asignación?"
                                    class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No hay asignaciones. Crea una nueva asignación para comenzar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($assignments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
