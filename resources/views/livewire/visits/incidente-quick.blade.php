<div>
    @if($visit)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" aria-hidden="true" wire:click="$dispatch('closeIncidenteQuick')"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-ep-dark text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full rounded-3xl border border-white/10">
                    <!-- Sticky Header -->
                    <div class="sticky top-0 z-10 bg-white/5 backdrop-blur-xl border-b border-red-500/20 px-6 py-5 sm:px-8">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-red-500/10 rounded-2xl border border-red-500/20 text-red-500 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-white tracking-tight uppercase italic leading-none mb-1">
                                        Reportar <span class="text-red-500">Incidente</span>
                                    </h2>
                                    <p class="text-red-100/40 text-[10px] font-bold uppercase tracking-[0.2em] italic">Protocolo de Respuesta Inmediata</p>
                                </div>
                            </div>
                            <button wire:click="$dispatch('closeIncidenteQuick')" class="text-indigo-100/40 hover:text-white hover:bg-white/5 rounded-xl p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="save" class="px-6 py-8 sm:px-8 space-y-8 relative">
                        {{-- Error Summary --}}
                        @if ($errors->any())
                            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-4 mb-6">
                                <div class="flex items-center gap-3 mb-2 text-red-500">
                                    <svg class="w-5 h-5 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest italic">Errores Detectados</span>
                                </div>
                                <ul class="list-disc list-inside text-xs text-red-100/60 italic space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Loading Overlay --}}
                        <div wire:loading wire:target="save" class="absolute inset-0 z-50 bg-ep-dark/50 backdrop-blur-[2px] flex items-center justify-center rounded-b-3xl">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-10 h-10 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-[10px] font-black text-white uppercase tracking-widest italic">Procesando Reporte...</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <!-- Título -->
                            <div>
                                <label class="block text-[10px] font-black text-red-500 uppercase tracking-widest mb-2 italic px-1">Título del Incidente *</label>
                                <input type="text" wire:model="titulo" 
                                       class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all placeholder-red-100/20"
                                       placeholder="Ej: Caída de trabajador desde andamio">
                                @error('titulo') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tipo de Incidente Premium -->
                            <div>
                                <label class="block text-[10px] font-black text-red-500 uppercase tracking-widest mb-3 italic px-1">Clasificación del Evento *</label>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                    @foreach(['accidente' => ['icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Accidente', 'color' => 'red'], 'casi_accidente' => ['icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'label' => 'Casi Accid.', 'color' => 'orange'], 'condicion_insegura' => ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Condición', 'color' => 'yellow'], 'acto_inseguro' => ['icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636', 'label' => 'Acto Ins.', 'color' => 'purple']] as $value => $info)
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" wire:model="tipo_incidente" value="{{ $value }}" class="peer sr-only">
                                            <div class="p-3 bg-white/5 border border-white/10 rounded-2xl text-center transition-all group-hover:bg-white/10 peer-checked:border-{{ $info['color'] }}-500 peer-checked:bg-{{ $info['color'] }}-500/10">
                                                <svg class="w-5 h-5 mx-auto mb-2 text-{{ $info['color'] }}-500 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}" /></svg>
                                                <div class="text-[9px] font-black uppercase tracking-widest text-indigo-100 transition-colors">{{ $info['label'] }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('tipo_incidente') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Personas Afectadas -->
                                <div>
                                    <label class="block text-[10px] font-black text-red-500 uppercase tracking-widest mb-2 italic px-1">Personas Afectadas *</label>
                                    <div class="relative">
                                        <input type="number" min="0" wire:model="personas_afectadas" 
                                               class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                                               placeholder="0">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <svg class="w-4 h-4 text-indigo-100/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        </div>
                                    </div>
                                    @error('personas_afectadas') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                                </div>

                                <!-- Ubicación -->
                                <div>
                                    <label class="block text-[10px] font-black text-red-500 uppercase tracking-widest mb-2 italic px-1">Ubicación Específica</label>
                                    <input type="text" wire:model="ubicacion_especifica" 
                                           class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all placeholder-red-100/20"
                                           placeholder="Ej: Área de producción, línea 3">
                                    @error('ubicacion_especifica') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label class="block text-[10px] font-black text-red-500 uppercase tracking-widest mb-2 italic px-1">Descripción Detallada *</label>
                                <textarea wire:model="descripcion" rows="3"
                                          class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all placeholder-red-100/20"
                                          placeholder="Relato de los hechos ocurrido..."></textarea>
                                @error('descripcion') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Medidas Tomadas -->
                            <div>
                                <label class="block text-[10px] font-black text-red-500 uppercase tracking-widest mb-2 italic px-1">Respuesta Inmediata *</label>
                                <textarea wire:model="medidas_tomadas" rows="2"
                                          class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all placeholder-red-100/20"
                                          placeholder="Acciones realizadas tras el evento..."></textarea>
                                @error('medidas_tomadas') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Gravedad Premium -->
                            <div>
                                <label class="block text-[10px] font-black text-red-500 uppercase tracking-widest mb-3 italic px-1">Nivel de Gravedad *</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @foreach(['leve' => ['color' => 'emerald', 'label' => 'Leve'], 'moderado' => ['color' => 'yellow', 'label' => 'Moderado'], 'grave' => ['color' => 'orange', 'label' => 'Grave'], 'muy_grave' => ['color' => 'red', 'label' => 'Crítico']] as $value => $info)
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" wire:model="gravedad" value="{{ $value }}" class="peer sr-only">
                                            <div class="p-3 bg-white/5 border border-white/10 rounded-2xl text-center transition-all group-hover:bg-white/10 peer-checked:border-{{ $info['color'] }}-500 peer-checked:bg-{{ $info['color'] }}-500/10">
                                                <div class="w-4 h-4 rounded-full bg-{{ $info['color'] }}-500 mx-auto mb-3 {{ $value === 'muy_grave' ? 'animate-pulse shadow-[0_0_15px_rgba(239,68,68,0.5)]' : '' }} border border-white/10 shadow-inner"></div>
                                                <div class="text-[9px] font-black uppercase tracking-widest text-indigo-100 group-hover:text-white transition-colors">{{ $info['label'] }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('gravedad') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Evidencia Fotográfica Premium -->
                            <div class="bg-black/20 p-6 rounded-3xl border-2 border-dashed border-white/5 relative group hover:border-red-500/30 transition-all">
                                <label class="block text-[10px] font-black text-indigo-100/60 uppercase tracking-widest mb-4 italic text-center">Registro Fotográfico del Incidente</label>
                                
                                <div class="flex items-center justify-center w-full focus-within:ring-2 focus-within:ring-red-500 rounded-lg">
                                    <label class="flex flex-col items-center justify-center w-full h-32 cursor-pointer group/label">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <div class="p-3 bg-white/5 rounded-2xl mb-3 group-hover/label:scale-110 group-hover/label:bg-red-500/10 transition-all">
                                                <svg class="w-6 h-6 text-indigo-100/40 group-hover/label:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            </div>
                                            <p class="text-[10px] text-indigo-100/40 font-black uppercase tracking-widest italic leading-none">Captura de <span class="text-red-500">Evidencia</span></p>
                                        </div>
                                        <input type="file" wire:model="photos" class="hidden" multiple accept="image/*" />
                                    </label>
                                </div>
                                
                                @if($photos)
                                    <div class="grid grid-cols-4 gap-3 mt-4">
                                        @foreach($photos as $index => $photo)
                                            <div class="relative group aspect-square rounded-xl overflow-hidden border border-white/10 shadow-lg">
                                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                                <button type="button" 
                                                        wire:click="$set('photos.{{$index}}', null)" 
                                                        class="absolute top-1 right-1 bg-red-600 text-white rounded-lg p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <div wire:loading wire:target="photos" class="absolute inset-0 bg-ep-dark/80 backdrop-blur-sm rounded-3xl flex items-center justify-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-8 h-8 border-4 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                                        <span class="text-[10px] font-black text-white uppercase tracking-widest italic">Procesando Evidencia...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-white/5">
                            <button type="button" wire:click="$dispatch('closeIncidenteQuick')" wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-8 py-3 bg-white/5 text-white text-[10px] font-black rounded-xl border border-white/10 hover:bg-white/10 transition-all uppercase tracking-widest italic disabled:opacity-50">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-10 py-3 bg-red-600 text-white text-[10px] font-black rounded-xl shadow-lg shadow-red-900/40 hover:bg-red-700 transition-all uppercase tracking-widest italic group overflow-hidden relative disabled:opacity-50 disabled:cursor-not-allowed">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <span class="relative flex items-center justify-center gap-2" wire:loading.remove wire:target="save">
                                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    Reportar Incidente
                                </span>
                                <span class="relative flex items-center justify-center gap-2" wire:loading wire:target="save">
                                    <div class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                    Enviando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
