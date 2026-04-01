@php
    $borderColor = match($style) {
        'today' => 'border-ep-primary',
        'overdue' => 'border-red-500',
        'week' => 'border-indigo-500/30',
        default => 'border-white/5'
    };
    
    $bgIcon = match($style) {
        'today' => 'bg-ep-primary/20 text-ep-primary',
        'overdue' => 'bg-red-500/20 text-red-500 animate-pulse',
        'week' => 'bg-indigo-500/10 text-indigo-400',
        default => 'bg-white/5 text-white'
    };
@endphp

<div class="p-6 mx-4 mb-3 bg-white/[0.02] border {{ $borderColor }} rounded-3xl hover:bg-white/[0.05] transition-all duration-500 group">
    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-6 w-full md:w-auto">
            <div class="h-14 w-1 flex-shrink-0 bg-white/5 rounded-full group-hover:h-16 transition-all duration-700 {{ $style === 'today' ? 'bg-ep-primary' : ($style === 'overdue' ? 'bg-red-500' : '') }}"></div>
            
            <div class="h-12 w-12 flex-shrink-0 rounded-2xl border border-white/5 flex items-center justify-center {{ $bgIcon }} shadow-inner">
                @if($style === 'overdue')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5"/></svg>
                @elseif(($task->category ?? 'Inspección') === 'Capacitación')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke-width="2"/></svg>
                @elseif(($task->category ?? 'Inspección') === 'Informe')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                @elseif($style === 'today')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2.5"/></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                @endif
            </div>

            <div>
                <h4 class="text-lg font-black text-white italic group-hover:text-ep-primary transition-colors leading-none mb-2 uppercase tracking-tight">
                    {{ $task->checklist->title ?? 'SIN TÍTULO' }}
                </h4>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black text-indigo-100/40 uppercase tracking-widest italic tracking-tighter">{{ $task->sede->nombre_sede ?? 'SEDE' }}</span>
                    <span class="h-1 w-1 bg-white/10 rounded-full"></span>
                    <span class="text-[10px] font-black text-indigo-100/20 uppercase tracking-widest italic">{{ $task->category ?? 'INSPECCIÓN' }}</span>
                    <span class="h-1 w-1 bg-white/10 rounded-full"></span>
                    @if(($task->source ?? 'manual') === 'contract')
                        <span class="text-[9px] font-black text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-indigo-500/20">CONTRATO</span>
                    @else
                        <span class="text-[9px] font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-emerald-500/20">SOLICITUD</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-end">
            <div class="text-right">
                <p class="text-[9px] font-black text-indigo-100/10 uppercase tracking-widest leading-none mb-1 italic">OBJETIVO</p>
                <p class="text-[13px] font-black {{ $style === 'overdue' ? 'text-red-500 glow-text-red' : ($style === 'today' ? 'text-ep-primary' : 'text-indigo-100/40') }} italic">
                    {{ $task->due_date ? (is_string($task->due_date) ? $task->due_date : $task->due_date->format('d/m/Y')) : '--/--/----' }}
                </p>
            </div>
            @php
                $category = $task->category ?? 'Inspección';
                $btnText = match($category) {
                    'Capacitación' => 'Ejecutar Capacitación',
                    'Informe' => 'Redactar Informe',
                    default => 'Ejecutar Inspección'
                };
                $btnRoute = route('tecnico.visits.index', [
                    'sede_id' => $task->sede->id,
                    'task_id' => $task->id ?? $task->dynamic_checklist_id,
                    'type' => $category,
                    'source' => $task->source ?? 'manual'
                ]);
            @endphp
            <a href="{{ $btnRoute }}" 
               class="px-8 h-12 flex items-center justify-center {{ $style === 'overdue' ? 'bg-red-600' : 'bg-ep-primary' }} text-white text-[10px] font-black uppercase tracking-widest italic rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-ep-primary/20 group/btn relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                <span class="relative">{{ $btnText }}</span>
            </a>
        </div>
    </div>
</div>
