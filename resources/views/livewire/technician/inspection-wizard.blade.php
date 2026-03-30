<div class="w-full mx-auto">
    {{-- Header de Inspección Premium --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Inspección <span class="text-white">Técnica</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 truncate max-w-[150px] sm:max-w-xs uppercase tracking-tight">
                            {{ $checklist->title }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none mb-1">Inspector</span>
                        <span class="text-xs font-black text-white italic uppercase">{{ auth()->user()->nombres }}</span>
                    </div>
                    <div class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
            </div>
            {{-- Efecto Shimmer en el Header --}}
            <div class="absolute bottom-0 left-0 h-0.5 w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="py-4 sm:py-8 min-h-screen bg-[#11111d] text-white selection:bg-ep-primary/30">
        <div class="max-w-5xl mx-auto px-1 sm:px-6 lg:px-8">

            {{-- Banner contextual Premium (Ahora con estilo de alerta técnica integrada) --}}
            @if($visitId)
                <div class="mb-8 relative overflow-hidden bg-ep-dark border-l-4 border-ep-primary p-5 rounded-r-3xl shadow-2xl group animate-fade-in-down">
                    <div class="absolute inset-0 bg-gradient-to-r from-ep-primary/5 via-transparent to-transparent opacity-50"></div>
                    <div class="relative flex items-center gap-5">
                        <div class="p-3 bg-ep-primary/10 rounded-2xl border border-ep-primary/20 text-ep-primary shadow-inner">
                            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-ep-primary uppercase tracking-[0.2em] italic mb-1">Consola de Visita Vinculada</h3>
                            <p class="text-xs font-bold text-indigo-100/60 leading-relaxed italic">
                                Sincronización activa con la visita de <span class="text-white">{{ $assignment->sede->nombre_sede ?? 'la sede' }}</span>.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Progress Bar Premium -->
            <div class="mb-10 px-2 mt-4">
                <div class="flex items-end justify-between mb-3 text-xs font-black uppercase tracking-widest italic">
                    <span class="text-indigo-100/40">Fase <span class="text-white">{{ $currentStep + 1 }}</span> de {{ $totalSteps }}</span>
                    <span class="text-ep-primary glow-text-orange">{{ round((($currentStep + 1) / $totalSteps) * 100) }}%</span>
                </div>
                <div class="relative h-1.5 w-full bg-white/5 rounded-full overflow-hidden border border-white/5 shadow-inner">
                    <div style="width:{{ (($currentStep + 1) / $totalSteps) * 100 }}%" 
                         class="absolute inset-y-0 left-0 bg-gradient-to-r from-ep-primary to-orange-500 shadow-[0_0_15px_rgba(246,146,57,0.5)] transition-all duration-700 ease-out rounded-full">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                </div>
            </div>

            <div class="bg-ep-dark rounded-3xl shadow-2xl border border-white/10 overflow-hidden relative group transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none opacity-30"></div>
                
                <!-- Step 0: Intro / Context Premium -->
                @if($currentStep === 0)
                    <div class="p-5 sm:p-12 relative">
                        <div class="text-center mb-10">
                            <div class="mx-auto h-20 w-20 bg-ep-primary/10 rounded-2xl border border-ep-primary/20 flex items-center justify-center mb-6 shadow-inner rotate-3 group-hover:rotate-0 transition-all duration-700">
                                <svg class="h-10 w-10 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-black text-white italic uppercase tracking-tight mb-3 leading-tight">{{ $checklist->title }}</h2>
                            <p class="text-sm font-medium text-indigo-100/40 italic uppercase tracking-wider">{{ $checklist->description }}</p>
                        </div>

                        <div class="space-y-6 bg-black/20 rounded-3xl p-8 border border-white/5 backdrop-blur-sm shadow-inner group/details relative overflow-hidden">
                            <h3 class="font-black text-ep-primary uppercase text-[10px] tracking-[0.3em] italic mb-6 flex items-center gap-2">
                                <span class="w-4 h-px bg-ep-primary/30"></span> 
                                Datos Técnicos del Recinto
                            </h3>
                            @if($assignment)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 relative z-10">
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-100/30 uppercase tracking-widest mb-1">Entidad</p>
                                        <p class="text-sm font-black text-white uppercase italic tracking-wide group-hover/details:text-ep-primary transition-colors">{{ $assignment->empresa->nombre }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-100/30 uppercase tracking-widest mb-1">Sede Técnica</p>
                                        <p class="text-sm font-black text-white uppercase italic tracking-wide">{{ $assignment->sede->nombre_sede }}</p>
                                    </div>
                                </div>
                            @endif
                            <div class="relative z-10 pt-4 border-t border-white/5 mt-4">
                                <p class="text-[9px] font-black text-indigo-100/30 uppercase tracking-widest mb-1">Firma del Especialista</p>
                                <p class="text-sm font-black text-indigo-100 italic">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Steps 1 to N: Sections Premium -->
                @foreach($sections as $index => $section)
                    @if($currentStep === ($index + 1))
                        <div class="p-5 sm:p-12 animate-fade-in">
                            <div class="mb-10 flex items-center gap-4">
                                <span class="text-2xl font-black text-ep-primary italic">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <div class="flex-1">
                                    <h2 class="text-2xl font-black text-white italic uppercase tracking-tight leading-tight">
                                        {{ $section['title'] }}
                                    </h2>
                                    @if($section['description'])
                                        <p class="text-xs font-medium text-indigo-100/40 italic uppercase tracking-[0.1em]">{{ $section['description'] }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-12">
                                @foreach($checklist->questions->where('section_id', $section['id'])->sortBy('order') as $question)
                                    <div class="space-y-5 relative p-8 bg-white/5 rounded-3xl border border-white/5 hover:border-ep-primary/20 transition-all duration-300">
                                        <label class="block text-sm font-black text-indigo-100 uppercase italic tracking-wide leading-snug">
                                            {{ $question->label }}
                                            @if($question->is_required) <span class="text-ep-primary ml-1 font-black animate-pulse">*</span> @endif
                                        </label>
                                        
                                        @if($question->help_text)
                                            <p class="text-[10px] text-indigo-100/30 italic font-bold uppercase tracking-widest mt-[-10px]">{{ $question->help_text }}</p>
                                        @endif

                                        @if($question->type === 'radio')
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                @foreach($question->options as $option)
                                                    <label class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer group/opt transition-all duration-300
                                                        {{ ($answers[$question->id] ?? '') === $option ? 'bg-ep-primary/20 border-ep-primary shadow-[0_0_15px_rgba(246,146,57,0.1)]' : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20' }}">
                                                        <input type="radio" wire:model.live="answers.{{ $question->id }}" value="{{ $option }}" class="sr-only">
                                                        
                                                        @if($option === 'Sí' || $option === 'CUMPLE' || $option === 'BUENO')
                                                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mb-2 {{ ($answers[$question->id] ?? '') === $option ? 'animate-pulse scale-125 shadow-[0_0_8px_#10b981]' : 'opacity-40' }} transition-all"></div>
                                                        @elseif($option === 'No' || $option === 'NO CUMPLE' || $option === 'MALO')
                                                            <div class="w-2.5 h-2.5 rounded-full bg-red-500 mb-2 {{ ($answers[$question->id] ?? '') === $option ? 'animate-pulse scale-125 shadow-[0_0_8px_#ef4444]' : 'opacity-40' }} transition-all"></div>
                                                        @else
                                                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-100/40 mb-2 {{ ($answers[$question->id] ?? '') === $option ? 'animate-pulse scale-125' : 'opacity-40' }} transition-all"></div>
                                                        @endif

                                                        <span class="text-[10px] font-black uppercase tracking-widest italic transition-colors {{ ($answers[$question->id] ?? '') === $option ? 'text-white' : 'text-indigo-100/40' }}">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($question->type === 'select')
                                            <select wire:model.live="answers.{{ $question->id }}" 
                                                    class="block w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all [color-scheme:dark]">
                                                <option value="">Seleccione una opción...</option>
                                                @foreach($question->options as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($question->type === 'checkbox')
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                @foreach($question->options as $option)
                                                    <label class="flex items-center space-x-4 p-5 rounded-2xl border border-white/5 bg-white/5 hover:bg-white/10 cursor-pointer transition-all">
                                                        <input type="checkbox" wire:model.live="answers.{{ $question->id }}" value="{{ $option }}" class="h-6 w-6 text-ep-primary focus:ring-ep-primary border-white/20 bg-black/40 rounded-xl">
                                                        <span class="text-xs font-black uppercase tracking-[0.2em] italic text-indigo-100">{{ $option }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($question->type === 'textarea')
                                            <textarea wire:model.live="answers.{{ $question->id }}" rows="3" 
                                                      class="block w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-white/10 italic" 
                                                      placeholder="Registro técnico detallado..."></textarea>
                                        @elseif($question->type === 'number')
                                            <input type="number" step="any" wire:model.live="answers.{{ $question->id }}" 
                                                   class="block w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all" 
                                                   placeholder="0.00">
                                        @elseif($question->type === 'date')
                                            <input type="date" wire:model.live="answers.{{ $question->id }}" 
                                                   class="block w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all [color-scheme:dark]">
                                        @else
                                            <input type="text" wire:model.live="answers.{{ $question->id }}" 
                                                   class="block w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-white/10" 
                                                   placeholder="Escriba su respuesta...">
                                        @endif

                                        @error("answers.{$question->id}")
                                            <div class="flex items-center gap-2 mt-2 text-red-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span class="text-[10px] font-bold uppercase italic tracking-widest">{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Step Final: Notes and Signatures Premium -->
                @if($currentStep === ($totalSteps - 1))
                    <div class="p-5 sm:p-12 animate-fade-in space-y-10">
                        <div class="text-center">
                            <h2 class="text-3xl font-black text-white italic uppercase tracking-tight mb-2">Protocolo de Cierre</h2>
                            <p class="text-xs font-medium text-indigo-100/40 italic uppercase tracking-[0.2em]">Validación de Registro e Integración</p>
                        </div>
                        
                        <div class="space-y-8">
                            <!-- Observaciones Generales -->
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic mb-2">Comentarios del Especialista</label>
                                <textarea wire:model.live="notes" rows="4" 
                                          class="block w-full bg-white/5 border border-white/10 rounded-3xl px-6 py-5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-white/5 italic" 
                                          placeholder="Aclaraciones técnicas o hallazgos no especificados en el checklist..."></textarea>
                            </div>

                            <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

                            <!-- Firmas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <!-- Firma Supervisor -->
                                <div class="space-y-5">
                                    <label class="block text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic">Captura Firma Supervisor <span class="text-white">*</span></label>
                                    <input type="text" wire:model.live="supervisorName" 
                                           class="block w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary transition-all placeholder-white/10 uppercase font-black tracking-widest text-center" 
                                           placeholder="NOMBRE COMPLETO">
                                    @error('supervisorName') <p class="text-[10px] text-red-400 font-bold italic uppercase tracking-widest">{{ $message }}</p> @enderror
                                    
                                    <div x-data="signaturePad(@entangle('supervisorSignature'))" class="relative bg-black/40 border-2 border-dashed border-white/10 rounded-3xl overflow-hidden aspect-[4/3] flex flex-col items-center justify-center group focus-within:border-ep-primary/50 transition-all">
                                        <template x-if="!signature">
                                            <div class="text-center p-6 group-hover:scale-110 transition-transform duration-500">
                                                <div class="p-4 bg-white/5 rounded-2xl border border-white/5 text-indigo-100/20 mb-3 mx-auto w-fit">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </div>
                                                <p class="text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic">Firmar sobre panel táctil</p>
                                            </div>
                                        </template>
                                        <canvas x-ref="canvas" class="absolute inset-0 w-full h-full cursor-crosshair z-10"></canvas>
                                        <template x-if="signature">
                                            <button @click="clear()" class="absolute top-4 right-4 z-20 bg-red-500/10 text-red-500 p-2 rounded-xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all shadow-xl">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                    @error('supervisorSignature') <p class="text-[10px] text-red-400 font-bold italic uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <!-- Firma Técnico -->
                                <div class="space-y-5">
                                    <label class="block text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic">Validación Técnico Especialista <span class="text-white">*</span></label>
                                    <div class="bg-ep-primary/10 border border-ep-primary/20 rounded-2xl p-4 text-[10px] text-white text-center font-black uppercase tracking-[0.2em] italic shadow-inner">
                                        {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
                                    </div>
                                    
                                    <div x-data="signaturePad(@entangle('technicianSignature'))" class="relative bg-black/40 border-2 border-dashed border-white/10 rounded-3xl overflow-hidden aspect-[4/3] flex flex-col items-center justify-center group focus-within:border-ep-primary/50 transition-all">
                                        <template x-if="!signature">
                                            <div class="text-center p-6 group-hover:scale-110 transition-transform duration-500">
                                                <div class="p-4 bg-white/5 rounded-2xl border border-white/5 text-indigo-100/20 mb-3 mx-auto w-fit">
                                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </div>
                                                <p class="text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic font-black">Panel de Firma Digital</p>
                                            </div>
                                        </template>
                                        <canvas x-ref="canvas" class="absolute inset-0 w-full h-full cursor-crosshair z-10"></canvas>
                                        <template x-if="signature">
                                            <button @click="clear()" class="absolute top-4 right-4 z-20 bg-red-500/10 text-red-500 p-2 rounded-xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all shadow-xl">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                    @error('technicianSignature') <p class="text-[10px] text-red-400 font-bold italic uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Navigation Buttons Premium -->
                <div class="bg-black/40 px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-5 border-t border-white/5 backdrop-blur-md">
                    <button type="button" wire:click="prevStep" 
                            class="w-full sm:w-auto px-10 py-3.5 text-[10px] font-black text-indigo-100 uppercase tracking-widest italic bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 transition-all
                                   {{ $currentStep === 0 ? 'invisible opacity-0' : 'opacity-100' }}">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Regresar Fase
                        </span>
                    </button>
                    
                    @if($currentStep < $totalSteps - 1)
                        <button type="button" wire:click="nextStep" 
                                class="w-full sm:w-auto px-12 py-4 text-[10px] font-black text-white bg-ep-primary rounded-2xl shadow-lg shadow-ep-primary/20 hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest italic group overflow-hidden relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            <span class="relative flex items-center justify-center gap-2">
                                {{ $currentStep === 0 ? 'Iniciar Protocolo' : 'Siguiente Fase' }}
                                <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </button>
                    @else
                        <button type="button" wire:click="complete" 
                                class="w-full sm:w-auto px-14 py-4 text-[10px] font-black text-white bg-emerald-600 rounded-2xl shadow-lg shadow-emerald-900/40 hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest italic group overflow-hidden relative">
                             <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                             <span class="relative flex items-center justify-center gap-2">
                                Finalizar Inspección Técnica
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                             </span>
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.3em] italic">Persistencia automática activada • XamaSafe Advanced Systems</p>
            </div>
        </div>

        <!-- Script para firmas Premium -->
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
                        // Restaurar con estilo premium
                        if (this.signature) {
                            const img = new Image();
                            img.onload = () => this.ctx.drawImage(img, 0, 0);
                            img.src = this.signature;
                        }
                    };

                    window.addEventListener('resize', resize);
                    setTimeout(resize, 100);

                    // Eventos
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
                    this.ctx.lineWidth = 3; // Un poco más intenso
                    this.ctx.lineCap = 'round';
                    this.ctx.strokeStyle = '#ffffff'; // Tinta blanca sobre fondo oscuro
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
            .glow-text-orange { text-shadow: 0 0 10px rgba(246,146,57,0.3); }
            .animate-shimmer {
                animation: shimmer 2.5s infinite linear;
            }
            @keyframes shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            @keyframes fade-in-down {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-down {
                animation: fade-in-down 0.6s ease-out forwards;
            }
            @keyframes fade-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .animate-fade-in {
                animation: fade-in 0.4s ease-in forwards;
            }
        </style>
    </div>
</div>
