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
                        Plantillas de <span class="text-ep-primary italic">Checklists</span>
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('gestor.checklists.create') }}" 
                       class="hidden xs:flex bg-ep-primary text-white hover:bg-ep-primary-dark px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nuevo Checklist
                    </a>

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
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

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
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="statusFilter" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                    <option value="all">Todos los estados</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de Checklists -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
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
                        <th class="px-6 py-3 text-left">Subcategoría</th>
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
                                @if($checklist->registry_code)
                                    <div class="text-xs text-gray-400 mt-1">Código: {{ $checklist->registry_code }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($checklist->categoryRelation)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $checklist->categoryRelation->color }}20; color: {{ $checklist->categoryRelation->color }}">
                                        {{ $checklist->categoryRelation->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($checklist->subcategoryRelation)
                                    <span class="text-sm text-gray-700">{{ $checklist->subcategoryRelation->name }}</span>
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
                                        {{ $checklist->is_active ? '✅ Activo' : '❌ Inactivo' }}
                                    </button>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2 flex-wrap">
                                    <a href="{{ route('gestor.checklists.edit', $checklist) }}"
                                       class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
                                       title="Editar">
                                        ✏️ Editar
                                    </a>
                                    <a href="{{ route('gestor.checklists.emulator', $checklist) }}"
                                       class="px-3 py-1 bg-teal-600 text-white rounded hover:bg-teal-700 text-sm"
                                       title="Emular">
                                        📱 Emular
                                    </a>
                                    <button wire:click="duplicate({{ $checklist->id }})"
                                            class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm"
                                            title="Duplicar">
                                        📋 Duplicar
                                    </button>
                                    <button wire:click="toggleActive({{ $checklist->id }})"
                                            wire:confirm="¿Estás seguro de {{ $checklist->is_active ? 'desactivar' : 'activar' }} este checklist?"
                                            class="px-3 py-1 {{ $checklist->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded text-sm"
                                            title="{{ $checklist->is_active ? 'Desactivar' : 'Activar' }}">
                                        {{ $checklist->is_active ? '🔒 Desactivar' : '✅ Activar' }}
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
