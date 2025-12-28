<div class="py-6 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumbs & Actions -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('inspections.index') }}" class="text-sm font-medium text-gray-500 hover:text-ep-primary">Historial</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="text-sm font-medium text-gray-400">Inspección #{{ $inspection->id }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $inspection->checklist->title }}</h1>
            </div>
            
            <div class="mt-4 md:mt-0 flex gap-3">
                <a href="{{ route('inspections.pdf.preview', $inspection) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Vista Previa PDF
                </a>
                <a href="{{ route('inspections.pdf.download', $inspection) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-md shadow-sm text-white bg-ep-primary hover:bg-ep-primary-dark transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Descargar Reporte
                </a>
            </div>
        </div>

        <!-- Main Report Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Report Header Section -->
            <div class="bg-gray-50 border-b border-gray-200 p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Información General</h4>
                        <div class="space-y-2">
                            <p class="text-sm"><span class="font-semibold text-gray-600">ID Reporte:</span> #{{ $inspection->id }}</p>
                            <p class="text-sm"><span class="font-semibold text-gray-600">Fecha:</span> {{ $inspection->completed_at?->format('d/m/Y H:i') ?? 'En proceso' }}</p>
                            <p class="text-sm">
                                <span class="font-semibold text-gray-600">Estado:</span>
                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-bold tracking-tight uppercase {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $inspection->status === 'completed' ? 'Completado' : 'Borrador' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Ubicación</h4>
                        <div class="space-y-2">
                            <p class="text-sm font-bold text-ep-primary text-lg">{{ $inspection->sede->empresa->nombre ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 font-medium">{{ $inspection->sede->nombre_sede ?? 'Sede no especificada' }}</p>
                            <p class="text-xs text-gray-400">{{ $inspection->sede->direccion ?? '' }}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Responsable</h4>
                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-gray-800">{{ $inspection->user->nombres ?? 'Desconocido' }} {{ $inspection->user->apellidos ?? '' }}</p>
                            <p class="text-xs text-gray-500">{{ $inspection->user->rol ?? 'Técnico' }}</p>
                            <p class="text-xs text-gray-400">{{ $inspection->user->email ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Answers Grouped by Sections -->
            <div class="p-8 space-y-12">
                @php
                    $groupedAnswers = $answers->groupBy(fn($a) => $a->question->section_id);
                    $sections = $inspection->checklist->sections->keyBy('id');
                @endphp

                @foreach($groupedAnswers as $sectionId => $sectionAnswers)
                    <section>
                        <h3 class="text-lg font-bold text-gray-900 border-l-4 border-ep-primary pl-4 mb-6">
                            {{ $sections[$sectionId]->title ?? 'Preguntas Generales' }}
                        </h3>
                        
                        <div class="space-y-6">
                            @foreach($sectionAnswers as $answer)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors p-2 rounded-lg">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700">{{ $answer->question->label }}</span>
                                        @if($answer->question->help_text)
                                            <span class="text-xs text-gray-400 mt-1 italic">{{ $answer->question->help_text }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center">
                                        @if($answer->question->type === 'checkbox')
                                            @php $values = json_decode($answer->value, true) ?? []; @endphp
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($values as $val)
                                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded border border-blue-100 text-xs font-semibold">
                                                        ✓ {{ $val }}
                                                    </span>
                                                @empty
                                                    <span class="text-gray-300 text-xs italic">Sin selección</span>
                                                @endforelse
                                            </div>
                                        @else
                                            <span class="text-sm {{ empty($answer->value) ? 'text-gray-300 italic' : 'text-gray-900 font-medium' }}">
                                                {{ $answer->value ?: 'Sin respuesta' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <!-- Observaciones -->
                @if($inspection->notes)
                    <section class="bg-blue-50/50 rounded-xl p-6 border border-blue-100">
                        <h3 class="text-sm font-bold text-blue-800 uppercase tracking-widest mb-3">Observaciones y Hallazgos</h3>
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $inspection->notes }}</p>
                    </section>
                @endif

                <!-- Signatures -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-12 border-t border-gray-200">
                    <!-- Supervisor Signature -->
                    <div class="flex flex-col items-center">
                        <div class="w-full max-w-[300px] h-40 border-b-2 border-gray-300 mb-4 flex items-center justify-center bg-gray-50 rounded-t-lg overflow-hidden">
                            @if($inspection->supervisor_signature)
                                <img src="{{ $inspection->supervisor_signature }}" alt="Firma Supervisor" class="max-h-full max-w-full">
                            @else
                                <span class="text-gray-300 text-xs italic">Firma no registrada</span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-gray-900">{{ $inspection->supervisor_name ?: 'Supervisor no especificado' }}</p>
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mt-1">Conformidad Supervisor</p>
                    </div>

                    <!-- Technician Signature -->
                    <div class="flex flex-col items-center">
                        <div class="w-full max-w-[300px] h-40 border-b-2 border-gray-300 mb-4 flex items-center justify-center bg-gray-50 rounded-t-lg overflow-hidden">
                            @if($inspection->technician_signature)
                                <img src="{{ $inspection->technician_signature }}" alt="Firma Técnico" class="max-h-full max-w-full">
                            @else
                                <span class="text-gray-300 text-xs italic">Firma no registrada</span>
                            @endif
                        </div>
                        <p class="text-sm font-bold text-gray-900">{{ $inspection->user->nombres ?? '' }} {{ $inspection->user->apellidos ?? '' }}</p>
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mt-1">Firma del Profesional</p>
                    </div>
                </div>
            </div>
            
            <!-- Report Footer -->
            <div class="bg-gray-50 border-t border-gray-200 p-6 text-center">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">
                    Generado automáticamente por EP Checklists • {{ now()->format('d/m/Y H:i:s') }}
                </p>
            </div>
        </div>
    </div>
</div>
