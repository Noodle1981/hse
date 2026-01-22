<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Asignación de Checklists</h1>
        <p class="text-gray-600 mt-1">Asigna checklists a técnicos en sedes específicas</p>
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
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Nueva Asignación</h3>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Checklist <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="formChecklistId" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Seleccionar checklist...</option>
                            @foreach($checklists as $checklist)
                                <option value="{{ $checklist->id }}">
                                    {{ $checklist->title }}
                                    @if($checklist->registry_code)
                                        ({{ $checklist->registry_code }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('formChecklistId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sede <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="formSedeId" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Seleccionar sede...</option>
                            @if($selectedEmpresa)
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            @else
                                @foreach($empresas as $empresa)
                                    <optgroup label="{{ $empresa->nombre }}">
                                        @foreach($empresa->sedes as $sede)
                                            <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endif
                        </select>
                        @error('formSedeId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Técnico <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="formUserId" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Seleccionar técnico...</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id }}">
                                    {{ $tecnico->apellidos }}, {{ $tecnico->nombres }}
                                </option>
                            @endforeach
                        </select>
                        @error('formUserId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Frecuencia <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="formFrequency" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="once">Única vez</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                            <option value="quarterly">Trimestral</option>
                            <option value="yearly">Anual</option>
                        </select>
                        @error('formFrequency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Vencimiento
                        </label>
                        <input type="date" wire:model="formDueDate" 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                        @error('formDueDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea wire:model="formNotes" rows="2" 
                                  class="w-full rounded-md border-gray-300 shadow-sm"
                                  placeholder="Notas adicionales..."></textarea>
                        @error('formNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" wire:click="toggleForm"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-ep-dark text-white rounded-md hover:bg-opacity-90 disabled:opacity-50">
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
