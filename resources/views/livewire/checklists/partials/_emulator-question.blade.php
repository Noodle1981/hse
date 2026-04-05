@php
    $id = 'q_' . $question->id;
@endphp

<div class="space-y-6 relative p-8 bg-white/5 rounded-[2rem] border border-white/5 hover:border-ep-primary/20 transition-all duration-500 group/question mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <label class="block text-sm font-black text-indigo-100 uppercase italic tracking-wide leading-snug">
            {{ $question->label }}
            @if($question->is_required) 
                <span class="text-ep-primary ml-1 font-black animate-pulse" title="Campo Requerido">*</span> 
            @endif
        </label>
        
        @if($question->help_text)
            <div class="flex items-center gap-2 px-3 py-1 bg-white/5 rounded-full border border-white/5">
                <svg class="w-3.5 h-3.5 text-indigo-100/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-[9px] font-black text-indigo-100/40 uppercase italic tracking-widest">{{ $question->help_text }}</span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="mt-2">
        @if($question->type === 'radio')
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach($question->options as $option)
                    @php
                        $isFailure = in_array($option, $question->failure_values ?? []);
                        $isSelected = ($answers[$question->id] ?? '') === $option;
                        $isSuccess = in_array($option, ['Sí', 'CUMPLE', 'BUENO', 'APTO', 'CONFORME']);
                    @endphp
                    <label class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer group/opt transition-all duration-300
                        {{ $isSelected 
                            ? ($isFailure 
                                ? 'bg-red-500/20 border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.2)]' 
                                : 'bg-ep-primary/20 border-ep-primary shadow-[0_0_15px_rgba(246,146,57,0.1)]') 
                            : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20' }}">
                        <input type="radio" wire:model.live="answers.{{ $question->id }}" value="{{ $option }}" class="sr-only">
                        
                        @if($isFailure)
                            <div class="w-2 rounded-full bg-red-500 h-2 mb-2 {{ $isSelected ? 'animate-ping scale-125 shadow-[0_0_8px_#ef4444]' : 'opacity-40' }}"></div>
                        @elseif($isSuccess)
                            <div class="w-2 rounded-full bg-emerald-500 h-2 mb-2 {{ $isSelected ? 'animate-pulse scale-125 shadow-[0_0_8px_#10b981]' : 'opacity-40' }}"></div>
                        @else
                            <div class="w-2 rounded-full bg-white/20 h-2 mb-2 {{ $isSelected ? 'animate-pulse scale-125' : 'opacity-40' }}"></div>
                        @endif

                        <span class="text-[10px] font-black uppercase tracking-widest italic transition-colors {{ $isSelected ? 'text-white' : 'text-indigo-100/40' }}">{{ $option }}</span>
                    </label>
                @endforeach
            </div>
        @elseif($question->type === 'select')
            @php $isSelectedFailure = in_array($answers[$question->id] ?? '', $question->failure_values ?? []); @endphp
            <div class="relative">
                <select wire:model.live="answers.{{ $question->id }}" 
                        class="block w-full bg-black/40 border rounded-2xl px-5 py-4 text-white text-sm font-bold italic focus:outline-none focus:ring-2 transition-all [color-scheme:dark]
                        {{ $isSelectedFailure ? 'border-red-500 ring-2 ring-red-500/20 bg-red-500/5' : 'border-white/10 focus:ring-ep-primary focus:border-transparent' }}">
                    <option value="" class="bg-ep-dark text-white/40">Sincronizar opción...</option>
                    @foreach($question->options as $option)
                        <option value="{{ $option }}" class="bg-ep-dark text-white uppercase italic font-black">{{ $option }}</option>
                    @endforeach
                </select>
                @if($isSelectedFailure)
                    <div class="absolute right-12 top-1/2 -translate-y-1/2 flex items-center gap-2 px-3 py-1 bg-red-500 rounded-lg shadow-[0_0_15px_rgba(239,68,68,0.4)] animate-pulse">
                        <span class="text-[8px] font-black text-white uppercase tracking-widest italic">Falla Crítica</span>
                    </div>
                @endif
            </div>
        @elseif($question->type === 'checkbox')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($question->options as $option)
                    @php 
                        $isSelected = in_array($option, (array)($answers[$question->id] ?? []));
                        $isOptionFailure = in_array($option, $question->failure_values ?? []);
                    @endphp
                    <label class="flex items-center space-x-4 p-5 rounded-2xl border transition-all cursor-pointer shadow-inner
                                {{ $isSelected 
                                    ? ($isOptionFailure ? 'bg-red-500/20 border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.1)]' : 'bg-ep-primary/20 border-ep-primary/40') 
                                    : 'bg-white/5 border-white/5 hover:bg-white/10' }}">
                        <input type="checkbox" wire:model.live="answers.{{ $question->id }}" value="{{ $option }}" 
                               class="h-6 w-6 text-ep-primary focus:ring-ep-primary/50 border-white/20 bg-black/40 rounded-xl transition-all">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] italic {{ $isSelected ? 'text-white' : 'text-indigo-100/40' }}">{{ $option }}</span>
                            @if($isOptionFailure && $isSelected)
                                <span class="text-[8px] font-black text-red-500 uppercase italic tracking-widest">Dispara Protocolo de Falla</span>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        @elseif($question->type === 'textarea')
            <textarea wire:model.live="answers.{{ $question->id }}" rows="3" 
                      class="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-white text-sm font-bold italic focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-white/5" 
                      placeholder="Protocolo técnico - Inserte registro detallado..."></textarea>
        @elseif($question->type === 'number')
            @php 
                $val = $answers[$question->id] ?? null;
                $isOOR = ($val !== null && $val !== '') && (
                    ($question->min_value !== null && $val < $question->min_value) || 
                    ($question->max_value !== null && $val > $question->max_value)
                );
            @endphp
            <div class="relative group/input">
                <input type="number" step="any" wire:model.live="answers.{{ $question->id }}" 
                       class="block w-full bg-black/40 border rounded-2xl px-6 py-5 text-white text-sm font-black italic focus:outline-none focus:ring-2 transition-all
                       {{ $isOOR ? 'border-red-500 ring-2 ring-red-500/20 bg-red-500/5' : 'border-white/10 focus:ring-ep-primary' }}" 
                       placeholder="0.00">
                <div class="absolute right-6 top-1/2 -translate-y-1/2 flex items-center gap-4">
                    @if($isOOR)
                        <div class="px-3 py-1 bg-red-500 rounded-lg shadow-[0_0_15px_rgba(239,68,68,0.4)] animate-bounce">
                            <span class="text-[8px] font-black text-white uppercase tracking-widest italic">Fuera de Rango</span>
                        </div>
                    @endif
                    <div class="text-[10px] font-black text-white/10 uppercase italic tracking-widest select-none group-focus-within/input:text-ep-primary/40">Unit_Val</div>
                </div>
            </div>
        @elseif($question->type === 'date')
            <input type="date" wire:model.live="answers.{{ $question->id }}" 
                   class="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-white text-sm font-black italic focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all [color-scheme:dark]">
        @elseif($question->type === 'signature')
             <div class="bg-black/40 p-8 rounded-3xl border border-white/5 relative overflow-hidden group/sign">
                <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/5 to-transparent opacity-50"></div>
                <div class="flex justify-between items-center mb-6">
                    <span class="text-[9px] font-black text-indigo-100/30 uppercase tracking-[0.3em] italic">Captura Firma Digital</span>
                    <span class="text-[8px] font-black text-white/5 uppercase tracking-widest italic bg-white/5 px-2 py-0.5 rounded">EP-SIGN-EMU</span>
                </div>
                <div class="h-32 border-2 border-dashed border-white/5 rounded-2xl flex items-center justify-center text-center">
                    <div>
                        <svg class="h-8 w-8 mx-auto text-indigo-100/20 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <p class="text-[9px] font-black text-indigo-100/20 uppercase tracking-widest italic">Panel Bloqueado en Emulador</p>
                    </div>
                </div>
            </div>
        @else
            <input type="text" wire:model.live="answers.{{ $question->id }}" 
                   class="block w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-5 text-white text-sm font-black italic focus:outline-none focus:ring-2 focus:ring-ep-primary focus:border-transparent transition-all placeholder-white/5" 
                   placeholder="Inserte valor alfanumérico...">
        @endif
    </div>

    {{-- Validation Error (Simulated for emulator) --}}
    @error("answers.{$question->id}")
        <div class="flex items-center gap-2 mt-2 px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-xl animate-fade-in">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="text-[9px] font-black text-red-500/80 uppercase tracking-widest italic">{{ $message }}</span>
        </div>
    @enderror
</div>
