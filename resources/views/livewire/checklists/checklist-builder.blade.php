<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-ep-dark">
            {{ $checklist ? 'Editar Checklist' : 'Nuevo Checklist' }}
        </h1>
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Datos Generales -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-ep-dark mb-4">Datos Generales</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input type="text" wire:model="title"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary
                                  @error('title') border-red-500 @enderror"
                           placeholder="Ej: Control de EPP">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select wire:model.live="categoryId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                        <option value="">Sin categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    </select>
                    <a href="{{ route('categories.index') }}" class="text-xs text-ep-primary hover:text-ep-primary-dark mt-1 hover:underline inline-flex items-center">
                        + Gestionar Categorías
                    </a>
                    @error('categoryId') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                </div>

                @if($categoryId)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subcategoría</label>
                        <select wire:model="subcategoryId"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                            <option value="">Sin subcategoría</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                        @error('subcategoryId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código de Registro</label>
                    <input type="text" wire:model="registryCode"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary"
                           placeholder="EP-REG-008, REG-001-0T...">
                    <p class="text-xs text-gray-500 mt-1">Código normativo opcional (ej: EP-REG-008)</p>
                    @error('registryCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea wire:model="description" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary"
                          placeholder="Descripción opcional del checklist"></textarea>
            </div>

            <div class="mt-4 flex items-center gap-6">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" wire:model="is_active" 
                           class="w-5 h-5 text-ep-primary rounded focus:ring-ep-primary">
                    <span class="text-sm text-gray-700">Checklist activo</span>
                </label>

                <label class="flex items-center space-x-3">
                    <input type="checkbox" wire:model.live="useSections" 
                           class="w-5 h-5 text-ep-primary rounded focus:ring-ep-primary">
                    <span class="text-sm text-gray-700">Organizar en secciones</span>
                </label>
            </div>
        </div>

        <!-- Secciones (si está habilitado) -->
        @if($useSections)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-ep-dark mb-4">Secciones</h2>
                
                @if(count($sections) > 0)
                    <div class="space-y-2 mb-4">
                        @foreach($sections as $index => $section)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border">
                                <div class="flex-1">
                                    <span class="font-medium">{{ $section['title'] }}</span>
                                </div>
                                <button type="button" wire:click="removeSection({{ $index }})"
                                        class="text-red-500 hover:text-red-700 px-2">✕</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex gap-2">
                    <input type="text" wire:model="newSectionTitle"
                           wire:keydown.enter.prevent="addSection"
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary"
                           placeholder="Nombre de la sección (ej: Ambiente de trabajo)">
                    <button type="button" wire:click="addSection"
                            class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark">
                        + Agregar Sección
                    </button>
                </div>
            </div>
        @endif

        <!-- Preguntas -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-ep-dark mb-4">Preguntas</h2>
            @error('questions') <div class="text-red-500 text-sm mb-4">{{ $message }}</div> @enderror
            
            <!-- Lista de preguntas agregadas -->
            @if(count($questions) > 0)
                <div class="space-y-3 mb-6">
                    @foreach($questions as $index => $question)
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border">
                            <div class="flex flex-col gap-1">
                                <button type="button" wire:click="moveUp({{ $index }})"
                                        class="text-gray-400 hover:text-ep-primary {{ $index === 0 ? 'opacity-30' : '' }}"
                                        {{ $index === 0 ? 'disabled' : '' }}>▲</button>
                                <button type="button" wire:click="moveDown({{ $index }})"
                                        class="text-gray-400 hover:text-ep-primary {{ $index === count($questions) - 1 ? 'opacity-30' : '' }}"
                                        {{ $index === count($questions) - 1 ? 'disabled' : '' }}>▼</button>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">{{ $question['label'] }}</div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span class="px-2 py-0.5 bg-ep-primary/10 text-ep-primary rounded">
                                        {{ ucfirst($question['type']) }}
                                    </span>
                                    @if($question['is_required'])
                                        <span class="text-red-500">* Requerido</span>
                                    @endif
                                    @if(!empty($question['options']))
                                        <span>{{ count($question['options']) }} opciones</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" wire:click="removeQuestion({{ $index }})"
                                    class="text-red-500 hover:text-red-700 px-2">✕</button>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Agregar nueva pregunta -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4" x-data="{ showOptions: ['select', 'radio', 'checkbox'].includes($wire.newQuestion.type) }">
                <h3 class="font-medium text-gray-700 mb-3">Agregar Pregunta</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" wire:model="newQuestion.label"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary"
                               placeholder="Texto de la pregunta">
                        @error('newQuestion.label') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <select wire:model.live="newQuestion.type"
                                x-on:change="showOptions = ['select', 'radio', 'checkbox'].includes($event.target.value)"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                            <option value="text">Texto corto</option>
                            <option value="textarea">Texto largo</option>
                            <option value="select">Dropdown</option>
                            <option value="radio">Opciones (una)</option>
                            <option value="checkbox">Opciones (múltiples)</option>
                            <option value="number">Número</option>
                            <option value="date">Fecha</option>
                            <option value="signature">Firma</option>
                        </select>
                    </div>
                </div>

                <!-- Opciones para select/radio/checkbox -->
                <div x-show="showOptions" x-cloak class="mt-4 p-4 bg-gray-100 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opciones</label>
                    <div class="flex gap-2 mb-2">
                        <input type="text" wire:model="optionInput"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg"
                               placeholder="Nueva opción" wire:keydown.enter.prevent="addOption">
                        <button type="button" wire:click="addOption"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            Agregar
                        </button>
                    </div>
                    @if(!empty($newQuestion['options']))
                        <div class="flex flex-wrap gap-2">
                            @foreach($newQuestion['options'] as $i => $opt)
                                <span class="inline-flex items-center px-3 py-1 bg-white border rounded-full text-sm">
                                    {{ $opt }}
                                    <button type="button" wire:click="removeOption({{ $i }})" class="ml-2 text-red-500">×</button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between mt-4">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="newQuestion.is_required" class="w-4 h-4 text-ep-primary rounded">
                            <span class="text-sm text-gray-700">Campo requerido</span>
                        </label>

                        @if($useSections && count($sections) > 0)
                            <select wire:model="newQuestion.section_id"
                                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                                <option value="">Sin sección</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section['id'] }}">{{ $section['title'] }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    
                    <button type="button" wire:click="addQuestion"
                            class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark">
                        + Agregar Pregunta
                    </button>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('checklists.index') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark font-semibold">
                {{ $checklist ? 'Guardar Cambios' : 'Crear Checklist' }}
            </button>
        </div>
    </form>


</div>
