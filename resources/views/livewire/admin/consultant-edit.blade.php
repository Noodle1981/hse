<div>
    {{-- Breadcrumb & Header --}}
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.consultants') }}" class="hover:text-ep-primary transition-colors">Mi Consultora</a>
            <span>/</span>
            <span class="text-gray-900 font-medium">Editar Datos</span>
        </div>
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Editar Consultora</h2>
                <p class="text-gray-500 mt-1">Actualice la información institucional corporativa.</p>
            </div>
            <a href="{{ route('admin.consultants') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>
        </div>
    </div>

    {{-- Formulario --}}
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <form wire:submit.prevent="save">
            <div class="p-8 space-y-8">
                
                {{-- Datos Principales --}}
                <div>
                    <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                        <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Datos Principales
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Nombre Comercial <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nombre" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all" placeholder="Ej: Safetify Soluciones">
                            @error('nombre') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">CUIT <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="cuit" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all">
                            @error('cuit') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Licencias (Técnicos) <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="max_tecnicos" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all">
                            @error('max_tecnicos') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Ubicación y Branding --}}
                <div>
                    <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                        <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Ubicación e Imagen Visual
                    </h4>
                    <div class="space-y-6">
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Logo Corporativo (Para Reportes PDF)</label>
                            <div class="flex items-center space-x-4">
                                <div class="shrink-0">
                                    @if ($logo)
                                        <img class="h-16 w-auto object-contain rounded-lg border border-gray-200 p-1" src="{{ $logo->temporaryUrl() }}" alt="Previsualización">
                                    @elseif($existingLogo)
                                        <img class="h-16 w-auto object-contain rounded-lg border border-gray-200 p-1 bg-white" src="{{ asset('storage/' . $existingLogo) }}?v={{ time() }}" alt="Logo Actual">
                                    @else
                                        <div class="h-16 w-16 rounded-lg border border-gray-200 flex items-center justify-center bg-gray-50 text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <label class="block w-full">
                                    <span class="sr-only">Elegir logo</span>
                                    <input type="file" wire:model="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2.5 file:px-4
                                        file:rounded-xl file:border-0
                                        file:text-sm file:font-bold
                                        file:bg-ep-primary/10 file:text-ep-primary
                                        hover:file:bg-ep-primary/20 transition-all cursor-pointer
                                    "/>
                                </label>
                            </div>
                            <p class="mt-2 text-xs text-gray-400">Se recomienda PNG o SVG con fondo transparente. Se aplicará en el encabezado de todos los PDFs corporativos.</p>
                            @error('logo') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Domicilio Fiscal</label>
                                <input type="text" wire:model="fiscal_address" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all">
                                @error('fiscal_address') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Provincia</label>
                                <input type="text" wire:model="province" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all">
                                @error('province') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Localidad</label>
                                <input type="text" wire:model="locality" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all">
                                @error('locality') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Colores Corporativos --}}
                <div>
                    <h4 class="text-base font-bold text-gray-900 mb-2 flex items-center border-b border-gray-100 pb-2">
                        <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        Colores Corporativos
                    </h4>
                    <p class="text-sm text-gray-500 mb-6">Haz clic en el campo para abrir el selector, o escribí directamente el valor HEX.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                        {{-- Primario --}}
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <label for="hex-primary" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Color Primario</label>
                            <input
                                type="text"
                                id="hex-primary"
                                data-coloris
                                data-wire-field="color_primary"
                                wire:model.lazy="color_primary"
                                maxlength="7"
                                class="w-full rounded-xl border-gray-200 text-base py-2.5 font-mono text-center shadow-sm focus:ring-2 focus:ring-ep-primary"
                                placeholder="#1e40af">
                            <div class="mt-4 h-8 rounded-xl shadow-inner border border-gray-200" id="preview-primary" style="background-color: {{ $color_primary }}"></div>
                            @error('color_primary') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Secundario --}}
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <label for="hex-secondary" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Color Secundario</label>
                            <input
                                type="text"
                                id="hex-secondary"
                                data-coloris
                                data-wire-field="color_secondary"
                                wire:model.lazy="color_secondary"
                                maxlength="7"
                                class="w-full rounded-xl border-gray-200 text-base py-2.5 font-mono text-center shadow-sm focus:ring-2 focus:ring-ep-primary"
                                placeholder="#6b7280">
                            <div class="mt-4 h-8 rounded-xl shadow-inner border border-gray-200" id="preview-secondary" style="background-color: {{ $color_secondary }}"></div>
                            @error('color_secondary') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Acento --}}
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <label for="hex-accent" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-4">Color de Acento</label>
                            <input
                                type="text"
                                id="hex-accent"
                                data-coloris
                                data-wire-field="color_accent"
                                wire:model.lazy="color_accent"
                                maxlength="7"
                                class="w-full rounded-xl border-gray-200 text-base py-2.5 font-mono text-center shadow-sm focus:ring-2 focus:ring-ep-primary"
                                placeholder="#16a34a">
                            <div class="mt-4 h-8 rounded-xl shadow-inner border border-gray-200" id="preview-accent" style="background-color: {{ $color_accent }}"></div>
                            @error('color_accent') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                {{-- Contacto y Extras --}}
                <div>
                    <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                        <svg class="w-5 h-5 mr-2 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Contacto e Información Adicional
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Email de Contacto</label>
                            <input type="email" wire:model="email" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Teléfono Institucional</label>
                            <input type="text" wire:model="phone" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all">
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Descripción General (Opcional)</label>
                            <textarea wire:model="description" rows="4" class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-ep-primary/20 focus:border-ep-primary text-base py-2.5 transition-all" placeholder="Breve descripción orientativa o notas sobre la firma..."></textarea>
                        </div>
                    </div>
                </div>

            </div>
            
            {{-- Footer / Acciones --}}
            <div class="p-6 bg-gray-50 flex items-center justify-between border-t border-gray-200 rounded-b-2xl">
                <a href="{{ route('admin.consultants') }}" class="px-6 py-3 text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded-xl transition-all">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-8 py-3 bg-ep-primary text-white text-base font-bold rounded-xl shadow-lg hover:bg-ep-primary-dark hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Coloris === 'undefined') {
        console.warn('Coloris no cargado');
        return;
    }

    Coloris({
        el: '[data-coloris]',
        theme: 'large',
        themeMode: 'light',
        formatToggle: false,
        clearButton: false,
        closeButton: true,
        closeLabel: 'Aplicar',
        alpha: false,
        defaultColor: '#4338ca',
        swatches: [
            '#4338ca','#3730a3','#6366f1',
            '#F69239','#f59e0b','#d97706',
            '#10B981','#059669','#34d399',
            '#EF4444','#dc2626','#f87171',
            '#3B82F6','#1d4ed8','#60a5fa',
            '#8B5CF6','#7c3aed','#a78bfa',
            '#111827','#374151','#6b7280',
            '#ffffff','#f3f4f6','#e5e7eb',
        ],
    });

    // When a color is picked via the panel, update preview + sync Livewire
    document.addEventListener('coloris:pick', function (event) {
        const input     = event.detail.currentEl;
        const hex       = event.detail.color;
        const key       = input.id.replace('hex-', '');
        const wireField = input.dataset.wireField;

        // Update preview swatch
        const preview = document.getElementById('preview-' + key);
        if (preview) preview.style.backgroundColor = hex;

        // Trigger native change event so wire:model.lazy picks it up
        input.dispatchEvent(new Event('change', { bubbles: true }));

        // Also set via Livewire directly for immediate sync
        if (wireField && window.Livewire) {
            const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
            if (componentId) Livewire.find(componentId)?.set(wireField, hex);
        }
    });
});
</script>
