<div>
    <div class="mb-6">
        <a href="{{ route(auth()->user()?->rol === 'Administrador' ? 'gestor.checklists.index' : 'tecnico.checklists.tasks.index') }}" class="text-ep-primary hover:underline">← Volver</a>
        <h1 class="text-3xl font-bold text-ep-dark mt-2">{{ $checklist->title }}</h1>
        @if($checklist->description)
            <p class="text-gray-600 mt-1">{{ $checklist->description }}</p>
        @endif
    </div>

    <form wire:submit="save" class="space-y-6">
        <!-- Preguntas -->
        <div class="bg-white rounded-lg shadow divide-y">
            @foreach($checklist->questions as $question)
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $question->label }}
                        @if($question->is_required)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    
                    @if($question->help_text)
                        <p class="text-sm text-gray-500 mb-2">{{ $question->help_text }}</p>
                    @endif

                    @switch($question->type)
                        @case('text')
                            <input type="text" 
                                   wire:model="answers.{{ $question->id }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary"
                                   placeholder="Escribe tu respuesta...">
                            @break

                        @case('textarea')
                            <textarea wire:model="answers.{{ $question->id }}"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary"
                                      placeholder="Escribe tu respuesta..."></textarea>
                            @break

                        @case('number')
                            <input type="number" 
                                   wire:model="answers.{{ $question->id }}"
                                   class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary">
                            @break

                        @case('date')
                            <input type="date" 
                                   wire:model="answers.{{ $question->id }}"
                                   class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary focus:border-ep-primary">
                            @break

                        @case('select')
                            <select wire:model="answers.{{ $question->id }}"
                                    class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary">
                                <option value="">Seleccione...</option>
                                @foreach($question->options ?? [] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('radio')
                            <div class="space-y-2">
                                @foreach($question->options ?? [] as $option)
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="radio" 
                                               wire:model="answers.{{ $question->id }}"
                                               value="{{ $option }}"
                                               class="w-5 h-5 text-ep-primary focus:ring-ep-primary">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @break

                        @case('checkbox')
                            <div class="space-y-2">
                                @foreach($question->options ?? [] as $option)
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" 
                                               wire:model="answers.{{ $question->id }}"
                                               value="{{ $option }}"
                                               class="w-5 h-5 text-ep-primary rounded focus:ring-ep-primary">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @break

                        @case('signature')
                            <!-- FIRMA DIGITAL CON SIGNATURE_PAD -->
                            <div x-data="signaturePad('{{ $question->id }}')" class="space-y-3">
                                <!-- Estado: No habilitado -->
                                <div x-show="!enabled" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    <p class="text-gray-500 mb-3">Firma Digital</p>
                                    <button type="button" @click="enable()" 
                                            class="px-4 py-2 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark">
                                        ✍️ Habilitar Firma
                                    </button>
                                </div>
                                
                                <!-- Estado: Canvas de firma -->
                                <div x-show="enabled" class="border-2 border-ep-primary rounded-lg p-4 bg-white">
                                    <p class="text-sm text-gray-600 mb-2">Firme con el mouse o dedo:</p>
                                    <canvas x-ref="canvas" 
                                            class="border border-gray-300 rounded-lg bg-white w-full"
                                            style="height: 200px; touch-action: none;"></canvas>
                                    
                                    <!-- Botones -->
                                    <div class="flex gap-2 mt-3">
                                        <button type="button" @click="clear()" 
                                                class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
                                            🗑️ Limpiar
                                        </button>
                                        <button type="button" @click="disable()" 
                                                class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm">
                                            ✕ Cerrar
                                        </button>
                                    </div>
                                    
                                    <!-- Preview de firma guardada -->
                                    <div x-show="hasSignature" class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-xs text-green-600">✓ Firma capturada</p>
                                    </div>
                                </div>
                                
                                <!-- Input oculto para Livewire -->
                                <input type="hidden" 
                                       x-ref="input" 
                                       wire:model="answers.{{ $question->id }}">
                            </div>
                            @break
                    @endswitch

                    @error("answers.{$question->id}")
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>

        <!-- Observaciones -->
        <div class="bg-white rounded-lg shadow p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones Generales</label>
            <textarea wire:model="notes" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ep-primary"
                      placeholder="Notas adicionales..."></textarea>
        </div>

        <!-- Botones -->
        <div class="flex justify-end gap-4">
            <a href="{{ route(auth()->user()?->rol === 'Administrador' ? 'gestor.checklists.index' : 'tecnico.checklists.tasks.index') }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark font-semibold">
                ✓ Completar Checklist
            </button>
        </div>
    </form>
</div>

<!-- Script de signature_pad -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
<script>
    function signaturePad(questionId) {
        return {
            enabled: false,
            hasSignature: false,
            signaturePad: null,
            
            enable() {
                this.enabled = true;
                this.$nextTick(() => {
                    this.initPad();
                });
            },
            
            disable() {
                this.enabled = false;
            },
            
            initPad() {
                const canvas = this.$refs.canvas;
                if (!canvas) return;
                
                // Ajustar tamaño del canvas
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = 200 * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                
                // Inicializar signature_pad
                this.signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 0.5,
                    maxWidth: 2.5
                });
                
                // Guardar firma cuando termine de dibujar
                this.signaturePad.addEventListener('endStroke', () => {
                    this.saveSignature();
                });
            },
            
            saveSignature() {
                if (this.signaturePad && !this.signaturePad.isEmpty()) {
                    const dataUrl = this.signaturePad.toDataURL('image/png');
                    this.$refs.input.value = dataUrl;
                    this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
                    this.hasSignature = true;
                }
            },
            
            clear() {
                if (this.signaturePad) {
                    this.signaturePad.clear();
                    this.$refs.input.value = '';
                    this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
                    this.hasSignature = false;
                }
            }
        }
    }
</script>
@endpush
