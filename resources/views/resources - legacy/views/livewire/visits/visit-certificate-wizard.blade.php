<div class="py-6 min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-ep-primary bg-ep-primary/10">
                    Paso {{ $currentStep }} de {{ $totalSteps }}
                </span>
                <span class="text-xs font-semibold inline-block text-ep-primary">
                    {{ round(($currentStep / $totalSteps) * 100) }}%
                </span>
            </div>
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                <div style="width:{{ ($currentStep / $totalSteps) * 100 }}%" 
                     class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-ep-primary transition-all duration-500"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Step 1: Sede y Tipo de Visita -->
            @if($currentStep === 1)
                <div class="p-8">
                    <div class="text-center mb-8">
                        <div class="mx-auto h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Nueva Constancia de Visita</h2>
                        <p class="mt-2 text-gray-500">Seleccione la sede y el tipo de visita realizada</p>
                    </div>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Sede <span class="text-red-500">*</span></label>
                                <select wire:model.live="sede_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm">
                                    <option value="">Seleccione una sede...</option>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->empresa->nombre }} - {{ $sede->nombre_sede }}</option>
                                    @endforeach
                                </select>
                                @error('sede_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha de Visita <span class="text-red-500">*</span></label>
                                <input type="date" wire:model.live="fecha_visita" max="{{ date('Y-m-d') }}"
                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm">
                                @error('fecha_visita') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[10px] text-gray-400">Podrás elegir actividades de los últimos 7 días desde esta fecha.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Visita <span class="text-red-500">*</span></label>
                            <select wire:model.live="tipo_visita" 
                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm">
                                <option value="">Seleccione el tipo de visita...</option>
                                <option value="Inspección de Seguridad">Inspección de Seguridad</option>
                                <option value="Capacitación">Capacitación</option>
                                <option value="Auditoría">Auditoría</option>
                                <option value="Entrega de EPP">Entrega de EPP</option>
                                <option value="Evaluación de Riesgos">Evaluación de Riesgos</option>
                                <option value="Asesoramiento Técnico">Asesoramiento Técnico</option>
                                <option value="Seguimiento">Seguimiento</option>
                                <option value="Verificación de Cumplimiento">Verificación de Cumplimiento</option>
                                <option value="Otro">Otro</option>
                            </select>
                            @error('tipo_visita') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Observaciones -->
            @if($currentStep === 2)
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Observaciones de la Visita</h2>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Observaciones <span class="text-red-500">*</span></label>
                        <textarea wire:model.live="observacion" rows="8" 
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" 
                                  placeholder="Describa los hallazgos, recomendaciones y detalles relevantes de la visita..."></textarea>
                        @error('observacion') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-gray-500">{{ strlen($observacion) }}/3000 caracteres</p>
                    </div>
                </div>
            @endif

            <!-- Step 3: Actividades (NUEVO) -->
            @if($currentStep === 3)
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Actividades Realizadas</h2>
                    <p class="text-sm text-gray-500 mb-6">Selecciona las actividades que realizaste durante esta visita (opcional)</p>
                    
                    @php
                        $activities = $this->getActivitiesForSede();
                    @endphp
                    
                    <!-- Inspecciones/Checklists -->
                    @if($activities['inspections']->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Inspecciones/Checklists
                            </h3>
                            <div class="space-y-2">
                                @foreach($activities['inspections'] as $inspection)
                                    <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model="selectedInspections" value="{{ $inspection->id }}" class="h-4 w-4 text-ep-primary focus:ring-ep-primary border-gray-300 rounded">
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $inspection->checklist->title }} - <span class="text-gray-500">{{ $inspection->completed_at->format('H:i') }}</span></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Capacitaciones -->
                    @if($activities['trainings']->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
                                Capacitaciones
                            </h3>
                            <div class="space-y-2">
                                @foreach($activities['trainings'] as $training)
                                    <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model="selectedTrainings" value="{{ $training->id }}" class="h-4 w-4 text-ep-primary focus:ring-ep-primary border-gray-300 rounded">
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $training->title }} - <span class="text-gray-500">{{ $training->date->format('H:i') }}</span></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    


                    <!-- Hallazgos Detectados -->
                    @if($activities['hallazgos']->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Hallazgos Detectados
                            </h3>
                            <div class="space-y-2">
                                @foreach($activities['hallazgos'] as $hallazgo)
                                    <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 cursor-pointer transition-colors">
                                        <input type="checkbox" wire:model="selectedHallazgos" value="{{ $hallazgo->id }}" class="h-4 w-4 text-ep-primary focus:ring-ep-primary border-gray-300 rounded">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-700">{{ $hallazgo->titulo }}</span>
                                            <div class="flex gap-2 mt-1">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $hallazgo->criticidad === 'critico' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                                    {{ ucfirst($hallazgo->criticidad) }}
                                                </span>
                                                <span class="text-[10px] text-gray-500">{{ $hallazgo->created_at->format('H:i') }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($activities['inspections']->isEmpty() && $activities['trainings']->isEmpty() &&
                        $activities['hallazgos']->isEmpty())
                        <div class="text-center p-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-gray-500 font-medium">No se encontraron actividades realizadas hoy en esta sede.</p>
                            <p class="text-xs text-gray-400 mt-2">Puedes continuar sin vincular actividades.</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Step 4: Firmas (antes era Step 3) -->
            @if($currentStep === 4)
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Firmas</h2>
                    
                    <div class="space-y-6">
                        <!-- Firma Técnico -->
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700">Firma del Técnico <span class="text-red-500">*</span></label>
                            <div class="bg-gray-100 rounded-lg p-2 text-xs text-gray-500 text-center font-medium">
                                {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                            </div>
                            
                            <div x-data="signaturePad(@entangle('firma_instructor'))" class="relative bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden aspect-[4/3] flex flex-col items-center justify-center group">
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
                            @error('firma_instructor') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <hr class="border-gray-200">

                        <!-- Datos del Responsable -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-blue-900 mb-1">Firma del Responsable</h4>
                                    <p class="text-xs text-blue-700">La firma del responsable se solicitará por email. Complete los datos opcionales si desea enviar el link de firma.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <input type="checkbox" wire:model.live="firma_no_disponible" id="signature_unavailable"
                                       class="h-4 w-4 text-ep-primary focus:ring-ep-primary border-gray-300 rounded">
                                <label for="signature_unavailable" class="text-sm font-semibold text-gray-700">No se puede obtener la firma del responsable en este momento</label>
                            </div>

                            <div x-show="$wire.firma_no_disponible" x-transition class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo de la Falta de Firma <span class="text-red-500">*</span></label>
                                    <textarea wire:model.live="justificacion_firma" rows="2" 
                                              class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" 
                                              placeholder="Ej: El responsable no se encontraba en la sede, personal ausente, etc."></textarea>
                                    @error('justificacion_firma') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div x-show="!$wire.firma_no_disponible" x-transition class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email del Responsable (Opcional)</label>
                                    <input type="email" wire:model.live="email_responsable" 
                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" 
                                           placeholder="responsable@empresa.com">
                                    @error('email_responsable') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Responsable (Opcional)</label>
                                    <input type="text" wire:model.live="aclaracion_responsable" 
                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ep-primary focus:ring-ep-primary sm:text-sm" 
                                           placeholder="Nombre completo del responsable">
                                    @error('aclaracion_responsable') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="bg-gray-50 px-8 py-5 flex items-center justify-between border-t border-gray-200">
                <button type="button" wire:click="prevStep" 
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-all
                               {{ $currentStep === 1 ? 'invisible' : '' }}">
                    Atrás
                </button>
                
                @if($currentStep < $totalSteps)
                    <button type="button" wire:click="nextStep" 
                            class="px-8 py-2.5 text-sm font-bold text-white bg-ep-primary rounded-lg shadow-lg hover:shadow-ep-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Siguiente
                    </button>
                @else
                    <button type="button" wire:click="complete" 
                            class="px-8 py-2.5 text-sm font-bold text-white bg-green-600 rounded-lg shadow-lg hover:shadow-green-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Crear Constancia
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Script para firmas -->
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
                
                const resize = () => {
                    const rect = this.canvas.getBoundingClientRect();
                    this.canvas.width = rect.width;
                    this.canvas.height = rect.height;
                    if (this.signature) {
                        const img = new Image();
                        img.onload = () => this.ctx.drawImage(img, 0, 0);
                        img.src = this.signature;
                    }
                };

                window.addEventListener('resize', resize);
                setTimeout(resize, 100);

                this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                this.canvas.addEventListener('mouseleave', () => this.stopDrawing());

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
