<div>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-ep-dark">
            {{ $checklist ? 'Editar Checklist' : 'Nuevo Checklist' }}
        </h1>
        <a href="{{ route('gestor.categories.index') }}" 
           class="bg-white text-ep-primary border border-gray-200 hover:bg-gray-50 hover:text-ep-primary-dark px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            Administrar Categorías
        </a>
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
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                    </div>
                    <select wire:model.live="categoryId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                        <option value="">Sin categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
                </div>

                @if($categoryId)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-sm font-medium text-gray-700">Subcategoría</label>
                            <button type="button" wire:click="openSubcategoryModal"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-ep-dark border border-gray-200 px-2 py-1 rounded transition-colors">
                                + Crear Subcategoría
                            </button>
                        </div>
                        <select wire:model="subcategoryId"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                            <option value="">Sin subcategoría</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                        @error('subcategoryId') <span class="text-red-500 text-sm block">{{ $message }}</span> @enderror
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
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-ep-dark">Preguntas</h2>
                @error('questions') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
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
                                    @if($useSections && !empty($question['section_id']))
                                        @php
                                            $sectionName = collect($sections)->firstWhere('id', $question['section_id'])['title'] ?? 'Sección eliminada';
                                        @endphp
                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                            </svg>
                                            {{ $sectionName }}
                                        </span>
                                    @endif
                                    @if($question['is_required'])
                                        <span class="text-red-500">* Requerido</span>
                                    @endif
                                    @if(!empty($question['options']))
                                        <span>{{ count($question['options']) }} opciones</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="editQuestion({{ $index }})"
                                        class="text-blue-500 hover:text-blue-700 px-2" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button type="button" wire:click="removeQuestion({{ $index }})"
                                        class="text-red-500 hover:text-red-700 px-2" title="Eliminar">✕</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Agregar/Editar pregunta -->
            <div id="question-form" 
                 class="border-2 border-dashed rounded-lg p-4 transition-all duration-300"
                 :class="$wire.editingQuestionIndex !== null ? 'border-blue-400 bg-blue-50' : 'border-gray-300'"
                 x-data="{ showOptions: ['select', 'radio', 'checkbox'].includes($wire.newQuestion.type) }"
                 x-init="$watch('$wire.editingQuestionIndex', value => { 
                     if (value !== null) { 
                         setTimeout(() => {
                             document.getElementById('question-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
                         }, 100);
                     }
                 })">
                <h3 class="font-medium text-gray-700 mb-3">
                    @if($editingQuestionIndex !== null)
                        Editar Pregunta
                    @else
                        Agregar Pregunta
                    @endif
                </h3>
                
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

                <!-- Lógica de Rangos (para números) -->
                <div x-show="$wire.newQuestion.type === 'number'" x-cloak class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Lógica de Aprobación (Opcional)</h4>
                    <p class="text-xs text-gray-500 mb-3">Defina el rango de valores aceptables. Si el valor ingresado está fuera de este rango, la inspección se marcará como "No Pasa".</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Mínimo Aceptable</label>
                            <input type="number" step="0.01" wire:model="newQuestion.min_value"
                                   class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                   placeholder="Ej: 0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Máximo Aceptable</label>
                            <input type="number" step="0.01" wire:model="newQuestion.max_value"
                                   class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                   placeholder="Ej: 100.00">
                        </div>
                    </div>
                </div>

                <!-- Opciones para select/radio/checkbox -->
                <div x-show="showOptions" x-cloak class="mt-4 p-4 bg-gray-100 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700">Opciones y Disparadores de Falla</label>
                        <span class="text-xs text-gray-500">Marque las opciones que causen que la inspección <b>No Pase</b>.</span>
                    </div>
                    <div class="flex gap-2 mb-4">
                        <input type="text" wire:model="optionInput"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg"
                               placeholder="Nueva opción (Ej: Mal estado, No cumple...)" wire:keydown.enter.prevent="addOption">
                        <button type="button" wire:click="addOption"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            Agregar
                        </button>
                    </div>
                    @if(!empty($newQuestion['options']))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($newQuestion['options'] as $i => $opt)
                                <div class="flex items-center justify-between p-2 bg-white border rounded-lg {{ in_array($opt, $newQuestion['failure_values']) ? 'border-red-300 bg-red-50' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <button type="button" wire:click="toggleFailureValue('{{ $opt }}')" 
                                                class="flex items-center gap-2 text-sm {{ in_array($opt, $newQuestion['failure_values']) ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                            <div class="w-4 h-4 rounded border {{ in_array($opt, $newQuestion['failure_values']) ? 'bg-red-500 border-red-500' : 'bg-white border-gray-300' }} flex items-center justify-center">
                                                @if(in_array($opt, $newQuestion['failure_values']))
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            {{ $opt }}
                                        </button>
                                    </div>
                                    <button type="button" wire:click="removeOption({{ $i }})" class="text-gray-400 hover:text-red-500 px-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
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
                            <div class="flex flex-col">
                                <label class="text-[10px] text-gray-400 uppercase font-bold mb-0.5">Sección</label>
                                <select wire:model="newQuestion.section_id"
                                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white">
                                    <option value="">Sin sección</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section['id'] }}">{{ $section['title'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif


                    </div>
                    
                    <div class="flex gap-2">
                        @if($editingQuestionIndex !== null)
                            <button type="button" wire:click="cancelEdit"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Cancelar
                            </button>
                            <button type="button" wire:click="updateQuestion"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Actualizar Pregunta
                            </button>
                        @else
                            <button type="button" wire:click="addQuestion"
                                    class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark">
                                + Agregar Pregunta
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('gestor.checklists.index') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark font-semibold">
                {{ $checklist ? 'Guardar Cambios' : 'Crear Checklist' }}
            </button>
        </div>
    </form>

    <!-- Modal para crear subcategoría -->
    @if($showSubcategoryModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeSubcategoryModal">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-ep-dark">Crear Nueva Subcategoría</h3>
                    <button type="button" wire:click="closeSubcategoryModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveSubcategory" class="space-y-4">
                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <select wire:model="newSubcategory.category_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary
                                       @error('newSubcategory.category_id') border-red-500 @enderror">
                            <option value="">Seleccionar categoría</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('newSubcategory.category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Código (Nueva Subcategoría) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Código *
                            <span class="text-xs text-gray-500 font-normal">(ej: electricidad)</span>
                        </label>
                        <input type="text" wire:model="newSubcategory.code"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary
                                      @error('newSubcategory.code') border-red-500 @enderror"
                               placeholder="electricidad">
                        @error('newSubcategory.code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" wire:model="newSubcategory.name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary
                                      @error('newSubcategory.name') border-red-500 @enderror"
                               placeholder="Nombre de la subcategoría">
                        @error('newSubcategory.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea wire:model="newSubcategory.description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary"
                                  placeholder="Descripción opcional"></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="closeSubcategoryModal"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark">
                            Crear Subcategoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif



</div>
