<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-ep-dark">Plantillas de Checklists</h1>
        <div class="flex gap-3">
            <a href="{{ route('gestor.assignments.index') }}" 
               class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold flex items-center">
                ← Volver
            </a>
            <a href="{{ route('gestor.checklists.create') }}" 
               class="px-6 py-3 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark transition-colors font-semibold">
                + Nuevo Checklist
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por título..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary">
            </div>
            <div>
                <select wire:model.live="categoryFilter" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="">Todas las categorías</option>
                    <option value="hys">Higiene y Seguridad</option>
                    <option value="calidad">Calidad</option>
                    <option value="seguimiento">Seguimiento</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de Checklists -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($checklists->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="text-xl">No hay checklists creados</p>
                <p class="text-sm mt-2">Crea tu primer checklist para comenzar</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-ep-dark text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">Título</th>
                        <th class="px-6 py-3 text-left">Categoría</th>
                        <th class="px-6 py-3 text-center">Preguntas</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($checklists as $checklist)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-ep-dark">{{ $checklist->title }}</div>
                                @if($checklist->description)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ $checklist->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($checklist->category)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($checklist->category === 'hys') bg-red-100 text-red-800
                                        @elseif($checklist->category === 'calidad') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($checklist->category) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-gray-100 rounded-full text-sm font-medium">
                                    {{ $checklist->questions_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($checklist->is_draft)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        ⚠️ Borrador
                                    </span>
                                @else
                                    <button wire:click="toggleActive({{ $checklist->id }})"
                                            class="px-3 py-1 rounded-full text-xs font-medium transition-colors
                                                {{ $checklist->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $checklist->is_active ? '✅ Activo' : 'Inactivo' }}
                                    </button>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2 flex-wrap">
                                        <!-- Test PDF Button -->
                                        <a href="{{ route('gestor.inspections.index') }}" 
                                           class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm flex items-center gap-1">
                                            <span>📄</span> Probar PDF
                                        </a>

                                        @if(!$checklist->is_draft)
                                            <!-- Note: Filling usually happens for technicians, but if admins want to test, we need a route -->
                                            <!-- There is no gestor.checklists.fill route in the provided web.php dump -->
                                            <!-- Keeping as is might break if route doesn't exist, but removing might be safer or pointing to technician route if shared? -->
                                            <!-- Actually, technician route is tecnico.checklists.fill -->
                                            <!-- Let's comment it out or point to edit if fill is not for gestor -->
                                            {{-- <a href="{{ route('checklists.fill', $checklist) }}" class="...">Llenar</a> --}} 
                                        @endif
                                        <a href="{{ route('gestor.checklists.edit', $checklist) }}"
                                           class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                                            Editar
                                        </a>
                                        <button wire:click="duplicate({{ $checklist->id }})"
                                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                            📋 Duplicar
                                        </button>
                                        <button wire:click="delete({{ $checklist->id }})"
                                                wire:confirm="¿Estás seguro de eliminar este checklist?"
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                            Eliminar
                                        </button>
                                    </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $checklists->links() }}
            </div>
        @endif
    </div>
</div>
