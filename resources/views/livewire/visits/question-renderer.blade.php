<div class="bg-white p-3 rounded border border-gray-200 hover:shadow-sm transition-shadow">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $question->label }}
        @if($question->is_required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    @if($question->help_text)
        <p class="text-xs text-gray-500 mb-2">{{ $question->help_text }}</p>
    @endif

    <div class="mt-2">
        @switch($question->type)
            @case('text')
                <input type="text" 
                       wire:model="responses.{{ $question->id }}"
                       class="shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md">
                @break

            @case('textarea')
                <textarea wire:model="responses.{{ $question->id }}"
                          rows="3"
                          class="shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                @break

            @case('select')
                <select wire:model="responses.{{ $question->id }}"
                        class="shadow-sm focus:ring-ep-primary focus:border-ep-primary block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Seleccione...</option>
                    @if(is_array($question->options))
                        @foreach($question->options as $option)
                             {{-- Manejar si options es array de strings o clave/valor --}}
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    @endif
                </select>
                @break

            @case('radio')
                <div class="space-y-2">
                    @if(is_array($question->options))
                        @foreach($question->options as $option)
                            <div class="flex items-center">
                                <input id="q-{{ $question->id }}-opt-{{ $loop->index }}" 
                                       name="q-{{ $question->id }}" 
                                       type="radio" 
                                       wire:model="responses.{{ $question->id }}"
                                       value="{{ $option }}"
                                       class="focus:ring-ep-primary h-4 w-4 text-ep-primary border-gray-300">
                                <label for="q-{{ $question->id }}-opt-{{ $loop->index }}" class="ml-3 block text-sm font-medium text-gray-700">
                                    {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    @endif
                </div>
                @break

            @case('checkbox')
                <div class="space-y-2">
                   @if(is_array($question->options))
                        @foreach($question->options as $option)
                            <div class="flex items-center">
                                <input id="q-{{ $question->id }}-chk-{{ $loop->index }}" 
                                       wire:model="responses.{{ $question->id }}"
                                       value="{{ $option }}"
                                       type="checkbox" 
                                       class="focus:ring-ep-primary h-4 w-4 text-ep-primary border-gray-300 rounded">
                                <label for="q-{{ $question->id }}-chk-{{ $loop->index }}" class="ml-3 block text-sm font-medium text-gray-700">
                                    {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    @endif
                </div>
                @break

            @case('compliant')
                 <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="responses.{{ $question->id }}" value="Cumple" class="text-green-600 focus:ring-green-500 border-gray-300">
                        <span class="ml-2">Cumple</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="responses.{{ $question->id }}" value="No Cumple" class="text-red-600 focus:ring-red-500 border-gray-300">
                        <span class="ml-2">No Cumple</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="responses.{{ $question->id }}" value="N/A" class="text-gray-600 focus:ring-gray-500 border-gray-300">
                        <span class="ml-2">N/A</span>
                    </label>
                </div>
                @break

            @case('photo')
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="file-upload-{{ $question->id }}" class="relative cursor-pointer bg-white rounded-md font-medium text-ep-primary hover:text-ep-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-ep-primary">
                                <span>Subir un archivo</span>
                                <input id="file-upload-{{ $question->id }}" name="file-upload" type="file" class="sr-only">
                            </label>
                            <p class="pl-1">o arrastrar y soltar</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG hasta 10MB</p>
                    </div>
                </div>
                <p class="text-xs text-yellow-600 mt-1">Funcionalidad de fotos pendiente de implementación completa.</p>
                @break

            @default
                <p class="text-xs text-red-500">Tipo de pregunta no soportado: {{ $question->type }}</p>
        @endswitch
        
        @error("responses.{$question->id}") 
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
        @enderror
    </div>
</div>
