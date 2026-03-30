<div>
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg print:hidden">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h1 class="text-lg md:text-xl font-medium text-white tracking-tight">
                        📱 Emulador: <span class="text-ep-primary italic">{{ $checklist->title }}</span>
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <button onclick="window.print()" 
                            class="hidden xs:flex bg-white/10 text-white hover:bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir PDF
                    </button>

                    <a href="{{ route('gestor.checklists.index') }}" 
                       class="flex items-center space-x-2 bg-ep-primary text-white hover:bg-ep-primary-dark px-3 py-1 rounded-full text-[10px] font-bold border border-white/10 transition-all">
                        <span>← Volver</span>
                    </a>
                </div>
            </div>
        </header>
    @endsection

    <!-- Información informativa (Modo interactivo) -->
    <div class="mb-8 flex items-center justify-between bg-ep-dark border-l-4 border-ep-primary p-5 rounded-2xl shadow-xl overflow-hidden relative group print:hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
        <div class="relative flex items-center gap-4">
            <div class="p-3 bg-white/5 rounded-xl border border-white/10 text-ep-primary shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-white text-sm font-black uppercase tracking-widest italic leading-none mb-1">
                    Modo <span class="text-ep-primary">Interactivo</span>
                </p>
                <p class="text-indigo-100/60 text-[11px] font-medium italic">
                    Esta es una visualización previa de cómo el técnico interactuará con el checklist.
                </p>
            </div>
        </div>
        <div class="hidden sm:block opacity-20 group-hover:opacity-40 transition-opacity">
            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17 2H7C5.89 2 5 2.89 5 4V20C5 21.11 5.89 22 7 22H17C18.11 22 19 21.11 19 20V4C19 2.89 18.11 2 17 2M17 19H7V5H17V19Z"/>
            </svg>
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
