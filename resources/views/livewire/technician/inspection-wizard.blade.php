<div class="py-6 min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-ep-primary bg-ep-primary/10">
                    Paso {{ $currentStep + 1 }} de {{ $totalSteps }}
                </span>
                <span class="text-xs font-semibold inline-block text-ep-primary">
                    {{ round((($currentStep + 1) / $totalSteps) * 100) }}%
                </span>
            </div>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                <div style="width:{{ (($currentStep + 1) / $totalSteps) * 100 }}%" 
                     class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-ep-primary transition-all duration-500"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Step 0: Intro / Context -->
            @if($currentStep === 0)
                <div class="p-8">
                    <div class="text-center mb-8">
                        <div class="mx-auto h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $checklist->title }}</h2>
                        <p class="mt-2 text-gray-500">{{ $checklist->description }}</p>
                    </div>

                    <div class="space-y-4 bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <h3 class="font-semibold text-gray-700 uppercase text-xs tracking-wider">Detalles de la Inspección</h3>
                        @if($assignment)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-400">Empresa</p>
                                    <p class="text-sm font-medium">{{ $assignment->empresa->nombre }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Sede</p>
                                    <p class="text-sm font-medium">{{ $assignment->sede->nombre_sede }}</p>
                                </div>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-400">Inspector</p>
                            <p class="text-sm font-medium">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Steps 1 to N: Sections -->
            @foreach($sections as $index => $section)
                @if($currentStep === ($index + 1))
                    <div class="p-8">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-gray-900">{{ $section['title'] }}</h2>
                            @if($section['description'])
                                <p class="text-sm text-gray-500">{{ $section['description'] }}</p>
                            @endif
                        </div>

                        <div class="space-y-8">
                            @foreach($checklist->questions->where('section_id', $section['id'])->sortBy('order') as $question)
                                <div class="space-y-3">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        {{ $question->label }}
                                        @if($question->is_required) <span class="text-red-500">*</span> @endif
                                    </label>
                                    
                                    @if($question->help_text)
                                        <p class="text-xs text-gray-400 italic">{{ $question->help_text }}</p>
                                    @endif

                                    @if($question->type === 'radio')
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                            @foreach($question->options as $option)
                                                <label class="relative flex items-center justify-center p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors
                                                    {{ ($answers[$question->id] ?? '') === $option ? 'border-ep-primary bg-ep-primary/5 text-ep-primary ring-1 ring-ep-primary' : 'border-gray-200' }}">
                                                    <input type="radio" wire:model.live="answers.{{ $question->id }}" value="{{ $option }}" class="sr-only">
                                                    <span class="text-sm font-medium">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'select')
                                        <select wire:model.live="answers.{{ $question->id }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm">
                                            <option value="">Seleccione una opción...</option>
                                            @foreach($question->options as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($question->type === 'checkbox')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach($question->options as $option)
                                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="answers.{{ $question->id }}" value="{{ $option }}" class="h-4 w-4 text-ep-primary focus:ring-ep-primary border-gray-300 rounded">
                                                    <span class="text-sm text-gray-700">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($question->type === 'textarea')
                                        <textarea wire:model.live="answers.{{ $question->id }}" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" placeholder="Escriba aquí..."></textarea>
                                    @elseif($question->type === 'number')
                                        <input type="number" step="any" wire:model.live="answers.{{ $question->id }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" placeholder="0.00">
                                    @elseif($question->type === 'date')
                                        <input type="date" wire:model.live="answers.{{ $question->id }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm">
                                    @else
                                        <input type="text" wire:model.live="answers.{{ $question->id }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" placeholder="Escriba su respuesta...">
                                    @endif

                                    @error("answers.{$question->id}")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Step Final: Notes and Signatures -->
            @if($currentStep === ($totalSteps - 1))
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Finalización</h2>
                    
                    <div class="space-y-6">
                        <!-- Observaciones Generales -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Observaciones Generales</label>
                            <textarea wire:model.live="notes" rows="4" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" placeholder="Cualquier aclaración o hallazgo importante..."></textarea>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Firmas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Firma Supervisor -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-gray-700">Firma del Supervisor <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.live="supervisorName" placeholder="Nombre completo del supervisor" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm">
                                @error('supervisorName') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                
                                <div x-data="signaturePad(@entangle('supervisorSignature'))" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden aspect-[4/3] flex flex-col items-center justify-center group">
                                    <template x-if="!signature">
                                        <div class="text-center p-4">
                                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            <p class="text-xs text-gray-500">Haz clic para firmar</p>
                                        </div>
                                    </template>
                                    <canvas x-ref="canvas" class="absolute inset-0 w-full h-full cursor-crosshair"></canvas>
                                    <template x-if="signature">
                                        <button @click="clear()" class="absolute top-2 right-2 bg-red-100 text-red-600 p-1 rounded-full hover:bg-red-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                                @error('supervisorSignature') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Firma Técnico -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-gray-700">Firma del Técnico <span class="text-red-500">*</span></label>
                                <div class="bg-gray-100 rounded-lg p-2 text-xs text-gray-500 text-center font-medium">
                                    {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                                </div>
                                
                                <div x-data="signaturePad(@entangle('technicianSignature'))" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden aspect-[4/3] flex flex-col items-center justify-center group">
                                    <template x-if="!signature">
                                        <div class="text-center p-4">
                                            <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            <p class="text-xs text-gray-500">Firma del Profesional</p>
                                        </div>
                                    </template>
                                    <canvas x-ref="canvas" class="absolute inset-0 w-full h-full cursor-crosshair"></canvas>
                                    <template x-if="signature">
                                        <button @click="clear()" class="absolute top-2 right-2 bg-red-100 text-red-600 p-1 rounded-full hover:bg-red-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                                @error('technicianSignature') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="bg-gray-50 px-8 py-5 flex items-center justify-between border-t border-gray-200">
                <button type="button" wire:click="prevStep" 
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-all
                               {{ $currentStep === 0 ? 'invisible' : '' }}">
                    Atrás
                </button>
                
                @if($currentStep < $totalSteps - 1)
                    <button type="button" wire:click="nextStep" 
                            class="px-8 py-2.5 text-sm font-bold text-white bg-ep-primary rounded-lg shadow-lg hover:shadow-ep-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        {{ $currentStep === 0 ? 'Empezar' : 'Siguiente' }}
                    </button>
                @else
                    <button type="button" wire:click="complete" 
                            class="px-8 py-2.5 text-sm font-bold text-white bg-green-600 rounded-lg shadow-lg hover:shadow-green-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Finalizar Inspección
                    </button>
                @endif
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-400">Los cambios se guardan automáticamente al avanzar.</p>
        </div>
    </div>

    <!-- Script para firmas (usando Alpine.js y el API nativo de Canvas) -->
    @script
    <script>
        Alpine.data('signaturePad', (signatureValue) => ({
            signature: signatureValue,
            drawing: false,
            canvas: null,
            ctx: null,

            init() {
                this.canvas = this.$refs.canvas;
                this.ctx = this.canvas.getContext('2d');
                
                // Ajustar tamaño del canvas al contenedor real
                const resize = () => {
                    const rect = this.canvas.getBoundingClientRect();
                    this.canvas.width = rect.width;
                    this.canvas.height = rect.height;
                    // Restaurar si ya había algo
                    if (this.signature) {
                        const img = new Image();
                        img.onload = () => this.ctx.drawImage(img, 0, 0);
                        img.src = this.signature;
                    }
                };

                window.addEventListener('resize', resize);
                setTimeout(resize, 100);

                // Eventos de ratón
                this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                this.canvas.addEventListener('mouseleave', () => this.stopDrawing());

                // Eventos táctiles
                this.canvas.addEventListener('touchstart', (e) => this.startDrawing(e.touches[0]));
                this.canvas.addEventListener('touchmove', (e) => {
                    e.preventDefault();
                    this.draw(e.touches[0]);
                }, { passive: false });
                this.canvas.addEventListener('touchend', () => this.stopDrawing());
            },

            startDrawing(e) {
                this.drawing = true;
                const rect = this.canvas.getBoundingClientRect();
                this.ctx.beginPath();
                this.ctx.lineWidth = 2;
                this.ctx.lineCap = 'round';
                this.ctx.strokeStyle = '#1e293b';
                this.ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
            },

            draw(e) {
                if (!this.drawing) return;
                const rect = this.canvas.getBoundingClientRect();
                this.ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
                this.ctx.stroke();
            },

            stopDrawing() {
                if (this.drawing) {
                    this.drawing = false;
                    this.signature = this.canvas.toDataURL();
                }
            },

            clear() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.signature = null;
            }
        }))
    </script>
    @endscript

    <style>
        .cursor-crosshair { cursor: crosshair; }
    </style>
</div>
