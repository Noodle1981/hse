<div>
    {{-- Preview Header Premium --}}
    @section('header_custom')
        <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg border-b border-white/5 print:hidden">
            <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-ep-primary shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-[10px] font-black text-ep-primary uppercase tracking-[0.2em] italic leading-none mb-1">
                            Laboratorio de <span class="text-white">Emulación</span>
                        </h1>
                        <h2 class="text-xs font-bold text-indigo-100 uppercase tracking-widest italic truncate max-w-[200px] sm:max-w-md">
                            {{ $checklist->title }}
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button wire:click="back" 
                            class="px-5 py-2 text-[10px] font-black text-white bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all uppercase tracking-widest italic flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"/></svg>
                        Regresar
                    </button>
                    <button onclick="window.print()" 
                            class="hidden sm:flex px-5 py-2 text-[10px] font-black text-white bg-ep-primary rounded-xl shadow-lg shadow-ep-primary/20 hover:brightness-110 active:scale-95 transition-all uppercase tracking-widest italic items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m24 0v4a2 2 0 01-2 2H7a2 2 0 01-2-2v-4m14-8V4a2 2 0 00-2-2H9a2 2 0 00-2 2v4m14 0H9"/></svg>
                        Exportar
                    </button>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-ep-primary/30 to-transparent"></div>
        </header>
    @endsection

    <div class="py-12 min-h-screen bg-[#11111d] text-white selection:bg-ep-primary/30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Mini Laboratorio Banner --}}
            <div class="mb-12 relative overflow-hidden bg-ep-dark border border-indigo-500/30 p-8 rounded-[2.5rem] shadow-[0_0_50px_rgba(79,70,229,0.1)] group animate-fade-in print:hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-8 z-10">
                    <div class="flex items-center gap-6">
                        <div class="p-4 bg-indigo-500/10 rounded-2xl border border-indigo-500/20 text-indigo-400 shadow-inner rotate-3 group-hover:rotate-0 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-indigo-400 uppercase tracking-[0.4em] italic mb-1">Mini Laboratorio XamaSafe</h3>
                            <p class="text-white text-lg font-black uppercase italic tracking-tight leading-none mb-2">Entorno de Simulación Técnica</p>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[8px] font-black rounded uppercase tracking-widest border border-emerald-500/20 italic">Visualización Pre-Asignación</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-xs">
                        <p class="text-[10px] font-bold text-indigo-100/40 uppercase italic tracking-widest leading-loose">
                            Interactúe con los controles para validar la lógica de campos críticos y fallos de auditoría antes de consolidar el protocolo.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Main Laboratory Container --}}
            <div class="bg-ep-dark rounded-[3rem] shadow-[0_40px_80px_-15px_rgba(0,0,0,0.6)] border border-white/10 overflow-hidden relative group transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none opacity-30"></div>
                
                <div class="p-8 sm:p-14 relative z-20">
                    {{-- Intro block style --}}
                    <div class="text-center mb-20 animate-fade-in">
                        <h1 class="text-3xl sm:text-5xl font-black text-white italic uppercase tracking-tighter mb-4 leading-none">
                            {{ $checklist->title }}
                        </h1>
                        <div class="flex items-center justify-center gap-4">
                            <div class="h-px w-20 bg-gradient-to-r from-transparent via-ep-primary/40 to-transparent"></div>
                            <span class="text-[10px] font-black text-ep-primary uppercase tracking-[0.4em] italic">{{ $checklist->registry_code ?? 'Protocolo Maestro' }}</span>
                            <div class="h-px w-20 bg-gradient-to-r from-transparent via-ep-primary/40 to-transparent"></div>
                        </div>
                        @if($checklist->description)
                            <p class="max-w-2xl mx-auto mt-8 text-xs font-bold text-indigo-100/40 uppercase tracking-widest leading-loose italic">
                                {{ $checklist->description }}
                            </p>
                        @endif
                    </div>

                    <div class="space-y-20">
                        {{-- Questions Grouped by Section --}}
                        @foreach($groupedQuestions as $sectionId => $questions)
                            @php $section = $questions->first()->section; @endphp
                            <div class="space-y-10 animate-fade-in-up">
                                <div class="flex items-center gap-5">
                                    <div class="w-1.5 h-10 bg-gradient-to-b from-ep-primary to-orange-600 rounded-full shadow-[0_0_15px_rgba(246,146,57,0.4)]"></div>
                                    <div>
                                        <h2 class="text-lg font-black text-white uppercase italic tracking-[0.2em] mb-1">
                                            {{ $section ? $section->title : 'Registro General' }}
                                        </h2>
                                        @if($section && $section->description)
                                            <p class="text-[10px] font-bold text-indigo-100/30 uppercase italic tracking-[0.2em]">{{ $section->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-6">
                                    @foreach($questions as $question)
                                        @include('livewire.checklists.partials._emulator-question', ['question' => $question])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        {{-- Unsectioned questions block --}}
                        @if($unsectionedQuestions->count() > 0)
                            <div class="space-y-10 pt-10 border-t border-white/5 animate-fade-in-up">
                                <div class="flex items-center gap-5">
                                    <div class="w-1.5 h-10 bg-white/10 rounded-full"></div>
                                    <h2 class="text-lg font-black text-white/40 uppercase italic tracking-[0.2em]">Puntos de Control Varios</h2>
                                </div>
                                <div class="grid grid-cols-1 gap-6">
                                    @foreach($unsectionedQuestions as $question)
                                        @include('livewire.checklists.partials._emulator-question', ['question' => $question])
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Mockup of Final Step: Protocols and Signatures --}}
                        <div class="pt-20 border-t border-white/10 mt-20 space-y-12">
                            <div class="text-center space-y-4">
                                <h3 class="text-sm font-black text-ep-primary uppercase tracking-[0.4em] italic">Validación Final de Auditoría</h3>
                                <div class="h-1 w-20 bg-ep-primary/20 mx-auto rounded-full"></div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                                {{-- Firma Supervisor Simulator --}}
                                <div class="bg-black/40 p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden group/sign hover:border-ep-primary/20 transition-all duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/5 to-transparent opacity-0 group-hover/sign:opacity-100 transition-opacity"></div>
                                    <div class="relative z-10">
                                        <div class="flex justify-between items-center mb-6">
                                            <span class="text-[9px] font-black text-indigo-100/30 uppercase tracking-[0.3em] italic">Captura Firma Digital</span>
                                            <span class="text-[8px] font-black text-white/5 uppercase tracking-widest italic bg-white/5 px-2 py-0.5 rounded">EP-SIGN-001</span>
                                        </div>
                                        <div class="h-40 border-2 border-dashed border-white/5 rounded-3xl flex items-center justify-center group-hover/sign:border-ep-primary/10 transition-all">
                                            <div class="text-center opacity-20 group-hover/sign:opacity-40 transition-all">
                                                <svg class="h-8 w-8 mx-auto text-indigo-100 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                <p class="text-[9px] font-black uppercase tracking-widest italic">Panel Bloqueado en Emulador</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Audit Confirmation Simulator --}}
                                <div class="bg-black/40 p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden group/sign hover:border-ep-primary/20 transition-all duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-br from-ep-primary/5 to-transparent opacity-0 group-hover/sign:opacity-100 transition-opacity"></div>
                                    <div class="relative z-10">
                                        <div class="flex justify-between items-center mb-6">
                                            <span class="text-[9px] font-black text-indigo-100/30 uppercase tracking-[0.3em] italic">Validación de Especialista</span>
                                            <span class="text-[8px] font-black text-white/5 uppercase tracking-widest italic bg-white/5 px-2 py-0.5 rounded">EP-AUTH-001</span>
                                        </div>
                                        <div class="flex flex-col gap-4 h-40 justify-center">
                                            <div class="p-5 bg-white/5 rounded-2xl border border-white/5">
                                                <p class="text-[8px] font-black text-indigo-100/20 uppercase tracking-widest mb-1 italic">Operador Actual</p>
                                                <p class="text-xs font-black text-white uppercase italic tracking-widest">{{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}</p>
                                            </div>
                                            <div class="mt-auto">
                                                <button disabled class="w-full py-4 bg-emerald-600/10 border border-emerald-600/20 rounded-2xl text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] italic opacity-60 flex items-center justify-center gap-3">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Finalizar Simulación
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Technical Hud --}}
            <div class="mt-16 text-center animate-fade-in">
                <div class="inline-flex items-center gap-3 px-6 py-2 bg-white/5 rounded-full border border-white/5 mb-4">
                    <span class="text-[9px] font-black text-indigo-100/20 uppercase tracking-[0.3em] italic">XamaSafe Laboratory Systems • Ver. 4.8.0</span>
                </div>
                <div class="flex justify-center gap-10 mt-2 opacity-5 scale-75 select-none grayscale">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e0/Placeholder_logo.svg" class="h-8" alt="">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e0/Placeholder_logo.svg" class="h-8" alt="">
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        .animate-fade-in-up { animation: fadeInUp 1s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        
        [x-cloak] { display: none !important; }

        @media print {
            header, .mb-10, .pt-20, .mt-16 { display: none !important; }
            .bg-\[\#11111d\] { background: white !important; padding: 0 !important; }
            .bg-ep-dark { background: white !important; color: black !important; border: none !important; box-shadow: none !important; }
            .text-white, .text-indigo-100\/40, .text-ep-primary { color: black !important; }
            .rounded-\[3rem\] { border-radius: 0 !important; }
            [class*="shadow-"] { box-shadow: none !important; }
        }
    </style>
</div>
