<div class="max-w-4xl mx-auto space-y-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/50">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Personalización Corporativa</h2>
            <p class="text-sm text-gray-500 mt-1">Configura la identidad visual de {{ $nombre }} en la plataforma.</p>
        </div>
        
        <form wire:submit.prevent="save" class="p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                {{-- Logo Section --}}
                <div class="space-y-4">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">Logo de la Empresa</label>
                    <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50 hover:bg-white hover:border-indigo-300 transition-all group relative overflow-hidden">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="h-32 object-contain mb-4">
                        @elseif ($existingLogo)
                            <img src="{{ asset('storage/' . $existingLogo) }}" class="h-32 object-contain mb-4">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif

                        <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                        <span class="text-xs font-bold text-gray-500 group-hover:text-indigo-600">Haz clic o arrastra para cambiar logo</span>
                        <p class="text-[10px] text-gray-400 mt-2">PNG, JPG hasta 1MB (Recomendado: Fondo transparente)</p>
                    </div>
                    @error('logo') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>

                {{-- Color Section --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Color de Marca (Botones y Acentos)</label>
                        <div class="flex items-center space-x-4">
                            <input type="color" wire:model="primary_color" class="h-12 w-24 rounded-lg border-gray-200 cursor-pointer p-1">
                            <input type="text" wire:model="primary_color" class="flex-1 rounded-lg border-gray-200 text-sm font-mono uppercase">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2 italic">Este color se aplicará dinámicamente en tu panel de control y reportes.</p>
                        @error('primary_color') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-indigo-50/30 p-4 rounded-xl border border-indigo-100/50">
                        <h4 class="text-[10px] font-bold text-indigo-800 uppercase tracking-widest mb-2 italic">Vista Previa de Estilo</h4>
                        <div class="flex items-center space-x-2">
                            <div class="h-8 flex-1 rounded shadow-sm" style="background-color: {{ $primary_color }}"></div>
                            <button type="button" class="h-8 px-4 text-[10px] font-bold text-white rounded shadow-sm" style="background-color: {{ $primary_color }}">Botón Demo</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-50 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-gray-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-800 transition-all shadow-lg hover:shadow-indigo-500/20">
                    Guardar Configuración de Marca
                </button>
            </div>
        </form>
    </div>
</div>

