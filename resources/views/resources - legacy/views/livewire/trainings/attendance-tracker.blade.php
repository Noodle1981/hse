<div class="min-h-screen bg-ep-dark text-white selection:bg-ep-primary/30 pb-20">
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg border-b border-white/5">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <a href="{{ $visitId ? (Auth::user()->isAdmin() ? route('gestor.visits.in-progress', $visitId) : route('tecnico.visits.in-progress', $visitId)) : '#' }}" 
                       class="p-2 text-indigo-300 hover:text-white transition-colors bg-white/5 rounded-xl border border-white/5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <div class="flex flex-col">
                        <h1 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic mb-1 leading-none">Protocolo de Asistencia</h1>
                        <h2 class="text-xs font-bold text-white uppercase tracking-tight leading-none italic truncate max-w-[200px] sm:max-w-none">
                            {{ $training->title }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col items-end mr-3">
                        <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest italic">{{ $training->empresa->nombre }}</span>
                        <span class="text-[9px] font-bold text-white/40 uppercase tracking-tighter">{{ $training->sede->nombre_sede }}</span>
                    </div>
                    <div class="w-8 h-8 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg shadow-ep-primary/20">
                        {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
        
        @if (session()->has('message'))
            <div class="mb-8 p-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-[2rem] text-xs font-black uppercase tracking-widest italic flex items-center shadow-lg shadow-emerald-500/5">
                <div class="w-8 h-8 bg-emerald-500/20 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- COLUMNA IZQUIERDA: REGISTRO --}}
            <div class="lg:col-span-12 xl:col-span-7 space-y-8">
                
                {{-- CARD DE REGISTRO --}}
                <div class="bg-white/5 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 sm:p-14 shadow-2xl relative overflow-hidden group/card" x-data="signaturePad()">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-ep-primary/40 to-transparent"></div>
                    
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-14 h-14 rounded-[2rem] bg-ep-primary/10 border border-ep-primary/20 flex items-center justify-center text-ep-primary rotate-3 group-hover/card:rotate-0 transition-all duration-500">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white italic uppercase tracking-tight leading-none mb-2">Nuevo Ingreso</h3>
                            <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest italic leading-none">Matriz de Asistencia Individual</p>
                        </div>
                    </div>

                    <div class="space-y-12">
                        {{-- Field: Nombre --}}
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] italic z-10 transition-colors group-focus-within:text-white">Identidad del Asistente</span>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-ep-primary transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <input type="text" wire:model="newAttendee.employee_name" 
                                       class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-ep-primary/40 transition-all placeholder-white/10 italic"
                                       placeholder="NOMBRE COMPLETO...">
                            </div>
                            @error('newAttendee.employee_name') <span class="text-red-500 text-[9px] font-black uppercase italic mt-2 ml-2 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Field: DNI --}}
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10 transition-colors group-focus-within:text-white">Documentación Digital</span>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-indigo-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <input type="text" wire:model="newAttendee.dni" 
                                       class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/40 transition-all placeholder-white/10 italic"
                                       placeholder="NÚMERO DE DNI...">
                            </div>
                            @error('newAttendee.dni') <span class="text-red-500 text-[9px] font-black uppercase italic mt-2 ml-2 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Field: Signature --}}
                        <div class="relative">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Validación Biométrica (Firma)</span>
                            <div class="relative bg-black/60 border-2 border-dashed border-white/5 rounded-[2.5rem] overflow-hidden touch-none h-64 shadow-inner group/canvas transition-all hover:border-emerald-500/20">
                                <div class="absolute inset-0 pointer-events-none opacity-5">
                                    <div class="absolute inset-0" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                                </div>
                                <template x-if="!isDrawing && !hasSignature">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5 text-emerald-400/20 mb-3 group-hover/canvas:scale-110 transition-transform duration-500">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </div>
                                        <p class="text-[9px] font-black text-emerald-400/30 uppercase tracking-[0.2em] italic">Panel de Validación Táctica</p>
                                    </div>
                                </template>
                                <canvas x-ref="canvas" class="absolute inset-0 w-full h-full cursor-crosshair z-10"></canvas>
                                
                                <button type="button" @click="clear()" 
                                        class="absolute top-4 right-4 z-20 h-10 w-10 bg-black/40 backdrop-blur-md border border-white/10 rounded-xl text-white/40 hover:text-red-400 hover:bg-red-500/10 transition-all flex items-center justify-center active:scale-95 group/clear">
                                    <svg class="w-4 h-4 group-hover/clear:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                            @error('newAttendee.signature') <span class="text-red-500 text-[9px] font-black uppercase italic mt-2 ml-2 block">{{ $message }}</span> @enderror
                        </div>

                        <button type="button" @click="submitAttendance()" 
                                class="w-full h-20 bg-gradient-to-r from-ep-primary to-orange-600 text-white rounded-[2rem] shadow-xl shadow-ep-primary/20 hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-4 group/btn overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:animate-shimmer"></div>
                            <span class="text-lg font-black uppercase italic tracking-widest relative z-10">Confirmar mi Asistencia</span>
                            <div class="h-10 w-10 bg-white/10 rounded-2xl flex items-center justify-center relative z-10">
                                <svg class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- CARD OBSERVACIONES --}}
                <div class="bg-white/5 backdrop-blur-md rounded-[3rem] border border-white/5 p-8 sm:p-10 shadow-2xl relative overflow-hidden group/obs">
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-transparent via-emerald-500/40 to-transparent"></div>
                    
                    <h3 class="text-lg font-black text-white italic uppercase tracking-tight flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center text-ep-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        Bitácora Táctica
                    </h3>

                    <div class="space-y-8">
                        <div class="relative group">
                            <span class="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-300 uppercase tracking-[0.3em] italic z-10">Notas y Comentarios del Instructor</span>
                            <textarea wire:model="observations" rows="4"
                                      class="w-full bg-black/40 border border-white/10 rounded-[2rem] px-8 py-6 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/40 transition-all placeholder-white/10 italic"
                                      placeholder="DESARROLLO DE LA CAPACITACIÓN, TEMAS TRATADOS, MATERIALES..."></textarea>
                        </div>

                        <button wire:click="completeTraining" 
                                class="w-full h-16 bg-emerald-600/10 border border-emerald-500/20 text-emerald-400 rounded-2xl shadow-xl shadow-emerald-600/5 hover:bg-emerald-600 hover:text-white transition-all font-black text-xs uppercase tracking-[0.4em] italic flex items-center justify-center gap-4 group/final active:scale-95">
                            <svg class="w-5 h-5 group-hover/final:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Finalizar Capacitación
                        </button>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: ASISTENTES --}}
            <div class="lg:col-span-12 xl:col-span-5 space-y-8">
                <div class="bg-ep-dark/40 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl overflow-hidden flex flex-col h-fit sticky top-24 z-10">
                    <div class="px-8 py-6 bg-white/5 border-b border-white/5 flex justify-between items-center">
                        <div class="flex flex-col">
                            <h3 class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.3em] italic leading-none mb-1">Matriz de Personal</h3>
                            <p class="text-xs font-bold text-white uppercase tracking-tight leading-none italic">Asistentes Registrados</p>
                        </div>
                        <div class="h-9 min-w-[2.25rem] px-2 bg-ep-primary/10 border border-ep-primary/20 rounded-xl flex items-center justify-center">
                            <span class="text-xs font-black text-ep-primary">{{ count($attendees) }}</span>
                        </div>
                    </div>

                    <div class="p-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                        <div class="grid grid-cols-1 gap-4">
                            @forelse($attendees as $att)
                                <div class="bg-white/5 border border-white/5 rounded-3xl p-5 group/item hover:border-ep-primary/30 transition-all duration-500 relative overflow-hidden">
                                    <div class="absolute inset-y-0 left-0 w-1 bg-ep-primary opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                                    <div class="flex items-center gap-4 relative z-10">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 group-hover/item:rotate-6 transition-all duration-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-black text-white italic uppercase tracking-tight leading-loose mb-1">{{ $att->employee_name }}</p>
                                            <p class="text-[9px] font-bold text-white/30 uppercase tracking-widest italic flex items-center gap-2">
                                                <span class="text-indigo-400/50">DNI:</span> {{ $att->dni }} 
                                                <span class="text-white/10">•</span> 
                                                <span class="text-[8px]">{{ $att->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                        @if($att->signature)
                                            <div class="p-1.5 bg-white/10 rounded-xl border border-white/10 group-hover/item:bg-white/20 transition-all shrink-0">
                                                <img src="{{ $att->signature }}" alt="Firma" class="h-6 max-w-[60px]">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-16 text-center">
                                    <div class="w-16 h-16 bg-white/5 rounded-[2rem] border border-white/5 flex items-center justify-center mx-auto mb-6 text-white/10 italic">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-black text-white/20 uppercase tracking-[0.3em] italic mb-2">Matriz Vacía</h4>
                                    <p class="text-[10px] text-white/10 font-bold uppercase tracking-widest italic">A la espera de ingresos de personal</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        Alpine.data('signaturePad', () => ({
            canvas: null,
            ctx: null,
            isDrawing: false,
            hasSignature: false,
            lastX: 0,
            lastY: 0,

            init() {
                this.canvas = this.$refs.canvas;
                this.ctx = this.canvas.getContext('2d');
                
                const resize = () => {
                    const rect = this.canvas.getBoundingClientRect();
                    this.canvas.width = rect.width;
                    this.canvas.height = rect.height;
                    this.setupContext();
                };

                setTimeout(resize, 300);
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

                window.addEventListener('attendance-added', () => {
                    this.clear();
                    this.hasSignature = false;
                });
            },

            setupContext() {
                this.ctx.strokeStyle = '#FFFFFF'; // Blanco Puro
                this.ctx.lineWidth = 3.5;
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
                this.ctx.shadowBlur = 2;
                this.ctx.shadowColor = 'rgba(255, 255, 255, 0.4)';
            },

            startDrawing(e) {
                this.isDrawing = true;
                this.hasSignature = true;
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
                this.hasSignature = false;
            },

            submitAttendance() {
                if (!this.hasSignature) return;
                const signatureData = this.canvas.toDataURL('image/png');
                $wire.set('newAttendee.signature', signatureData);
                $wire.addAttendance();
            }
        }));
    </script>
    @endscript

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(246, 146, 57, 0.2); }
        .animate-fade-in { animation: fade-in 0.8s ease-out forwards; }
        @keyframes fade-in { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        .animate-shimmer { animation: shimmer 1.5s infinite; }
    </style>
</div>
