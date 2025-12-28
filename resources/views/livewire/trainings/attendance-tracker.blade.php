<div class="py-6 min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $training->title }}</h1>
            <div class="mt-2 flex flex-col items-center gap-1 text-sm text-gray-500">
                <span class="font-medium text-ep-primary">{{ $training->empresa->nombre }}</span>
                <span>{{ $training->sede->nombre_sede }} • {{ $training->date->format('d/m/Y') }}</span>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Formulario de Registro -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8" x-data="signaturePad()">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Registrar Asistencia
            </h3>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nombre y Apellido</label>
                    <input type="text" wire:model="newAttendee.employee_name" 
                           class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-4 transition-all"
                           placeholder="Escriba su nombre completo">
                    @error('newAttendee.employee_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">DNI / Documento</label>
                    <input type="text" wire:model="newAttendee.dni" 
                           class="block w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-4 transition-all"
                           placeholder="Número de documento">
                    @error('newAttendee.dni') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Canvas de Firma -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Firma Digital</label>
                    <div class="relative bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl overflow-hidden touch-none h-48">
                        <canvas x-ref="canvas" class="w-full h-full cursor-crosshair"></canvas>
                        <button type="button" @click="clear()" 
                                class="absolute top-3 right-3 p-2 bg-white/80 backdrop-blur shadow-sm rounded-lg text-gray-500 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @error('newAttendee.signature') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <button type="button" @click="submitAttendance()" 
                        class="w-full py-4 bg-ep-primary text-white rounded-2xl shadow-xl shadow-ep-primary/20 hover:bg-ep-primary-dark transition-all font-bold text-lg">
                    Confirmar mi Asistencia
                </button>
            </div>
        </div>

        <!-- Lista de Asistentes Recientes -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Asistentes Registrados</h3>
                <span class="bg-ep-primary/10 text-ep-primary text-xs font-bold px-2 py-1 rounded-full">{{ count($attendees) }}</span>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse($attendees as $att)
                    <li class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $att->employee_name }}</p>
                                <p class="text-xs text-gray-500">DNI: {{ $att->dni }} • {{ $att->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if($att->signature)
                            <img src="{{ $att->signature }}" alt="Firma" class="h-8 max-w-[80px] border rounded p-0.5 bg-gray-50">
                        @endif
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-gray-400 italic text-sm">
                        Aún no hay asistentes registrados.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('signaturePad', () => ({
                canvas: null,
                ctx: null,
                isDrawing: false,
                lastX: 0,
                lastY: 0,

                init() {
                    this.canvas = this.$refs.canvas;
                    this.ctx = this.canvas.getContext('2d');
                    
                    const resize = () => {
                        const rect = this.canvas.getBoundingClientRect();
                        this.canvas.width = rect.width;
                        this.canvas.height = rect.height;
                        this.ctx.strokeStyle = '#2B6CB0';
                        this.ctx.lineWidth = 3;
                        this.ctx.lineCap = 'round';
                        this.ctx.lineJoin = 'round';
                    };

                    setTimeout(resize, 200);
                    window.addEventListener('resize', resize);

                    this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                    this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                    this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                    
                    this.canvas.addEventListener('touchstart', (e) => {
                        const touch = e.touches[0];
                        const rect = this.canvas.getBoundingClientRect();
                        this.startDrawing({ offsetX: touch.clientX - rect.left, offsetY: touch.clientY - rect.top });
                        e.preventDefault();
                    }, { passive: false });

                    this.canvas.addEventListener('touchmove', (e) => {
                        const touch = e.touches[0];
                        const rect = this.canvas.getBoundingClientRect();
                        this.draw({ offsetX: touch.clientX - rect.left, offsetY: touch.clientY - rect.top });
                        e.preventDefault();
                    }, { passive: false });

                    this.canvas.addEventListener('touchend', () => this.stopDrawing());

                    this.$watch('$wire.attendees', () => {
                        this.clear();
                    });
                    
                    window.addEventListener('attendance-added', () => this.clear());
                },

                startDrawing(e) {
                    this.isDrawing = true;
                    [this.lastX, this.lastY] = [e.offsetX, e.offsetY];
                },

                draw(e) {
                    if (!this.isDrawing) return;
                    this.ctx.beginPath();
                    this.ctx.moveTo(this.lastX, this.lastY);
                    this.ctx.lineTo(e.offsetX, e.offsetY);
                    this.ctx.stroke();
                    [this.lastX, this.lastY] = [e.offsetX, e.offsetY];
                },

                stopDrawing() {
                    this.isDrawing = false;
                },

                clear() {
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                },

                submitAttendance() {
                    const signatureData = this.canvas.toDataURL('image/png');
                    this.$wire.set('newAttendee.signature', signatureData);
                    this.$wire.addAttendance();
                }
            }));
        });
    </script>
</div>
