<div class="min-h-screen bg-[#11111d] p-4 lg:p-8">
    {{-- Tactical Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] mb-3">
                <a href="{{ route('admin.consultants') }}" class="hover:text-ep-primary transition-colors">Sistema Global</a>
                <span>/</span>
                <span class="text-ep-primary">Configuración de Nodo</span>
            </div>
            <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                Editar <span class="text-ep-primary shadow-[0_0_20px_#4338ca/30]">Consultora</span>
            </h2>
            <p class="text-white/40 mt-2 font-medium tracking-wide max-w-lg">Actualice los parámetros de identidad, límites operativos y credenciales institucionales del nodo administrativo.</p>
        </div>
        <a href="{{ route('admin.consultants') }}" class="group flex items-center px-6 py-3 bg-white/5 backdrop-blur-md border border-white/10 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-white/10 transition-all shadow-xl">
            <svg class="w-5 h-5 mr-2 text-ep-primary group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Volver al Panel
        </a>
    </div>

    <form wire:submit.prevent="save" class="max-w-6xl mx-auto space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Columna Izquierda: Identidad y Branding --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Bloque: Información Corporativa --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-ep-primary/5 rounded-full blur-3xl group-hover:bg-ep-primary/10 transition-colors"></div>
                    
                    <h4 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-8 flex items-center">
                        <span class="w-8 h-[1px] bg-ep-primary mr-3"></span>
                        Identidad del Nodo
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Denominación Legal / Comercial</label>
                            <input type="text" wire:model="nombre" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white placeholder-white/10 focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-medium text-lg shadow-inner"
                                   placeholder="Ej: XamaSafe Consultoría Integral">
                            @error('nombre') <span class="text-red-400 text-[10px] font-bold mt-2 block uppercase tracking-wider italic">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Identificación Fiscal (CUIT)</label>
                            <input type="text" wire:model="cuit" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-mono tracking-widest shadow-inner">
                            @error('cuit') <span class="text-red-400 text-[10px] font-bold mt-2 block uppercase tracking-wider italic">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Capacidad de Operadores (Licencias)</label>
                            <div class="relative">
                                <input type="number" wire:model="max_tecnicos" 
                                       class="w-full bg-black/40 border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-black text-xl shadow-inner">
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-ep-primary uppercase tracking-widest">Slots</div>
                            </div>
                            @error('max_tecnicos') <span class="text-red-400 text-[10px] font-bold mt-2 block uppercase tracking-wider italic">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Bloque: Imagen Visual (Logo) --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                    <h4 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-8 flex items-center">
                        <span class="w-8 h-[1px] bg-ep-primary mr-3"></span>
                        Proyección de Marca
                    </h4>

                    <div class="flex flex-col md:flex-row items-center gap-10">
                        <div class="relative group/logo">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-ep-primary/20 to-transparent rounded-[2rem] blur-xl opacity-0 group-hover/logo:opacity-100 transition-opacity"></div>
                            <div class="relative w-40 h-40 bg-black/40 border border-white/10 rounded-[2rem] flex items-center justify-center p-4 backdrop-blur-md overflow-hidden shadow-2xl">
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" class="max-h-full w-auto object-contain z-10">
                                @elseif($existingLogo)
                                    <img src="{{ asset('storage/' . $existingLogo) }}?v={{ time() }}" class="max-h-full w-auto object-contain z-10">
                                @else
                                    <svg class="w-16 h-16 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover/logo:opacity-100 transition-opacity flex items-end justify-center pb-4 text-[8px] font-black text-white tracking-widest uppercase">Preview</div>
                            </div>
                        </div>

                        <div class="flex-1 w-full space-y-4">
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest italic">Carga de Identificador Gráfico (Logo)</label>
                            <label class="relative block group/input cursor-pointer">
                                <div class="w-full bg-black/40 border-2 border-dashed border-white/10 rounded-2xl py-8 px-6 flex flex-col items-center justify-center hover:border-ep-primary/40 transition-all">
                                    <svg class="w-8 h-8 text-white/20 mb-3 group-hover/input:text-ep-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span class="text-xs font-bold text-white/60 group-hover/input:text-white transition-colors">Click para subir o arrastra el archivo</span>
                                    <span class="text-[9px] text-white/20 mt-2 uppercase tracking-widest">PNG, JPG, SVG • Máx 2MB</span>
                                </div>
                                <input type="file" wire:model="logo" class="hidden" accept="image/*">
                            </label>
                            @error('logo') <span class="text-red-400 text-[10px] font-bold mt-2 block uppercase tracking-wider italic">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Configuración Táctica --}}
            <div class="space-y-8">
                {{-- Bloque: Colores Corporativos --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl h-full relative overflow-hidden group">
                    <div class="absolute -top-10 -left-10 w-32 h-32 bg-yellow-400/5 rounded-full blur-2xl"></div>
                    
                    <h4 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-8 flex items-center">
                        <span class="w-8 h-[1px] bg-ep-primary mr-3"></span>
                        Cromática Dinámica
                    </h4>

                    <div class="space-y-6">
                        {{-- Color Items --}}
                        @php $colorSlots = [
                            ['key' => 'primary', 'field' => 'color_primary', 'label' => 'Primario / Glow'],
                            ['key' => 'secondary', 'field' => 'color_secondary', 'label' => 'Subordinado'],
                            ['key' => 'accent', 'field' => 'color_accent', 'label' => 'Alerta / Acento']
                        ]; @endphp

                        @foreach($colorSlots as $slot)
                        <div class="space-y-3">
                            <label class="block text-[9px] font-black text-white/40 uppercase tracking-widest italic">{{ $slot['label'] }}</label>
                            <div class="relative flex items-center group/color">
                                <div class="absolute left-4 w-6 h-6 rounded-lg pointer-events-none shadow-[0_0_10px_rgba(0,0,0,0.5)] border border-white/20" 
                                     id="preview-{{ $slot['key'] }}" 
                                     style="background-color: <?php echo ${$slot['field']}; ?>"></div>
                                <input type="text" id="hex-{{ $slot['key'] }}" data-coloris data-wire-field="{{ $slot['field'] }}" wire:model.lazy="{{ $slot['field'] }}"
                                       class="w-full bg-black/40 border-white/10 rounded-2xl text-white pl-14 py-4 px-6 font-mono text-sm tracking-widest focus:ring-ep-primary/30 shadow-inner">
                            </div>
                            @error($slot['field']) <span class="text-red-400 text-[9px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-10 p-4 bg-yellow-400/10 border border-yellow-400/20 rounded-2xl">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-[9px] font-medium text-yellow-400/80 leading-relaxed uppercase tracking-wider italic">
                                Estas tonalidades definirán la identidad visual del entorno Gestor y la paleta de sus reportes inteligentes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bloque Inferior: Localización e Institucional --}}
            <div class="lg:col-span-3">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                    <h4 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-8 flex items-center">
                        <span class="w-8 h-[1px] bg-ep-primary mr-3"></span>
                        Coordenadas y Geopolítica Operativa
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Domicilio de Operaciones / Legal</label>
                            <input type="text" wire:model="fiscal_address" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-medium shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Jurisdicción / Región</label>
                            <input type="text" wire:model="province" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-medium shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Localidad / Distrito</label>
                            <input type="text" wire:model="locality" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-medium shadow-inner">
                        </div>
                        
                        {{-- Contacto --}}
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Enlace de Comunicación (Email)</label>
                            <input type="email" wire:model="email" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-medium shadow-inner">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-white/40 uppercase tracking-widest mb-3 italic">Frecuencia de Radio / Teléfono</label>
                            <input type="text" wire:model="phone" 
                                   class="w-full bg-black/40 border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-ep-primary/30 focus:border-ep-primary py-4 px-6 transition-all font-medium shadow-inner">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Footer de Acciones --}}
        <div class="sticky bottom-8 z-30 flex items-center justify-between p-4 bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-[0_-20px_40px_rgba(0,0,0,0.4)]">
            <a href="{{ route('admin.consultants') }}" class="px-8 py-4 text-xs font-black text-white/40 hover:text-white uppercase tracking-[0.3em] transition-colors italic">
                Abortar Operación
            </a>
            <button type="submit" 
                    class="group relative inline-flex items-center px-12 py-5 bg-ep-primary text-white text-sm font-black uppercase italic tracking-[0.2em] rounded-2xl shadow-[0_0_30px_rgba(67,56,202,0.5)] active:scale-95 transition-all overflow-hidden hover:brightness-110">
                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></span>
                <svg class="w-5 h-5 mr-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Inyectar Cambios
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Coloris === 'undefined') return;

    Coloris({
        el: '[data-coloris]',
        theme: 'pill',
        themeMode: 'dark',
        formatToggle: false,
        clearButton: false,
        closeButton: true,
        closeLabel: 'Confirmar',
        alpha: false,
        swatches: [
            '#4338ca','#F69239','#10B981','#EF4444','#3B82F6','#8B5CF6','#111827','#ffffff',
        ],
    });

    document.addEventListener('coloris:pick', function (event) {
        const input = event.detail.currentEl;
        const hex = event.detail.color;
        const key = input.id.replace('hex-', '');
        const wireField = input.dataset.wireField;
        const preview = document.getElementById('preview-' + key);
        if (preview) preview.style.backgroundColor = hex;
        input.dispatchEvent(new Event('change', { bubbles: true }));
        if (wireField && window.Livewire) {
            const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
            if (componentId) Livewire.find(componentId)?.set(wireField, hex);
        }
    });
});
</script>
