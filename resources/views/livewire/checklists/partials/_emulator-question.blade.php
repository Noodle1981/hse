@php
    $id = 'q_' . $question->id;
@endphp

<div class="question-container p-4 bg-white rounded-lg border border-gray-100 shadow-sm print:shadow-none print:border-b print:border-gray-300 print:rounded-none">
    <label class="block text-sm font-medium text-gray-800 mb-2">
        {{ $question->label }}
        @if($question->is_required)
            <span class="text-red-500 ml-1" title="Campo requerido">*</span>
        @endif
    </label>

    @if($question->help_text)
        <p class="text-xs text-gray-500 mb-3 italic">{{ $question->help_text }}</p>
    @endif

    <div class="mt-2">
        @switch($question->type)
            @case('text')
                <input type="text" disabled readonly 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed print:bg-transparent print:border-gray-400"
                       placeholder="Respuesta de texto corto...">
                @break

            @case('textarea')
                <textarea disabled readonly rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed print:bg-transparent print:border-gray-400"
                          placeholder="Respuesta de texto largo..."></textarea>
                @break

            @case('number')
                <div class="flex items-center gap-2">
                    <input type="number" disabled readonly 
                           class="w-32 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed print:bg-transparent print:border-gray-400"
                           placeholder="0.00">
                    
                    @if($question->min_value !== null || $question->max_value !== null)
                        <span class="text-xs text-gray-400 print:hidden">
                            (Rango aceptado: {{ $question->min_value ?? '-∞' }} a {{ $question->max_value ?? '∞' }})
                        </span>
                    @endif
                </div>
                @break

            @case('date')
                <input type="date" disabled readonly 
                       class="w-48 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed print:bg-transparent print:border-gray-400">
                @break

            @case('select')
                <select disabled readonly 
                        class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed print:bg-transparent print:border-gray-400 print:appearance-none">
                    <option value="">Seleccione una opción...</option>
                    @if($question->options)
                        @foreach($question->options as $option)
                            <option value="{{ $option }}">{{ $option }} {!! in_array($option, $question->failure_values ?? []) ? '(⚠️ Falla)' : '' !!}</option>
                        @endforeach
                    @endif
                </select>
                @break

            @case('radio')
                @if($question->options)
                    <div class="space-y-2">
                        @foreach($question->options as $index => $option)
                            <label class="flex items-center space-x-3 cursor-not-allowed">
                                <input type="radio" disabled
                                       name="{{ $id }}"
                                       class="w-4 h-4 text-ep-primary border-gray-300 focus:ring-ep-primary">
                                <span class="text-sm text-gray-600">
                                    {{ $option }}
                                    @if(in_array($option, $question->failure_values ?? []))
                                        <span class="text-xs text-red-500 ml-1 print:hidden">(Dispara Falla)</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
                @break

            @case('checkbox')
                @if($question->options)
                    <div class="space-y-2">
                        @foreach($question->options as $index => $option)
                            <label class="flex items-center space-x-3 cursor-not-allowed">
                                <input type="checkbox" disabled
                                       class="w-4 h-4 text-ep-primary rounded border-gray-300 focus:ring-ep-primary">
                                <span class="text-sm text-gray-600">
                                    {{ $option }}
                                    @if(in_array($option, $question->failure_values ?? []))
                                        <span class="text-xs text-red-500 ml-1 print:hidden">(Dispara Falla)</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
                @break

            @case('signature')
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50 print:border-solid print:bg-transparent print:border-gray-400">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2 print:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    <span class="text-sm text-gray-500">Área para firma digital (Bloqueada en Emulador)</span>
                    <div class="h-16 hidden print:block pt-8 text-gray-300 border-b border-gray-300 w-64 mx-auto"></div>
                </div>
                @break
        @endswitch
    </div>
</div>
