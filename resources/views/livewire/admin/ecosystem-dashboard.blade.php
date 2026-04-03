<div class="min-h-screen bg-[#11111d] p-4 lg:p-8">
    {{-- Tactical Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] mb-3">
                <span class="w-8 h-[1px] bg-ep-primary"></span>
                <span>Ecosistema XamaSafe v4.0</span>
            </div>
            <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                Sincronización <span class="text-ep-primary shadow-[0_0_20px_#4338ca/30]">Centro de Mando</span>
            </h2>
            <p class="text-white/40 mt-2 font-medium tracking-wide italic">Consolidado inteligente de cumplimiento, seguridad técnica y reporte pericial.</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="px-5 py-3 bg-white/5 border border-white/10 rounded-2xl flex items-center shadow-2xl backdrop-blur-md">
                <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse mr-3 shadow-[0_0_10px_#22c55e]"></div>
                <span class="text-[10px] font-black text-white uppercase tracking-widest leading-none">Sistema Global Operativo</span>
            </div>
        </div>
    </div>

    {{-- Stats HUD Matrix --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        {{-- Card: Nodos --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group hover:border-ep-primary/30 transition-all duration-500">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-ep-primary/5 rounded-full blur-3xl group-hover:scale-150 transition-transform"></div>
            <p class="text-[9px] font-black text-white/30 uppercase tracking-[.25em] mb-4 italic">Frentes Corporativos</p>
            <div class="flex items-end justify-between">
                <div class="text-5xl font-black text-white italic tracking-tighter">{{ $stats['total_companies'] }}</div>
                <div class="w-10 h-10 rounded-xl bg-ep-primary/10 flex items-center justify-center text-ep-primary border border-ep-primary/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>

        {{-- Card: Visitas --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group hover:border-green-500/30 transition-all duration-500">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-green-500/5 rounded-full blur-3xl group-hover:scale-150 transition-transform"></div>
            <p class="text-[9px] font-black text-white/30 uppercase tracking-[.25em] mb-4 italic">Misión Crítica (Visitas)</p>
            <div class="flex items-end justify-between">
                <div class="text-5xl font-black text-white italic tracking-tighter">{{ $stats['total_visits'] }}</div>
                <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-400 border border-green-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
        </div>

        {{-- Card: Inteligencia (Hallazgos/Incidentes) --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group hover:border-red-500/30 transition-all duration-500">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-red-500/5 rounded-full blur-3xl group-hover:scale-150 transition-transform"></div>
            <p class="text-[9px] font-black text-white/30 uppercase tracking-[.25em] mb-4 italic">Seguridad & Alertas</p>
            <div class="flex items-end justify-between">
                <div class="flex flex-col">
                    <div class="text-3xl font-black text-white italic tracking-tighter">{{ $stats['total_findings'] }} <span class="text-[10px] text-white/20 uppercase">H</span></div>
                    <div class="text-3xl font-black text-red-500 italic tracking-tighter">{{ $stats['total_incidents'] }} <span class="text-[10px] text-red-500/40 uppercase">I</span></div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>

        {{-- Card: Reportes --}}
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group hover:border-blue-500/30 transition-all duration-500">
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-blue-500/5 rounded-full blur-3xl group-hover:scale-150 transition-transform"></div>
            <p class="text-[9px] font-black text-white/30 uppercase tracking-[.25em] mb-4 italic">Informes Generados</p>
            <div class="flex items-end justify-between">
                <div class="text-5xl font-black text-white italic tracking-tighter">{{ $stats['total_reports'] }}</div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Activity Matrix --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[3rem] shadow-2xl relative overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-ep-primary/5 rounded-full blur-[120px]"></div>
        
        <div class="px-10 py-8 border-b border-white/10 flex items-center justify-between relative z-10 bg-black/20">
            <div>
                <h3 class="text-2xl font-black text-white uppercase italic tracking-tighter">Matriz de Inteligencia de Nodos</h3>
                <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mt-1 italic">Detección de patrones operativos y cumplimiento contractual</p>
            </div>
            <div class="flex items-center space-x-6 text-[10px] font-black uppercase italic tracking-widest text-white/40">
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-ep-primary mr-2"></span> Gestión</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Seguridad</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Reporte</div>
            </div>
        </div>

        <div class="overflow-x-auto relative z-10">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-black/10 text-left border-b border-white/5">
                        <th class="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.4em]">Identidad Nodo</th>
                        <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] text-center bg-white/[0.02]">Gestión Tecn.</th>
                        <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] text-center bg-red-500/[0.02]">Seguridad Crit.</th>
                        <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] text-center bg-blue-500/[0.02]">Auditoría</th>
                        <th class="px-8 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] text-center bg-yellow-500/[0.02]">Estatus Comercial</th>
                        <th class="px-10 py-6 text-right text-[10px] font-black text-white/20 uppercase tracking-[0.3em]">Comando</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($companies as $company)
                    <tr class="group/row hover:bg-white/[0.03] transition-all duration-300">
                        {{-- Identidad --}}
                        <td class="px-10 py-8">
                            <div class="flex items-center">
                                <div class="relative w-14 h-14 rounded-2xl bg-black/60 border border-white/10 flex items-center justify-center p-2 group-hover/row:border-ep-primary/40 transition-colors">
                                    @if($company->logo_path)
                                        <img src="{{ asset('storage/' . $company->logo_path) }}" class="max-h-full w-auto object-contain">
                                    @else
                                        <span class="text-white font-black italic text-xl">{{ substr($company->nombre, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="ml-6">
                                    <div class="text-lg font-black text-white uppercase italic tracking-tighter group-hover/row:text-ep-primary transition-colors">{{ $company->nombre }}</div>
                                    <div class="text-[9px] font-bold text-white/20 uppercase tracking-widest">{{ $company->cuit }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Gestión --}}
                        <td class="px-8 py-8 text-center bg-white/[0.01]">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl font-black text-white italic tracking-tighter">{{ $company->checklist_assignments_count }}</span>
                                    <span class="text-[7px] font-black text-white/30 uppercase">Checklists</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-black text-indigo-400 italic tracking-tighter">{{ $company->visit_trainings_count }}</span>
                                    <span class="text-[7px] font-black text-indigo-400/40 uppercase">Capacitaciones</span>
                                </div>
                            </div>
                        </td>

                        {{-- Seguridad --}}
                        <td class="px-8 py-8 text-center bg-red-500/[0.01]">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl font-black {{ $company->hallazgos_count > 0 ? 'text-white' : 'text-white/10' }} italic tracking-tighter">{{ $company->hallazgos_count }}</span>
                                    <span class="text-[7px] font-black text-white/30 uppercase">Hallazgos</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-black {{ $company->incidentes_count > 0 ? 'text-red-500' : 'text-red-500/10' }} italic tracking-tighter">{{ $company->incidentes_count }}</span>
                                    <span class="text-[7px] font-black text-red-500/40 uppercase">Incidentes</span>
                                </div>
                            </div>
                        </td>

                        {{-- Reporte --}}
                        <td class="px-8 py-8 text-center bg-blue-500/[0.01]">
                            <div class="inline-flex flex-col items-center">
                                <span class="text-2xl font-black text-blue-400 italic shadow-[0_0_15px_#60a5fa30]">{{ $company->informes_count }}</span>
                                <div class="text-[8px] font-black text-blue-400/40 uppercase tracking-[0.2em] mt-1">Informes</div>
                            </div>
                        </td>

                        {{-- Comercial --}}
                        <td class="px-8 py-8 text-center bg-yellow-500/[0.01]">
                            <div class="flex flex-col items-center gap-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl font-black text-white italic tracking-tighter">{{ $company->sedes_con_plan_count }}</span>
                                    <span class="text-[8px] font-black text-yellow-500/60 uppercase tracking-widest italic">Contratos</span>
                                </div>
                                @php $totalLibre = $company->checklists_libres_count + $company->trainings_libres_count; @endphp
                                <div class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg group/status">
                                    <span class="text-[9px] font-black {{ $totalLibre > 0 ? 'text-ep-primary animate-pulse' : 'text-white/10' }} uppercase tracking-widest">
                                        {{ $totalLibre }} Operaciones Libres
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Comando --}}
                        <td class="px-10 py-8 text-right whitespace-nowrap">
                            <a href="{{ route('admin.consultants') }}" class="inline-flex items-center px-5 py-3 bg-white border border-white/10 text-black text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-ep-primary hover:text-white hover:border-ep-primary transition-all shadow-xl group/btn">
                                Entrar Nodo
                                <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-10 py-6 bg-black/40 border-t border-white/5 flex items-center justify-between relative z-10">
            <div class="flex items-center space-x-4">
                <span class="text-[8px] font-black text-white/20 uppercase tracking-[0.3em]">Data Link: Sincronizado</span>
                <div class="w-1.5 h-1.5 rounded-full bg-ep-primary"></div>
                <span class="text-[8px] font-black text-white/20 uppercase tracking-[0.3em]">Cifrado: End-to-End</span>
            </div>
            <div class="text-[9px] font-bold text-white/10 uppercase tracking-widest italic">XamaSafe Command Terminal © 2026</div>
        </div>
    </div>
</div>

</div>

