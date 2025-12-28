<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="h-24 w-24 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13zM7 13a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13z" clip-rule="evenodd"/></svg>
            </div>
            
            <div class="flex items-center space-x-4 mb-4">
                <a href="{{ route('risk-matrices.index') }}" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Evaluación de Campo</h2>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $matrix->title }}</h1>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm pt-4 border-t border-gray-50">
                <div><span class="text-gray-400">Sede:</span> <span class="font-bold text-gray-700">{{ $matrix->sede->nombre_sede }}</span></div>
                <div><span class="text-gray-400">Area:</span> <span class="font-bold text-gray-700">{{ $matrix->area ?: 'N/D' }}</span></div>
                <div><span class="text-gray-400">Proceso:</span> <span class="font-bold text-gray-700">{{ $matrix->process ?: 'N/D' }}</span></div>
            </div>
        </div>

        {{-- Evaluation Cards --}}
        <div class="space-y-6">
            @foreach($matrix->risks as $risk)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="risk-{{ $risk->id }}">
                    {{-- Risk Title --}}
                    <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Peligro Identificado</span>
                            <h3 class="text-lg font-bold text-gray-800 leading-tight">{{ $risk->hazard }}</h3>
                        </div>
                        <div class="text-right">
                            <div class="px-3 py-1 rounded-full text-white text-xs font-bold shadow-sm" style="background-color: {{ $evaluations[$risk->id]['color'] }}">
                                {{ $evaluations[$risk->id]['level'] }} ({{ $evaluations[$risk->id]['score'] }})
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Probability --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Probabilidad (1-5)</label>
                                <div class="flex items-center justify-between gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button wire:click="$set('evaluations.{{ $risk->id }}.probability', {{ $i }}); updateCalculation({{ $risk->id }})" 
                                                class="flex-1 py-3 text-sm font-bold rounded-lg border-2 transition-all 
                                                       {{ $evaluations[$risk->id]['probability'] == $i 
                                                          ? 'bg-indigo-600 border-indigo-600 text-white shadow-md transform scale-105' 
                                                          : 'bg-white border-gray-100 text-gray-400 hover:border-indigo-200' }}">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <p class="text-[10px] text-gray-400 italic">1: Remoto • 5: Muy Probable / Frecuente</p>
                            </div>

                            {{-- Severity --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Severidad / Impacto (1-5)</label>
                                <div class="flex items-center justify-between gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button wire:click="$set('evaluations.{{ $risk->id }}.severity', {{ $i }}); updateCalculation({{ $risk->id }})" 
                                                class="flex-1 py-3 text-sm font-bold rounded-lg border-2 transition-all 
                                                       {{ $evaluations[$risk->id]['severity'] == $i 
                                                          ? 'bg-indigo-600 border-indigo-600 text-white shadow-md transform scale-105' 
                                                          : 'bg-white border-gray-100 text-gray-400 hover:border-indigo-200' }}">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <p class="text-[10px] text-gray-400 italic">1: Insignificante • 5: Catastrófico</p>
                            </div>
                        </div>

                        {{-- Final Evaluation --}}
                        <div class="mt-8 pt-6 border-t border-gray-50 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Justificación / Hallazgos</label>
                                <textarea wire:model.defer="evaluations.{{ $risk->id }}.justification" 
                                          rows="2" 
                                          placeholder="Describa por qué asigna estos valores (ej: falta de señalización, estado de herramientas...)" 
                                          class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                            </div>
                            <div class="flex items-end">
                                <button wire:click="saveEvaluation({{ $risk->id }})" 
                                        class="w-full py-3 px-4 bg-gray-900 hover:bg-black text-white rounded-lg font-bold text-sm shadow-md transition-all flex items-center justify-center group">
                                    <svg class="w-4 h-4 mr-2 text-indigo-400 group-hover:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                    Guardar Riesgo
                                </button>
                            </div>
                        </div>

                        {{-- Alert feedback --}}
                        @if (session()->has('risk_'.$risk->id))
                            <div class="mt-4 p-2 bg-green-50 text-green-700 text-xs font-bold rounded border border-green-100 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                {{ session('risk_'.$risk->id) }}
                            </div>
                        @endif
                    </div>

                    {{-- Measures display (Read-only for Tech) --}}
                    @if($risk->controls->count() > 0)
                    <div class="bg-indigo-50/30 p-4 border-t border-gray-50">
                        <h4 class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-2 italic">Medidas de seguridad requeridas:</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($risk->controls as $control)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-white border border-indigo-100 text-[10px] font-medium text-indigo-700 shadow-sm">
                                    <svg class="w-2 h-2 mr-1.5 text-indigo-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                    {{ $control->measure }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        {{-- Finish Button / Summary --}}
        <div class="mt-12 text-center pb-20">
            <p class="text-sm text-gray-500 mb-6">Asegúrese de haber evaluado todos los peligros antes de retirarse del área.</p>
            <a href="{{ route('risk-matrices.index') }}" class="inline-flex items-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Finalizar Recorrido
            </a>
        </div>
    </div>
</div>
