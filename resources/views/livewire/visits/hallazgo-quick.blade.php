<div>
    @if($visit)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" aria-hidden="true" wire:click="$dispatch('closeHallazgoQuick')"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-ep-dark text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full rounded-3xl border border-white/10">
                    <!-- Sticky Header -->
                    <div class="sticky top-0 z-10 bg-white/5 backdrop-blur-xl border-b border-white/10 px-6 py-5 sm:px-8">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-ep-primary/10 rounded-2xl border border-ep-primary/20 text-ep-primary shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-white tracking-tight uppercase italic leading-none mb-1">
                                        Registrar <span class="text-ep-primary">Hallazgo</span>
                                    </h2>
                                    <p class="text-indigo-100/40 text-[10px] font-bold uppercase tracking-[0.2em] italic">Detección Técnica en Tiempo Real</p>
                                </div>
                            </div>
                            <button wire:click="$dispatch('closeHallazgoQuick')" class="text-indigo-100/40 hover:text-white hover:bg-white/5 rounded-xl p-2 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="save" class="px-6 py-8 sm:px-8 space-y-8 relative">
                        {{-- Loading Overlay --}}
                        <div wire:loading wire:target="save" class="absolute inset-0 z-50 bg-ep-dark/50 backdrop-blur-[2px] flex items-center justify-center rounded-b-3xl">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-10 h-10 border-4 border-ep-primary border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-[10px] font-black text-white uppercase tracking-widest italic">Registrando Hallazgo...</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Título -->
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2 italic px-1">Título del Hallazgo *</label>
                                <input type="text" wire:model="titulo" 
                                       class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-indigo-100/20"
                                       placeholder="Ej: Escalones oxidados en escalera principal">
                                @error('titulo') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2 italic px-1">Descripción Detallada *</label>
                                <textarea wire:model="descripcion" rows="3"
                                          class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-indigo-100/20"
                                          placeholder="Describa el hallazgo en detalle..."></textarea>
                                @error('descripcion') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Ubicación -->
                            <div>
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2 italic px-1">Ubicación Específica</label>
                                <input type="text" wire:model="ubicacion_especifica" 
                                       class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-indigo-100/20"
                                       placeholder="Ej: Sector Norte, Piso 2">
                                @error('ubicacion_especifica') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Plazo -->
                            <div>
                                <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-2 italic px-1">Plazo de Corrección *</label>
                                <input type="date" wire:model="plazo_correccion" 
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       class="block w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all [color-scheme:dark]">
                                @error('plazo_correccion') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-ep-primary uppercase tracking-widest mb-3 italic px-1">Nivel de Criticidad *</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach([
                                    'bajo' => ['bg' => 'bg-emerald-500', 'border' => 'peer-checked:border-emerald-500', 'light' => 'peer-checked:bg-emerald-500/10', 'label' => 'Bajo'],
                                    'medio' => ['bg' => 'bg-yellow-500', 'border' => 'peer-checked:border-yellow-500', 'light' => 'peer-checked:bg-yellow-500/10', 'label' => 'Medio'],
                                    'alto' => ['bg' => 'bg-orange-500', 'border' => 'peer-checked:border-orange-500', 'light' => 'peer-checked:bg-orange-500/10', 'label' => 'Alto'],
                                    'critico' => ['bg' => 'bg-red-500', 'border' => 'peer-checked:border-red-500', 'light' => 'peer-checked:bg-red-500/10', 'label' => 'Crítico']
                                ] as $value => $info)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" wire:model="criticidad" value="{{ $value }}" class="peer sr-only">
                                        <div class="p-4 bg-white/5 border border-white/10 rounded-2xl text-center transition-all group-hover:bg-white/10 {{ $info['border'] }} {{ $info['light'] }}">
                                            <div class="w-4 h-4 rounded-full {{ $info['bg'] }} mx-auto mb-3 border border-white/10 shadow-inner {{ $value === 'critico' ? 'animate-pulse shadow-[0_0_15px_rgba(239,68,68,0.5)]' : '' }}"></div>
                                            <div class="text-[9px] font-black uppercase tracking-widest text-indigo-100 group-hover:text-white transition-colors">{{ $info['label'] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('criticidad') <span class="text-red-400 text-[10px] mt-1 italic block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Evidencia Fotográfica Premium -->
                        <div class="bg-black/20 p-6 rounded-3xl border-2 border-dashed border-white/5 relative group hover:border-ep-primary/30 transition-all">
                            <label class="block text-[10px] font-black text-indigo-100/60 uppercase tracking-widest mb-4 italic text-center">Evidencia Fotográfica (Opcional)</label>
                            
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 cursor-pointer group/label">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <div class="p-3 bg-white/5 rounded-2xl mb-3 group-hover/label:scale-110 group-hover/label:bg-ep-primary/10 transition-all">
                                            <svg class="w-6 h-6 text-indigo-100/40 group-hover/label:text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </div>
                                        <p class="text-[10px] text-indigo-100/40 font-black uppercase tracking-widest">Toque para <span class="text-ep-primary">Subir Fotos</span></p>
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
                                    <div class="w-8 h-8 border-4 border-ep-primary border-t-transparent rounded-full animate-spin"></div>
                                    <span class="text-[10px] font-black text-white uppercase tracking-widest italic">Procesando Imágenes...</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-white/5">
                            <button type="button" wire:click="$dispatch('closeHallazgoQuick')" wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-8 py-3 bg-white/5 text-white text-[10px] font-black rounded-xl border border-white/10 hover:bg-white/10 transition-all uppercase tracking-widest italic disabled:opacity-50">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                    class="w-full sm:w-auto px-10 py-3 bg-ep-primary text-white text-[10px] font-black rounded-xl shadow-lg shadow-ep-primary/20 hover:brightness-110 transition-all uppercase tracking-widest italic group overflow-hidden relative disabled:opacity-50 disabled:cursor-not-allowed">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <span class="relative flex items-center justify-center gap-2" wire:loading.remove wire:target="save">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    Registrar Hallazgo
                                </span>
                                <span class="relative flex items-center justify-center gap-2" wire:loading wire:target="save">
                                    <div class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                    Guardando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
