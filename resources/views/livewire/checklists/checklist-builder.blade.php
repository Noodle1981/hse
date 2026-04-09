<div class="w-full mx-auto">
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

                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Ingeniería de <span class="text-white">Protocolos</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none">
                            {{ $checklist ? 'Editando' : 'Creando' }} Diseño Maestro
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('gestor.checklists.index') }}" 
                       class="h-10 px-5 bg-white/5 border border-white/10 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic flex items-center gap-2 transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Cancelar
                    </a>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-ep-primary/30">
        <form wire:submit="save" class="max-w-[95rem] mx-auto space-y-10 animate-fade-in">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                {{-- COLUMNA LATERAL: DATOS GENERALES --}}
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden group/card">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-ep-primary/40 to-transparent"></div>
                        <h2 class="text-sm font-black text-white italic uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-ep-primary/20 flex items-center justify-center text-ep-primary">01</span>
                            Identidad del Protocolo
                        </h2>

                        <div class="space-y-6">
                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Título del Checklist</span>
                                <input type="text" wire:model="title" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic" placeholder="Ej: Control de EPP Avanzado">
                                @error('title') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="relative group">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic">Categoría Maestra</span>
                                    <div class="flex gap-3">
                                        @if($categoryId)
                                            <button type="button" wire:click="editCategory" class="text-[9px] font-black text-white hover:text-ep-primary uppercase tracking-widest italic transition-colors">✎ Editar Seleccionada</button>
                                        @endif
                                        <button type="button" wire:click="openCategoryModal" class="text-[9px] font-black text-ep-primary hover:text-ep-primary/80 uppercase tracking-widest italic transition-colors">+ Crear Nueva</button>
                                    </div>
                                </div>
                                <select wire:model.live="categoryId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                                    <option value="">SIN CATEGORÍA</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if($categoryId)
                                <div class="relative group animate-fade-in-down">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic">Subcategoría Específica</span>
                                        <div class="flex gap-3">
                                            @if($subcategoryId)
                                                <button type="button" wire:click="editSubcategory" class="text-[9px] font-black text-white hover:text-emerald-400 uppercase tracking-widest italic transition-colors">✎ Editar Seleccionada</button>
                                            @endif
                                            <button type="button" wire:click="openSubcategoryModal" class="text-[9px] font-black text-indigo-400 hover:text-indigo-300 uppercase tracking-widest italic transition-colors">+ Crear Nueva</button>
                                        </div>
                                    </div>
                                    <select wire:model.live="subcategoryId" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                                        <option value="">SIN SUBCATEGORÍA</option>
                                        @foreach($subcategories as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Código de Registro (ISO/NORMA)</span>
                                <input type="text" wire:model="registryCode" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic" placeholder="EP-REG-008, OHSAS-01...">
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Descripción Operativa</span>
                                <textarea wire:model="description" rows="3" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic" placeholder="Instrucciones generales para el técnico..."></textarea>
                            </div>

                            <div class="flex flex-col gap-4 p-6 bg-black/20 rounded-[2rem] border border-white/5">
                                <label class="flex items-center gap-4 cursor-pointer group/toggle">
                                    <div class="relative">
                                        <input type="checkbox" wire:model.live="is_active" class="sr-only peer">
                                        <div class="w-10 h-5 bg-white/10 rounded-full transition-colors group-hover/toggle:bg-white/20 border border-white/5 peer-checked:bg-emerald-500/20"></div>
                                        <div class="absolute left-1 top-1 w-3 h-3 bg-white/40 rounded-full transition-all peer-checked:translate-x-5 peer-checked:bg-emerald-400"></div>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest italic {{ $is_active ? 'text-emerald-400' : 'text-white/20' }}">Checklist Publicado</span>
                                </label>

                                <label class="flex items-center gap-4 cursor-pointer group/toggle">
                                    <div class="relative">
                                        <input type="checkbox" wire:model.live="useSections" class="sr-only">
                                        <div class="w-10 h-5 bg-white/10 rounded-full transition-colors group-hover/toggle:bg-white/20"></div>
                                        <div class="absolute left-1 top-1 w-3 h-3 bg-white/40 rounded-full transition-all peer-checked:translate-x-5 peer-checked:bg-ep-primary"></div>
                                    </div>
                                    <input type="checkbox" wire:model.live="useSections" class="hidden">
                                    <span class="text-[10px] font-black uppercase tracking-widest italic {{ $useSections ? 'text-ep-primary' : 'text-white/20' }}">Habilitar Secciones</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIONES: DINÁMICO --}}
                    @if($useSections)
                        <div class="bg-ep-dark/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative animate-fade-in-up">
                            <h2 class="text-sm font-black text-white italic uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-ep-primary/20 flex items-center justify-center text-ep-primary text-xs">S</span>
                                Arquitectura de Secciones
                            </h2>

                            <div class="space-y-4 mb-8 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                @forelse($sections as $index => $section)
                                    <div class="flex items-center justify-between p-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl transition-all group/sec">
                                        <span class="text-[11px] font-black text-white/80 italic uppercase tracking-tight">{{ $section['title'] }}</span>
                                        <button type="button" wire:click="removeSection({{ $index }})" class="w-8 h-8 flex items-center justify-center text-white/20 hover:text-red-500 transition-colors">✕</button>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-[9px] font-bold text-white/10 uppercase tracking-widest italic">Cero secciones definidas</div>
                                @endforelse
                            </div>

                            <div class="relative">
                                <input type="text" wire:model="newSectionTitle" wire:keydown.enter.prevent="addSection" class="w-full bg-black/40 border border-white/10 rounded-2xl pl-6 pr-14 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/50 transition-all placeholder-white/5 italic" placeholder="+ Nueva Sección...">
                                <button type="button" wire:click="addSection" class="absolute right-3 top-2 w-10 h-10 bg-ep-primary text-white rounded-xl flex items-center justify-center shadow-lg active:scale-90 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- COLUMNA PRINCIPAL: TALLER DE PREGUNTAS --}}
                <div class="lg:col-span-2 space-y-10">
                    
                    {{-- MASTER LIST DE PREGUNTAS --}}
                    <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl relative overflow-hidden group/workshop">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/40 to-transparent"></div>
                        
                        <div class="flex items-center justify-between mb-10">
                            <h2 class="text-sm font-black text-white italic uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">02</span>
                                Cronología de Puntos de Control
                            </h2>
                            <div class="px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-xl">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">{{ count($questions) }} Entradas Detectadas</span>
                            </div>
                        </div>

                        <div class="space-y-4 mb-12">
                            @forelse($questions as $index => $question)
                                <div class="group/row flex items-center gap-6 p-6 bg-white/5 hover:bg-white/10 border border-white/5 rounded-3xl transition-all duration-500 relative">
                                    {{-- CONTROLES DE ORDEN --}}
                                    <div class="flex flex-col gap-2 opacity-20 group-hover/row:opacity-100 transition-opacity">
                                        <button type="button" wire:click="moveUp({{ $index }})" class="p-1 text-white hover:text-ep-primary transition-colors {{ $index === 0 ? 'pointer-events-none opacity-10' : '' }}">▲</button>
                                        <button type="button" wire:click="moveDown({{ $index }})" class="p-1 text-white hover:text-ep-primary transition-colors {{ $index === count($questions) - 1 ? 'pointer-events-none opacity-10' : '' }}">▼</button>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center gap-4 mb-2">
                                            <span class="text-xs font-black text-white italic uppercase tracking-tight">{{ $question['label'] }}</span>
                                            @if($question['is_required'])
                                                <span class="text-[9px] font-black text-red-500 uppercase italic tracking-widest animate-pulse">* Crítico</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <span class="px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[8px] font-black tracking-[0.2em] uppercase italic rounded-lg">
                                                TIPO: {{ $question['type'] }}
                                            </span>
                                            @if($useSections && !empty($question['section_id']))
                                                @php
                                                    $sectionName = collect($sections)->firstWhere('id', $question['section_id'])['title'] ?? 'Sección eliminada';
                                                @endphp
                                                <span class="px-3 py-1 bg-white/5 border border-white/10 text-white/40 text-[8px] font-black tracking-widest uppercase italic rounded-lg flex items-center gap-2">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                                    {{ $sectionName }}
                                                </span>
                                            @endif
                                            @if(!empty($question['options']))
                                                <span class="text-[8px] font-black text-emerald-400 uppercase tracking-widest italic">{{ count($question['options']) }} Opciones Configuradas</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <button type="button" wire:click="editQuestion({{ $index }})" class="w-11 h-11 bg-white/5 hover:bg-indigo-600 border border-white/10 text-white rounded-2xl flex items-center justify-center transition-all shadow-lg active:scale-95 group/editbtn">
                                            <svg class="w-4 h-4 group-hover/editbtn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <button type="button" wire:click="removeQuestion({{ $index }})" class="w-11 h-11 bg-white/5 hover:bg-red-600 border border-white/10 text-white/40 hover:text-white rounded-2xl flex items-center justify-center transition-all shadow-lg active:scale-95">✕</button>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 text-center border-2 border-dashed border-white/5 rounded-[3rem]">
                                    <p class="text-xs font-black text-white/10 uppercase tracking-[0.4em] italic mb-2">Protocolo sin Variables Definidas</p>
                                    <p class="text-[9px] text-white/5 italic">Utilice el panel inferior para añadir puntos de control técnico</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- PANEL DE INGENIERÍA: AGREGAR PREGUNTA --}}
                        <div id="question-form" 
                             class="relative p-10 bg-black/40 border-2 {{ $editingQuestionIndex !== null ? 'border-indigo-500 shadow-[0_0_30px_rgba(99,102,241,0.2)]' : 'border-dashed border-white/10 hover:border-white/20' }} rounded-[3rem] transition-all duration-700 overflow-hidden"
                             x-data="{ showOptions: ['select', 'radio', 'checkbox'].includes($wire.newQuestion.type) }"
                             x-init="
                                 $watch('$wire.newQuestion.type', value => { showOptions = ['select', 'radio', 'checkbox'].includes(value); });
                                 $watch('$wire.editingQuestionIndex', value => { 
                                     if (value !== null) { 
                                         showOptions = ['select', 'radio', 'checkbox'].includes($wire.newQuestion.type);
                                         setTimeout(() => { document.getElementById('question-form').scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
                                     }
                                 });
                             ">
                            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-400/30 to-transparent"></div>
                            
                            <h3 class="text-xs font-black text-indigo-400 italic uppercase tracking-[0.3em] mb-10">
                                @if($editingQuestionIndex !== null)
                                    Laboratorio: Refinando Variable #{{ $editingQuestionIndex + 1 }}
                                @else
                                    Laboratorio: Nueva Variable Crítica
                                @endif
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-10">
                                <div class="md:col-span-8 relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Texto de Pregunta / Variable</span>
                                    <input type="text" wire:model="newQuestion.label" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic" placeholder="Ej: Estado de lubricación de rodamientos superiores...">
                                    @error('newQuestion.label') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="md:col-span-4 relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Tipo de Respuesta</span>
                                    <select wire:model.live="newQuestion.type" x-on:change="showOptions = ['select', 'radio', 'checkbox'].includes($event.target.value)" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase">
                                        <option value="text">TEXTO CORTO</option>
                                        <option value="textarea">TEXTO LARGO</option>
                                        <option value="select">DROPDOWN / MENÚ</option>
                                        <option value="radio">OPCIÓN ÚNICA (BOLITA)</option>
                                        <option value="checkbox">OPCIÓN MÚLTIPLE (CHECK)</option>
                                        <option value="number">CANTIDAD NUMÉRICA</option>
                                        <option value="date">FECHA / CALENDARIO</option>
                                    </select>
                                </div>
                            </div>

                            {{-- LÓGICA DE NÚMEROS --}}
                            <div x-show="$wire.newQuestion.type === 'number'" x-transition x-cloak class="mb-10 p-8 bg-white/5 border border-white/5 rounded-3xl animate-fade-in">
                                <h4 class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em] italic mb-6">Protocolo de Aceptación Numérica</h4>
                                <div class="grid grid-cols-2 gap-8">
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Límite Mínimo</span>
                                        <input type="number" step="0.01" wire:model="newQuestion.min_value" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic">
                                    </div>
                                    <div class="relative group">
                                        <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Límite Máximo</span>
                                        <input type="number" step="0.01" wire:model="newQuestion.max_value" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/20 transition-all placeholder-white/5 italic">
                                    </div>
                                </div>
                            </div>

                            {{-- OPCIONES MASTER --}}
                            <div x-show="showOptions" x-transition x-cloak class="mb-10 p-8 bg-white/5 border border-white/5 rounded-3xl animate-fade-in">
                                <div class="flex justify-between items-center mb-8">
                                    <h4 class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.3em] italic">Configuración de Atributos y Disparadores</h4>
                                    <span class="text-[8px] font-bold text-white/20 uppercase tracking-widest italic">Marque las opciones que causen un fallo de auditoría</span>
                                </div>
                                
                                <div class="flex gap-4 mb-10 group/opt">
                                    <input type="text" wire:model="optionInput" class="flex-1 bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic" placeholder="+ Nueva Variable (Ej: DEFECTUOSO, NO CUMPLE)..." wire:keydown.enter.prevent="addOption">
                                    <button type="button" wire:click="addOption" class="h-14 px-8 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-xl shadow-indigo-600/20 active:scale-95 transition-all">INYECTAR</button>
                                </div>

                                @if(!empty($newQuestion['options']))
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($newQuestion['options'] as $i => $opt)
                                            <div class="group/optrow flex items-center justify-between p-4 bg-black/40 border {{ in_array($opt, $newQuestion['failure_values']) ? 'border-red-500/40 bg-red-500/5 shadow-[0_0_15px_rgba(239,68,68,0.05)]' : 'border-white/5' }} rounded-2x border border-white/10 transition-all duration-300">
                                                <button type="button" wire:click="toggleFailureValue('{{ $opt }}')" class="flex items-center gap-3">
                                                    <div class="w-5 h-5 rounded-lg border {{ in_array($opt, $newQuestion['failure_values']) ? 'bg-red-500 border-red-500 shadow-[0_0_8px_#ef4444]' : 'bg-white/5 border-white/10' }} flex items-center justify-center transition-all">
                                                        @if(in_array($opt, $newQuestion['failure_values']))
                                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        @endif
                                                    </div>
                                                    <span class="text-[10px] font-black uppercase tracking-tighter italic {{ in_array($opt, $newQuestion['failure_values']) ? 'text-red-400' : 'text-white/60' }}">{{ $opt }}</span>
                                                </button>
                                                <button type="button" wire:click="removeOption({{ $i }})" class="text-white/10 hover:text-red-500 transition-colors">✕</button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-10">
                                <div class="flex items-center gap-10">
                                    <label class="flex items-center gap-4 cursor-pointer group/toggle">
                                        <div class="relative">
                                            <input type="checkbox" wire:model.live="newQuestion.is_required" class="sr-only peer">
                                            <div class="w-10 h-5 bg-white/10 rounded-full transition-colors group-hover/toggle:bg-white/20 border border-white/5 peer-checked:bg-red-500/20"></div>
                                            <div class="absolute left-1 top-1 w-3 h-3 bg-white/40 rounded-full transition-all peer-checked:translate-x-5 peer-checked:bg-red-500"></div>
                                        </div>
                                        <span class="text-[10px] font-black uppercase tracking-widest italic {{ $newQuestion['is_required'] ? 'text-red-500' : 'text-white/20' }}">REQUERIDO</span>
                                    </label>

                                    @if($useSections && count($sections) > 0)
                                        <div class="relative group">
                                            <span class="absolute -top-2.5 left-4 bg-black px-2 text-[7px] font-black text-indigo-400 uppercase tracking-widest italic z-10">Vincular a Sección</span>
                                            <select wire:model="newQuestion.section_id" class="bg-black/60 border border-white/10 rounded-xl px-6 py-2.5 text-[10px] font-black text-white italic uppercase focus:ring-2 focus:ring-indigo-500/50 appearance-none cursor-pointer pr-10">
                                                <option value="">SIN ASIGNAR</option>
                                                @foreach($sections as $section)
                                                    <option value="{{ $section['id'] }}">{{ $section['title'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex gap-4">
                                    @if($editingQuestionIndex !== null)
                                        <button type="button" wire:click="cancelEdit" class="h-14 px-8 bg-white/5 text-white/40 rounded-2xl text-[10px] font-black uppercase tracking-widest italic border border-white/5 hover:text-white transition-all">Cancelar Refinado</button>
                                        <button type="button" wire:click="updateQuestion" class="h-14 px-10 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-xl shadow-indigo-600/20 active:scale-95 transition-all">Sincronizar Variable</button>
                                    @else
                                        <button type="button" wire:click="addQuestion" class="h-14 px-10 bg-ep-primary text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic shadow-xl shadow-ep-primary/20 active:scale-95 transition-all">+ Inyectar Punto de Control</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER DE ACCIONES FINAL: PROTOCOLO --}}
            <div class="bg-ep-dark/60 backdrop-blur-md rounded-[3rem] border border-white/5 p-8 shadow-2xl animate-fade-in-up flex items-center justify-between mb-20">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-6">
                        <div class="h-1 w-20 bg-gradient-to-r from-ep-primary to-transparent rounded-full"></div>
                        <span class="text-[9px] font-black text-white/20 uppercase tracking-[0.5em] italic">XamaSafe Build Protocol v4.5</span>
                    </div>
                    @if ($errors->any())
                        <div class="text-[10px] font-black text-red-500 uppercase italic tracking-widest animate-pulse ml-26">
                            ⚠️ Faltan campos obligatorios (Título o puntos de control)
                        </div>
                    @endif
                </div>
                <div class="flex gap-6">
                    <a href="{{ route('gestor.checklists.index') }}" class="h-14 px-10 bg-white/5 border border-white/10 text-white hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest italic flex items-center transition-all">Descartar Cambios</a>
                    <button type="submit" class="h-14 px-14 bg-ep-primary text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic shadow-[0_0_30px_rgba(246,146,57,0.3)] hover:brightness-110 active:scale-95 transition-all">
                        {{ $checklist ? 'CONSOLIDAR PROTOCOLO' : 'INICIAR PROTOCOLO MAESTRO' }}
                    </button>
                </div>
            </div>
        </form>

        {{-- MODAL HUD: SUBCATEGORÍA --}}
        @if($showSubcategoryModal)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
                <div class="absolute inset-0 bg-[#0a0a0f]/90 backdrop-blur-xl" wire:click.self="closeSubcategoryModal"></div>
                
                <div class="relative bg-ep-dark/80 border border-white/10 w-full max-w-lg rounded-[3rem] shadow-[0_0_100px_rgba(0,0,0,0.8)] overflow-hidden animate-fade-in-up">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-emerald-500/40 to-transparent"></div>
                    
                    <div class="p-10">
                        <div class="flex items-center gap-6 mb-10">
                            <div class="w-16 h-16 rounded-[2rem] bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 rotate-12">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white italic uppercase tracking-tight leading-none mb-2">
                                    {{ $editingSubcategoryId ? 'Editando Subcategoría' : 'Nueva Subcategoría' }}
                                </h3>
                                <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest italic">
                                    {{ $editingSubcategoryId ? 'Refinando Especialización' : 'Especialización de Auditoría' }}
                                </p>
                            </div>
                        </div>

                        <form wire:submit.prevent="saveSubcategory" class="space-y-8">
                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Categoría Vinculada</span>
                                <select wire:model="newSubcategory.category_id" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/40 transition-all [color-scheme:dark] italic uppercase">
                                    <option value="">SELECCIONAR CATEGORÍA...</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('newSubcategory.category_id') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Código Maestro (Slug)</span>
                                <input type="text" wire:model="newSubcategory.code" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/40 transition-all placeholder-white/5 italic" placeholder="ej: elec-potencia">
                                @error('newSubcategory.code') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white uppercase tracking-[0.3em] italic z-10">Nombre Comercial</span>
                                <input type="text" wire:model="newSubcategory.name" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/40 transition-all placeholder-white/5 italic" placeholder="Ej: Electricidad de Gran Potencia">
                                @error('newSubcategory.name') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Misión Detallada</span>
                                <textarea wire:model="newSubcategory.description" rows="3" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/10 transition-all italic placeholder-white/5"></textarea>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button type="submit" class="flex-1 h-14 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
                                    {{ $editingSubcategoryId ? 'GUARDAR CAMBIOS' : 'CONSOLIDAR' }}
                                </button>
                                <button type="button" wire:click="closeSubcategoryModal" class="h-14 px-8 bg-white/5 text-white/40 rounded-2xl text-[10px] font-black uppercase tracking-widest italic border border-white/5">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL HUD: CATEGORÍA MAESTRA --}}
        @if($showCategoryModal)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
                <div class="absolute inset-0 bg-[#0a0a0f]/90 backdrop-blur-xl" wire:click.self="closeCategoryModal"></div>
                
                <div class="relative bg-ep-dark/80 border border-white/10 w-full max-w-lg rounded-[3rem] shadow-[0_0_100px_rgba(0,0,0,0.8)] overflow-hidden animate-fade-in-up">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-ep-primary/40 to-transparent"></div>
                    
                    <div class="p-10">
                        <div class="flex items-center gap-6 mb-10">
                            <div class="w-16 h-16 rounded-[2rem] bg-ep-primary/10 border border-ep-primary/20 flex items-center justify-center text-ep-primary rotate-12">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white italic uppercase tracking-tight leading-none mb-2">
                                    {{ $editingCategoryId ? 'Editando Categoría Maestra' : 'Nueva Categoría Maestra' }}
                                </h3>
                                <p class="text-[10px] font-bold text-ep-primary uppercase tracking-widest italic">
                                    {{ $editingCategoryId ? 'Refinando Estructura Global' : 'Estructura Global de Procesos' }}
                                </p>
                            </div>
                        </div>

                        <form wire:submit.prevent="saveCategory" class="space-y-8">
                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10">Código de Referencia</span>
                                <input type="text" wire:model="newCategory.code" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/40 transition-all placeholder-white/5 italic" placeholder="ej: seg-higiene">
                                @error('newCategory.code') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white uppercase tracking-[0.3em] italic z-10">Nombre de la Categoría</span>
                                <input type="text" wire:model="newCategory.name" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/40 transition-all placeholder-white/5 italic" placeholder="Ej: Seguridad e Higiene">
                                @error('newCategory.name') <span class="text-red-500 text-[8px] font-black uppercase mt-2 block ml-2">{{ $message }}</span> @enderror
                            </div>

                            <div class="relative group">
                                <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Misión de Categoría</span>
                                <textarea wire:model="newCategory.description" rows="3" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/10 transition-all italic placeholder-white/5"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Color Distintivo</span>
                                    <input type="color" wire:model="newCategory.color" class="w-full bg-black/60 border border-white/5 h-14 rounded-2xl p-1 cursor-pointer">
                                </div>
                                <div class="relative group">
                                    <span class="absolute -top-2.5 left-4 bg-[#1e1e2d] px-2 text-[8px] font-black text-white/30 uppercase tracking-[0.3em] italic z-10">Icono (Lucide)</span>
                                    <input type="text" wire:model="newCategory.icon" class="w-full bg-black/60 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-white/10 transition-all italic placeholder-white/5">
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button type="submit" class="flex-1 h-14 bg-ep-primary text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic hover:brightness-110 transition-all shadow-xl shadow-ep-primary/20 active:scale-95">
                                    {{ $editingCategoryId ? 'GUARDAR CAMBIOS' : 'CONSOLIDAR CATEGORÍA' }}
                                </button>
                                <button type="button" wire:click="closeCategoryModal" class="h-14 px-8 bg-white/5 text-white/40 rounded-2xl text-[10px] font-black uppercase tracking-widest italic border border-white/5">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
        .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
        .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
        @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(246,146,57,0.3); }
    </style>
</div>

