<div>
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-ep-dark flex items-center gap-3">
                <span class="text-3xl">📱</span>
                Emulador: {{ $checklist->title }}
            </h1>
            <p class="text-gray-500 mt-1">
                Visualización previa de cómo el técnico verá e interactuará con este checklist.
                <strong>(Modo de solo lectura interactivo)</strong>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('gestor.checklists.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                ← Volver a Checklists
            </a>
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Vista de Impresión (PDF)
            </button>
        </div>
    </div>

    <!-- Contenedor principal estilo móvil/tablet -->
    <div class="max-w-3xl mx-auto bg-gray-100 p-4 sm:p-8 rounded-2xl shadow-inner print:max-w-none print:bg-white print:p-0 print:shadow-none">
        <!-- Dispositivo Mockup -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200 print:shadow-none print:border-none print:rounded-none">
            
            <!-- Header Simulado de la App -->
            <div class="bg-ep-dark text-white p-4 text-center sticky top-0 z-10 print:bg-white print:text-black print:border-b print:border-gray-300">
                <h2 class="font-bold text-lg truncate">{{ $checklist->title }}</h2>
                @if($checklist->registry_code)
                    <p class="text-xs text-ep-muted print:text-gray-600">Código: {{ $checklist->registry_code }}</p>
                @endif
            </div>

            <div class="p-6 space-y-8">
                @if($checklist->description)
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-6 print:border print:border-blue-200">
                        {{ $checklist->description }}
                    </div>
                @endif

                <!-- Preguntas SIN sección -->
                @if(count($unsectionedQuestions) > 0)
                    <div class="space-y-6">
                        @foreach($unsectionedQuestions as $question)
                            @include('livewire.checklists.partials._emulator-question', ['question' => $question])
                        @endforeach
                    </div>
                @endif

                <!-- Preguntas agrupadas por SECCIÓN -->
                @foreach($checklist->sections->sortBy('order') as $section)
                    @if(isset($groupedQuestions[$section->id]) && count($groupedQuestions[$section->id]) > 0)
                        <div class="section-block {{ $loop->first && count($unsectionedQuestions) == 0 ? '' : 'pt-6 border-t border-gray-200' }}">
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-ep-dark bg-gray-50 p-3 rounded border-l-4 border-ep-primary print:bg-transparent print:border-gray-800">
                                    {{ $section->title }}
                                </h3>
                                @if($section->description)
                                    <p class="text-sm text-gray-500 mt-2 px-3">{{ $section->description }}</p>
                                @endif
                            </div>

                            <div class="space-y-6 px-3">
                                @foreach($groupedQuestions[$section->id] as $question)
                                    @include('livewire.checklists.partials._emulator-question', ['question' => $question])
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Footer Simulado de la App -->
            <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end print:hidden">
                <button type="button" disabled class="px-6 py-3 bg-ep-primary text-white rounded-lg opacity-80 cursor-not-allowed font-semibold w-full sm:w-auto text-center shadow-sm flex justify-center items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Completar Inspección (Simulado)
                </button>
            </div>
        </div>
    </div>
    
    <!-- Estilos específicos para impresión (PDF mockup) -->
    <style>
        @media print {
            body {
                background: white;
            }
            nav, header, footer, .mb-6.flex.justify-between {
                display: none !important;
            }
            .section-block {
                page-break-inside: avoid;
            }
            .question-container {
                page-break-inside: avoid;
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
            }
            input[type="text"], input[type="number"], input[type="date"], textarea, select {
                border: 1px solid #999 !important;
                background-color: transparent !important;
            }
        }
    </style>
</div>
