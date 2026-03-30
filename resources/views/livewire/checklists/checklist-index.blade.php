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
                                    <a href="{{ route('gestor.checklists.edit', $checklist->id) }}" 
                                       class="px-3 py-1 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-black transition-all flex items-center gap-1.5 shadow-sm uppercase tracking-widest"
                                       title="Editar">
                                        <svg class="w-3.5 h-3.5 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Editar
                                    </a>
                                    <a href="{{ route('gestor.checklists.emulator', $checklist->id) }}" 
                                       class="px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-100 rounded-lg text-[10px] font-black transition-all flex items-center gap-1.5 shadow-sm uppercase tracking-widest"
                                       title="Emular (Ver como técnico)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                        </svg>
                                        Emular
                                    </a>
                                    <button wire:click="duplicate({{ $checklist->id }})"
                                            class="px-3 py-1 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-lg text-[10px] font-black transition-all flex items-center gap-1.5 shadow-sm uppercase tracking-widest"
                                            title="Duplicar">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                        </svg>
                                        Duplicar
                                    </button>
                                    <button @click="$dispatch('open-confirm-modal', { 
                                                id: {{ $checklist->id }}, 
                                                title: '¿Cambiar Estado?', 
                                                message: '¿Estás seguro de {{ $checklist->is_active ? 'desactivar' : 'activar' }} el checklist: {{ $checklist->title }}?',
                                                action: 'toggleActive',
                                                confirmLabel: '{{ $checklist->is_active ? 'Desactivar' : 'Activar' }}',
                                                confirmColor: '{{ $checklist->is_active ? 'bg-orange-600 shadow-orange-200' : 'bg-green-600 shadow-green-200' }}'
                                            })"
                                            class="px-3 py-1 flex items-center gap-1.5 {{ $checklist->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg text-[10px] font-black transition-all hover:scale-105 shadow-sm uppercase tracking-widest"
                                            title="{{ $checklist->is_active ? 'Desactivar' : 'Activar' }}">
                                        @if($checklist->is_active)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Desactivar
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0v4m0 4h.01M5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"/></svg>
                                            Activar
                                        @endif
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

    <x-modal-confirm />
</div>
