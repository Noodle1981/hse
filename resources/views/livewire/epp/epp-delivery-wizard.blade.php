<div class="py-6 min-h-[calc(100vh-10rem)]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Stepper -->
        <div class="mb-12">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-ep-primary transition-all duration-500 -z-10" 
                     style="width: {{ ($step - 1) * 50 }}%"></div>
                
                @foreach(['Seleccionar Personal', 'Elementos de Protección', 'Firma y Confirmación'] as $i => $label)
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300
                                    {{ $step > $i + 1 ? 'bg-ep-primary text-white scale-110 shadow-lg' : ($step == $i + 1 ? 'bg-white border-4 border-ep-primary text-ep-primary scale-125 shadow-xl' : 'bg-white border-2 border-gray-300 text-gray-400') }}">
                            @if($step > $i + 1)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="mt-3 text-[10px] font-bold uppercase tracking-widest {{ $step == $i + 1 ? 'text-ep-primary' : 'text-gray-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <!-- Paso 1: Selección de Empleado -->
            @if($step == 1)
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">¿A quién se le entrega el equipo?</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Empresa</label>
                            <select wire:model.live="empresa_id" class="w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary p-3 text-sm">
                                <option value="">Seleccione Empresa...</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sede / Obra</label>
                            <select wire:model.live="sede_id" {{ !$empresa_id ? 'disabled' : '' }} class="w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary p-3 text-sm">
                                <option value="">Seleccione Sede...</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($sede_id)
                        <div class="relative mb-6">
                            <input type="text" wire:model.live="searchEmployee" placeholder="Buscar por nombre..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-ep-primary focus:bg-white transition-all">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <div class="grid grid-cols-1 gap-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                            @forelse($employees as $emp)
                                <button wire:click="selectEmployee({{ $emp->id }})" class="flex items-center p-4 bg-gray-50 rounded-2xl hover:bg-ep-primary/5 hover:border-ep-primary border-2 border-transparent transition-all group">
                                    <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center text-ep-primary font-bold mr-4 group-hover:bg-ep-primary group-hover:text-white transition-colors">
                                        {{ substr($emp->name, 0, 1) }}{{ substr($emp->last_name, 0, 1) }}
                                    </div>
                                    <div class="text-left">
                                        <div class="font-bold text-gray-900">{{ $emp->full_name }}</div>
                                        <div class="text-xs text-gray-500">DNI: {{ $emp->dni }} • {{ $emp->puesto }}</div>
                                    </div>
                                </button>
                            @empty
                                <div class="text-center py-12 text-gray-400">No se encontraron empleados en esta sede.</div>
                            @endforelse
                        </div>
                    @else
                        <div class="text-center py-20">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <p class="text-gray-500">Primero seleccione una empresa y sede.</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Paso 2: Selección de Items -->
            @if($step == 2)
                <div class="flex flex-col h-[70vh]">
                    <div class="p-8 border-b border-gray-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Elegir Equipamiento</h2>
                                <p class="text-sm text-gray-500">Entregando a: <span class="font-bold text-ep-primary">{{ $selectedEmployee->full_name }}</span></p>
                            </div>
                            <div class="bg-ep-primary/10 text-ep-primary px-4 py-2 rounded-xl text-xs font-bold">
                                {{ count($selectedItems) }} seleccionados
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($eppItems->groupBy('tipo') as $tipo => $items)
                                <div class="col-span-full mt-4 first:mt-0">
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">{{ $tipo }}</h3>
                                </div>
                                @foreach($items as $item)
                                    <div wire:click="toggleItem({{ $item->id }})" 
                                         class="relative p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between
                                                {{ isset($selectedItems[$item->id]) ? 'border-ep-primary bg-ep-primary/5' : 'border-gray-100 bg-white hover:border-gray-300' }}">
                                        @if(isset($selectedItems[$item->id]))
                                            <div class="absolute -top-3 -right-3 w-7 h-7 bg-ep-primary text-white rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <div class="font-bold text-gray-900 mb-1 leading-tight">{{ $item->nombre }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $item->codigo_norma ?: 'Sin norma' }}</div>
                                        </div>

                                        @if(isset($selectedItems[$item->id]))
                                            <div class="mt-4 pt-4 border-t border-ep-primary/20 grid grid-cols-2 gap-3" @click.stop>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-ep-primary uppercase mb-1">Cant.</label>
                                                    <input type="number" wire:model="selectedItems.{{ $item->id }}.cantidad" class="w-full bg-white border-0 rounded-lg text-xs p-2 focus:ring-ep-primary">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] font-bold text-ep-primary uppercase mb-1">Motivo</label>
                                                    <select wire:model="selectedItems.{{ $item->id }}.tipo_entrega" class="w-full bg-white border-0 rounded-lg text-xs p-2 focus:ring-ep-primary">
                                                        <option value="inicial">Inicial</option>
                                                        <option value="reposicion_rotura">Rotura</option>
                                                        <option value="reposicion_fecha">Vencimiento</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-between">
                        <button wire:click="$set('step', 1)" class="px-8 py-3 bg-white border border-gray-200 rounded-xl font-bold text-gray-600 hover:bg-gray-100 transition-all">
                            Atrás
                        </button>
                        <button wire:click="$set('step', 3)" 
                                {{ empty($selectedItems) ? 'disabled' : '' }}
                                class="px-10 py-3 bg-ep-primary text-white rounded-xl font-bold shadow-lg shadow-ep-primary/30 hover:bg-ep-primary-dark transition-all disabled:opacity-50">
                            Continuar a Firma
                        </button>
                    </div>
                </div>
            @endif

            <!-- Paso 3: Firma y Observaciones -->
            @if($step == 3)
                <div class="p-8" x-data="signaturePad()">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Finalizar Entrega</h2>
                    <p class="text-sm text-gray-500 mb-8">El empleado debe firmar la recepción de los elementos abajo.</p>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 text-center lg:text-left">Firma del Trabajador</label>
                            <div class="relative bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl overflow-hidden p-1 touch-none">
                                <canvas x-ref="canvas" class="w-full h-64 cursor-crosshair"></canvas>
                                <button type="button" @click="clear" class="absolute top-4 right-4 bg-white/80 backdrop-blur p-2 rounded-lg text-gray-400 hover:text-red-500 transition-colors shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                            @error('signature') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Observaciones Adicionales</label>
                            <textarea wire:model="observaciones" class="flex-1 w-full border-gray-200 rounded-3xl bg-gray-50 focus:bg-white focus:ring-ep-primary p-6 text-sm resize-none" placeholder="Escriba aquí cualquier aclaración sobre la entrega..."></textarea>
                            
                            <div class="mt-6 p-4 bg-yellow-50 rounded-2xl border border-yellow-100 text-[10px] text-yellow-700 leading-relaxed font-bold">
                                Al confirmar, se genera la constancia legal con validez jurídica. Verifique que los elementos y la cantidad sean correctos.
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 -mx-8 -mb-8 p-8 border-t border-gray-100 flex justify-between items-center mt-auto">
                        <button wire:click="$set('step', 2)" class="px-8 py-3 bg-white border border-gray-200 rounded-xl font-bold text-gray-600 hover:bg-gray-100 transition-all">
                            Atrás
                        </button>
                        <button @click="save" class="px-12 py-4 bg-emerald-600 text-white rounded-2xl font-bold shadow-xl shadow-emerald-500/20 hover:bg-emerald-700 transition-all flex items-center">
                            <span wire:loading.remove>Confirmar Entrega</span>
                            <span wire:loading class="animate-pulse">Guardando...</span>
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @script
    <script>
        Alpine.data('signaturePad', () => ({
            canvas: null,
            ctx: null,
            drawing: false,
            lastX: 0,
            lastY: 0,

            init() {
                this.canvas = this.$refs.canvas;
                this.ctx = this.canvas.getContext('2d');
                this.resize();
                
                // Events
                this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                this.canvas.addEventListener('mouseleave', () => this.stopDrawing());

                this.canvas.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    this.startDrawing(e.touches[0]);
                });
                this.canvas.addEventListener('touchmove', (e) => {
                    e.preventDefault();
                    this.draw(e.touches[0]);
                });
                this.canvas.addEventListener('touchend', () => this.stopDrawing());
            },

            resize() {
                const rect = this.canvas.getBoundingClientRect();
                this.canvas.width = rect.width;
                this.canvas.height = rect.height;
                this.ctx.lineWidth = 3;
                this.ctx.lineCap = 'round';
                this.ctx.strokeStyle = '#1e293b';
            },

            startDrawing(e) {
                this.drawing = true;
                const rect = this.canvas.getBoundingClientRect();
                [this.lastX, this.lastY] = [e.clientX - rect.left, e.clientY - rect.top];
            },

            draw(e) {
                if (!this.drawing) return;
                const rect = this.canvas.getBoundingClientRect();
                const [x, y] = [e.clientX - rect.left, e.clientY - rect.top];
                
                this.ctx.beginPath();
                this.ctx.moveTo(this.lastX, this.lastY);
                this.ctx.lineTo(x, y);
                this.ctx.stroke();
                
                [this.lastX, this.lastY] = [x, y];
            },

            stopDrawing() {
                this.drawing = false;
            },

            clear() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            },

            save() {
                const signature = this.canvas.toDataURL('image/png');
                $wire.set('signature', signature);
                $wire.save();
            }
        }))
    </script>
    @endscript

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</div>
