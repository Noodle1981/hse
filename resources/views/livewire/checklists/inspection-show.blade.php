@php
    $user = auth()->user();
    $prefix = $user?->isAdmin() ? 'gestor' : 'tecnico';
    $indexRoute   = $prefix . '.inspections.index';
    
    // Si hay un visit_id activo, volver a la visita. Si no, a la lista correspondiente.
    if (isset($visit_id) && $visit_id) {
        $backUrl = route($prefix . '.visits.in-progress', $visit_id);
    } else {
        $backUrl = ($prefix === 'tecnico') 
            ? route('tecnico.checklists.tasks.index') 
            : route($sidebar_prefix ?? $prefix . '.inspections.index');
    }

    $pdfPreview   = $prefix . '.inspections.pdf.preview';
    $pdfDownload  = $prefix . '.inspections.pdf.download';
@endphp

<div class="w-full mx-auto">
    {{-- Header de Reporte Premium --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ $backUrl }}" class="p-2 bg-white/5 rounded-xl border border-white/10 text-indigo-100 hover:text-ep-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div class="flex flex-col">
                        <h1 class="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Reporte <span class="text-white">Finalizado</span>
                        </h1>
                        <h2 class="text-sm font-bold text-indigo-100 truncate max-w-[150px] sm:max-w-xs uppercase tracking-tight">
                            #{{ $inspection->id }} • {{ $inspection->checklist->title }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic leading-none mb-1">Verificado por</span>
                        <span class="text-xs font-black text-white italic uppercase">{{ $inspection->user->nombres ?? 'N/A' }}</span>
                    </div>
                    <div class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.2)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-0.5 w-full bg-gradient-to-r from-transparent via-emerald-500/30 to-transparent"></div>
        </header>
    @endsection

    <div class="py-6 min-h-screen bg-[#11111d] text-white selection:bg-ep-primary/30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs & Actions Premium -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-6 animate-fade-in-down">
                <div>
                    <h1 class="text-3xl font-black text-white italic uppercase tracking-tight leading-tight">
                        Resumen de <span class="text-ep-primary">Seguridad</span>
                    </h1>
                    <p class="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.3em] italic mt-2">Protocolo técnico consolidado</p>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <a href="{{ $backUrl }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-white/5 border border-white/10 text-[10px] font-black text-indigo-100 uppercase tracking-widest italic rounded-2xl hover:bg-white/10 hover:border-white/20 transition-all group">
                        <svg class="w-4 h-4 mr-2 text-indigo-300 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                        Volver
                    </a>
                    <a href="{{ route($pdfPreview, $inspection) }}" target="_blank"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-white/5 border border-white/10 text-[10px] font-black text-indigo-100 uppercase tracking-widest italic rounded-2xl hover:bg-white/10 hover:border-white/20 transition-all group">
                        <svg class="w-4 h-4 mr-2 text-indigo-300 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Vista Previa
                    </a>
                    <a href="{{ route($pdfDownload, $inspection) }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-ep-primary text-[10px] font-black text-white uppercase tracking-widest italic rounded-2xl shadow-lg shadow-ep-primary/20 hover:brightness-110 active:scale-95 transition-all group">
                        <svg class="w-4 h-4 mr-2 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar Reporte
                    </a>
                </div>
            </div>

            <!-- Main Report Content Premium -->
            <div class="bg-ep-dark rounded-[2.5rem] shadow-2xl border border-white/10 overflow-hidden relative animate-fade-in group">
                <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none opacity-30"></div>
                
                <!-- Report Metadata Grid -->
                <div class="bg-black/20 border-b border-white/5 p-8 sm:p-12">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="space-y-6">
                            <h4 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic mb-4">Metadatos del Registro</h4>
                            <div class="space-y-3">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-indigo-100/30 uppercase tracking-widest">Código Único</span>
                                    <span class="text-sm font-black text-white italic">#{{ $inspection->id }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-indigo-100/30 uppercase tracking-widest">Sincronización</span>
                                    <span class="text-sm font-black text-white italic">{{ $inspection->completed_at?->format('d/m/Y H:i') ?? 'Borrador' }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black tracking-[0.1em] uppercase italic border {{ $inspection->status === 'completed' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500' : 'bg-yellow-500/10 border-yellow-500/30 text-yellow-500' }}">
                                        {{ $inspection->status === 'completed' ? 'Finalizado' : 'En Proceso' }}
                                    </span>
                                    @if($inspection->result)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black tracking-[0.1em] uppercase italic border {{ $inspection->result === 'Pass' ? 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400' : 'bg-red-500/10 border-red-500/30 text-red-400' }}">
                                            {{ $inspection->result === 'Pass' ? 'Aprobado' : 'Crítico' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6 md:border-l md:border-white/5 md:pl-10">
                            <h4 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic mb-4">Ubicación Técnica</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-lg font-black text-white uppercase italic leading-tight">{{ $inspection->sede->empresa->nombre ?? 'N/A' }}</p>
                                    <p class="text-xs font-black text-ep-primary/60 uppercase italic tracking-widest mt-1">{{ $inspection->sede->nombre_sede ?? 'Sede no especificada' }}</p>
                                </div>
                                <p class="text-[10px] text-indigo-100/30 italic font-medium leading-relaxed max-w-[200px]">{{ $inspection->sede->direccion ?? '' }}</p>
                            </div>
                        </div>

                        <div class="space-y-6 md:border-l md:border-white/5 md:pl-10">
                            <h4 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.3em] italic mb-4">Profesional Encargado</h4>
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-black text-white uppercase italic">{{ $inspection->user->nombres ?? 'N/A' }} {{ $inspection->user->apellidos ?? '' }}</p>
                                    <p class="text-[9px] font-black text-ep-primary/60 uppercase tracking-widest leading-none">{{ $inspection->user->rol ?? 'Especialista' }}</p>
                                    <p class="text-[10px] text-indigo-100/30 italic">{{ $inspection->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answers Grouped by Sections Premium -->
                <div class="p-8 sm:p-12 space-y-16">
                    @php
                        $groupedAnswers = $answers->groupBy(fn($a) => $a->question->section_id);
                        $sections = $inspection->checklist->sections->keyBy('id');
                    @endphp

                    @foreach($groupedAnswers as $sectionId => $sectionAnswers)
                        <section class="animate-fade-in" <?php echo 'style="animation-delay: ' . ($loop->index * 0.1) . 's"'; ?>>
                            <div class="flex items-center gap-4 mb-8">
                                 <div class="h-1 w-8 bg-ep-primary rounded-full"></div>
                                 <h3 class="text-xl font-black text-white italic uppercase tracking-tight">
                                    {{ $sections[$sectionId]->title ?? 'Preguntas Generales' }}
                                 </h3>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($sectionAnswers as $answer)
                                    <div class="relative group/row grid grid-cols-1 md:grid-cols-2 gap-6 p-6 sm:p-8 bg-white/5 rounded-3xl border border-white/5 hover:border-white/10 hover:bg-white/[0.07] transition-all duration-300 shadow-inner">
                                        <div class="flex flex-col justify-center">
                                            <span class="text-xs font-black text-indigo-100 uppercase italic tracking-wide leading-snug group-hover/row:text-white transition-colors">{{ $answer->question->label }}</span>
                                            @if($answer->question->help_text)
                                                <span class="text-[10px] text-indigo-100/30 mt-2 italic font-medium uppercase tracking-widest leading-relaxed">{{ $answer->question->help_text }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-start md:justify-end">
                                            @if($answer->question->type === 'checkbox')
                                                @php $values = json_decode($answer->value, true) ?? []; @endphp
                                                <div class="flex flex-wrap gap-2 md:justify-end">
                                                    @forelse($values as $val)
                                                        <span class="px-3 py-1.5 bg-ep-primary/10 text-ep-primary rounded-xl border border-ep-primary/20 text-[10px] font-black uppercase italic tracking-widest">
                                                            {{ $val }}
                                                        </span>
                                                    @empty
                                                        <span class="text-indigo-100/10 text-[10px] font-black uppercase tracking-widest group-hover/row:text-indigo-100/20">Sin selección</span>
                                                    @endforelse
                                                </div>
                                            @else
                                                @php
                                                    $v = $answer->value ?: 'Sin respuesta';
                                                    $isPositive = in_array(strtoupper($v), ['SÍ', 'CUMPLE', 'BUENO', 'PASÓ', 'SI']);
                                                    $isNegative = in_array(strtoupper($v), ['NO', 'NO CUMPLE', 'MALO', 'FALLÓ']);
                                                @endphp
                                                
                                                <div class="flex items-center gap-3">
                                                    @if($isPositive)
                                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981]"></div>
                                                    @elseif($isNegative)
                                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_8px_#ef4444]"></div>
                                                    @endif
                                                    <span class="text-sm font-black uppercase italic tracking-widest {{ ($isPositive) ? 'text-emerald-400' : ($isNegative ? 'text-red-400' : (empty($answer->value) ? 'text-indigo-100/10' : 'text-indigo-100')) }} transition-colors">
                                                        {{ $v }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endforeach

                    <!-- Observaciones Premium -->
                    @if($inspection->notes)
                        <section class="bg-indigo-500/5 rounded-3xl p-8 border border-white/5 relative overflow-hidden group/notes">
                            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover/notes:opacity-10 transition-opacity">
                                 <svg class="w-16 h-16 text-indigo-400" fill="currentColor" viewBox="0 0 24 24"><path d="M21 7h-2v2h2V7zm0 4h-2v2h2v-2zm0 4h-2v2h2v-2zM7 7h10v2H7V7zm0 4h10v2H7v-2zm0 4h10v2H7v-2zM5 7H3v2h2V7zm0 4H3v2h2v-2zm0 4H3v2h2v-2z"/></svg>
                            </div>
                            <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] italic mb-5 flex items-center gap-2">
                                 <span class="w-3 h-px bg-indigo-500/30"></span> Comentarios y Críticas
                            </h3>
                            <p class="text-indigo-100/60 text-xs italic leading-relaxed whitespace-pre-line relative z-10">{{ $inspection->notes }}</p>
                        </section>
                    @endif

                    <!-- Signatures Premium -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-16 border-t border-white/5">
                        <!-- Supervisor Signature -->
                        <div class="flex flex-col items-center group/sig">
                            <div class="w-full max-w-[320px] aspect-[4/3] border border-white/10 rounded-[2rem] mb-6 flex items-center justify-center bg-black/40 shadow-inner overflow-hidden relative transition-all group-hover/sig:border-ep-primary/30">
                                @if($inspection->supervisor_signature)
                                    <img src="{{ $inspection->supervisor_signature }}" alt="Firma Supervisor" class="max-h-[80%] max-w-[80%] brightness-0 invert opacity-90 transition-all group-hover/sig:scale-105">
                                @else
                                    <div class="text-center p-6 opacity-10">
                                        <svg class="h-10 w-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <p class="text-[9px] font-black uppercase tracking-widest italic leading-none">Pendiente registro</p>
                                    </div>
                                @endif
                            </div>
                            <p class="text-sm font-black text-white uppercase italic tracking-tight">{{ $inspection->supervisor_name ?: 'Supervisor No Identificado' }}</p>
                            <p class="text-[9px] font-black text-ep-primary/40 uppercase tracking-[0.3em] mt-2 italic">Aceptación de Protocolo</p>
                        </div>

                        <!-- Technician Signature -->
                        <div class="flex flex-col items-center group/sig">
                            <div class="w-full max-w-[320px] aspect-[4/3] border border-white/10 rounded-[2rem] mb-6 flex items-center justify-center bg-black/40 shadow-inner overflow-hidden relative transition-all group-hover/sig:border-ep-primary/30">
                                @if($inspection->technician_signature)
                                    <img src="{{ $inspection->technician_signature }}" alt="Firma Técnico" class="max-h-[80%] max-w-[80%] brightness-0 invert opacity-90 transition-all group-hover/sig:scale-105">
                                @else
                                    <div class="text-center p-6 opacity-10">
                                        <svg class="h-10 w-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <p class="text-[9px] font-black uppercase tracking-widest italic leading-none">Pendiente registro</p>
                                    </div>
                                @endif
                            </div>
                            <p class="text-sm font-black text-white uppercase italic tracking-tight">{{ $inspection->user->nombres ?? '' }} {{ $inspection->user->apellidos ?? '' }}</p>
                            <p class="text-[9px] font-black text-ep-primary/40 uppercase tracking-[0.3em] mt-2 italic">Responsable Técnico</p>
                        </div>
                    </div>
                </div>
                
                <!-- Report Footer Premium -->
                <div class="bg-black/20 border-t border-white/5 p-8 text-center">
                    <p class="text-[9px] font-black text-indigo-100/10 uppercase tracking-[0.4em] italic">
                        Generado bajo protocolos de seguridad xamasafe • {{ now()->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <style>
            .animate-fade-in-down {
                animation: fade-in-down 0.6s ease-out forwards;
            }
            .animate-fade-in {
                animation: fade-in 0.8s ease-out forwards;
            }
            @keyframes fade-in-down {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes fade-in {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        </style>
    </div>
</div>

