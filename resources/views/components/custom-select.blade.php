@props(['label' => null, 'model', 'options', 'placeholder' => 'Seleccionar...', 'icon' => null, 'disabled' => false])

<div class="space-y-3" x-data="{ 
    open: false, 
    value: @entangle($model),
    get label() {
        if (!this.value) return '{{ $placeholder }}';
        const option = this.options.find(o => o.value == this.value);
        return option ? option.label : '{{ $placeholder }}';
    },
    options: @js($options),
    search: '',
    get filteredOptions() {
        if (!this.search) return this.options;
        return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
    }
}" @click.outside="open = false">
    @if($label)
        <label class="flex items-center gap-2 text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic ml-1">
            @if($icon)
                {!! $icon !!}
            @else
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            @endif
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <button @click="if(!{{ $disabled ? 'true' : 'false' }}) open = !open" 
                type="button" 
                {{ $disabled ? 'disabled' : '' }}
                class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3.5 text-sm font-bold text-white flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-ep-primary/50 transition-all backdrop-blur-md {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-white/20' }}">
            <span x-text="label" :class="!value ? 'text-white/20 font-medium' : ''"></span>
            <svg class="w-4 h-4 text-ep-primary transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
             class="absolute z-[100] mt-3 w-full bg-[#1e1e2d] border border-white/10 rounded-3xl shadow-2xl py-3 backdrop-blur-2xl overflow-hidden overflow-y-auto max-h-72 custom-select-scrollbar"
             style="display: none;">
             
            {{-- Search internal (Optional if many options) --}}
            <template x-if="options.length > 10">
                <div class="px-3 pb-2 mb-2 border-b border-white/5">
                    <input type="text" x-model="search" placeholder="Filtrar..." class="w-full bg-white/5 border border-white/5 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-ep-primary focus:outline-none">
                </div>
            </template>

            <div class="px-2 space-y-1">
                {{-- Opcion nula si no es requerido --}}
                <div @click="value = ''; open = false" 
                     class="px-5 py-3 text-[11px] font-black uppercase tracking-widest text-indigo-100/30 hover:bg-white/5 hover:text-white rounded-2xl transition-all cursor-pointer italic">
                    Limpiar Selección
                </div>

                <template x-for="option in filteredOptions" :key="option.value">
                    <div @click="value = option.value; open = false" 
                         class="px-5 py-3.5 text-xs font-bold text-indigo-100/70 hover:bg-ep-primary/20 hover:text-white rounded-2xl transition-all cursor-pointer flex items-center justify-between group"
                         :class="value == option.value ? 'bg-ep-primary/10 text-white' : ''">
                        <span x-text="option.label"></span>
                        <div x-show="value == option.value" class="h-2 w-2 bg-ep-primary rounded-full shadow-[0_0_10px_#F69239]"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-select-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-select-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-select-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(246, 146, 57, 0.2);
        border-radius: 10px;
    }
    .custom-select-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(246, 146, 57, 0.5);
    }
</style>
