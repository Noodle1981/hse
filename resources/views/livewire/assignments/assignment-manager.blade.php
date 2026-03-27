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
                        Asignación de <span class="text-ep-primary italic">Checklists</span>
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <button wire:click="toggleForm" 
                            class="hidden xs:flex bg-white/10 backdrop-blur-md text-indigo-100 hover:bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all items-center gap-1">
                        {{ $showForm ? 'Cancelar' : 'Nueva Asignación' }}
                    </button>
                    
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

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                <select wire:model.live="selectedEmpresa" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">Todas las empresas</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                <select wire:model.live="selectedSede" class="w-full rounded-md border-gray-300 shadow-sm" {{ !$selectedEmpresa ? 'disabled' : '' }}>
                    <option value="">{{ $selectedEmpresa ? 'Todas las sedes' : 'Primero selecciona una empresa' }}</option>
                    @if($selectedEmpresa)
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    <!-- Formulario de Nueva/Editar Asignación -->
    @if($showForm)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border-2 border-ep-primary">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    {{ $editingId ? 'Editar Asignación de Checklist' : 'Nueva Asignación de Checklist' }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $editingId ? 'Modifica el técnico, fecha o notas de la asignación' : 'Completa los campos para asignar un checklist a un técnico' }}
                </p>
            </div>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Empresa -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Empresa <span class="text-red-500">*</span>
                        </label>
                        @if($editingId)
                            <input type="text" value="{{ $empresas->find($formEmpresaId)?->nombre }}" disabled class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        @else
                            <select wire:model.live="formEmpresaId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary">
                                <option value="">Seleccionar empresa...</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                            @error('formEmpresaId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <!-- Sede -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sede <span class="text-red-500">*</span>
                        </label>
                        @if($editingId)
                            <input type="text" value="{{ $empresas->find($formEmpresaId)?->sedes->find($formSedeId)?->nombre_sede }}" disabled class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        @else
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
                        @endif
                    </div>

                    <!-- Categoría -->
                    @if(!$editingId)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Categoría <span class="text-gray-400 text-xs">(Filtro opcional)</span>
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

                        <!-- Subcategoría -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Subcategoría <span class="text-gray-400 text-xs">(Filtro opcional)</span>
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
                    @endif

                    <!-- Checklist -->
                    <div {{ $editingId ? 'class=md:col-span-2' : '' }}>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Checklist <span class="text-red-500">*</span>
                        </label>
                        @if($editingId)
                            @php
                                $checklist = \App\Models\DynamicChecklist::find($formChecklistId);
                            @endphp
                            <input type="text" value="{{ $checklist?->title }}{{ $checklist?->registry_code ? ' (' . $checklist->registry_code . ')' : '' }}" disabled class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        @else
                            <select wire:model="formChecklistId" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                    {{ !$formSedeId ? 'disabled' : '' }}>
                                <option value="">{{ $formSedeId ? 'Seleccionar checklist...' : 'Primero selecciona una sede' }}</option>
                                @if($formSedeId)
                                    @if(empty($checklists))
                                        <option value="" disabled>No hay checklists disponibles</option>
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
                        @endif
                    </div>

                    <!-- Técnico -->
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

                    <!-- Fecha de Vencimiento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Vencimiento <span class="text-gray-400 text-xs">(Opcional)</span>
                        </label>
                        <input type="date" wire:model="formDueDate" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary">
                        @error('formDueDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notas -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Notas Adicionales <span class="text-gray-400 text-xs">(Opcional)</span>
                        </label>
                        <textarea wire:model="formNotes" rows="2" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary"
                                  placeholder="Agrega cualquier nota o instrucción especial..."></textarea>
                        @error('formNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
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
                        <span wire:loading.remove>{{ $editingId ? 'Guardar Cambios' : 'Crear Asignación' }}</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tabla de Asignaciones -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
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
                            <div class="flex items-center gap-3">
                                @if($assignment->canBeEdited())
                                    <button wire:click="edit({{ $assignment->id }})"
                                            class="text-blue-600 hover:text-blue-900 font-medium p-1"
                                            title="Editar asignación">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                @endif

                                @if($assignment->status === 'completed')
                                    @if($assignment->inspection)
                                        <a href="{{ route('gestor.inspections.pdf.preview', $assignment->inspection->id) }}" 
                                           target="_blank"
                                           class="text-ep-primary hover:text-ep-primary-dark p-1" 
                                           title="Ver Inspección PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('gestor.inspections.pdf.download', $assignment->inspection->id) }}" 
                                           class="text-green-600 hover:text-green-800 p-1" 
                                           title="Descargar Inspección PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    @elseif($assignment->visitCompletion)
                                        <a href="{{ route('gestor.visits.checklist.preview', $assignment->visitCompletion->id) }}" 
                                           target="_blank"
                                           class="text-ep-primary hover:text-ep-primary-dark p-1" 
                                           title="Ver Checklist PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('gestor.visits.checklist.download', $assignment->visitCompletion->id) }}" 
                                           class="text-green-600 hover:text-green-800 p-1" 
                                           title="Descargar Checklist PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    @endif
                                @endif

                                @if($assignment->canBeDeleted())
                                    <button wire:click="delete({{ $assignment->id }})"
                                            wire:confirm="¿Estás seguro de eliminar esta asignación?"
                                            class="text-red-600 hover:text-red-900 font-medium p-1"
                                            title="Eliminar asignación">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            </div>
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
