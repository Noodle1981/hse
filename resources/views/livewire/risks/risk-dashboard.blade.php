<div class="py-6">
    @php
        $prefix = auth()->user()->isAdmin() ? 'gestor.' : 'tecnico.';
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                    @if($matrixId)
                        {{ \App\Models\RiskMatrix::find($matrixId)->title }}
                    @else
                        Mapa de Calor Global
                    @endif
                </h1>
                <p class="text-gray-500 mt-1">Resumen estadístico de criticidad operativa basado en ISO 31000.</p>
            </div>
            <div class="flex items-center space-x-3">
                <select wire:model.live="matrixId" class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm">
                    <option value="">Vista Global (Todas)</option>
                    @foreach($matrices as $m)
                        <option value="{{ $m->id }}">{{ $m->title }} ({{ $m->sede->nombre_sede }})</option>
                    @endforeach
                </select>
                <button wire:click="loadData" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 shadow-sm transition-all focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Actualizar
                </button>
                <a href="{{ route($prefix . 'risk-matrices.dashboard.pdf', ['matrix_id' => $matrixId]) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 shadow-sm transition-all focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar PDF
                </a>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Riesgos</div>
                <div class="text-3xl font-black text-gray-900">{{ $kpis['total_risks'] }}</div>
            </div>
            <div class="bg-red-50 p-6 rounded-xl shadow-sm border border-red-100">
                <div class="text-[10px] font-bold text-red-400 uppercase tracking-widest mb-1">Críticos</div>
                <div class="text-3xl font-black text-red-600">{{ $kpis['critical'] }}</div>
            </div>
            <div class="bg-orange-50 p-6 rounded-xl shadow-sm border border-orange-100">
                <div class="text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-1">Altos</div>
                <div class="text-3xl font-black text-orange-600">{{ $kpis['high'] }}</div>
            </div>
            <div class="bg-yellow-50 p-6 rounded-xl shadow-sm border border-yellow-100">
                <div class="text-[10px] font-bold text-yellow-500 uppercase tracking-widest mb-1">Medios</div>
                <div class="text-3xl font-black text-yellow-600">{{ $kpis['medium'] }}</div>
            </div>
            <div class="bg-green-50 p-6 rounded-xl shadow-sm border border-green-100">
                <div class="text-[10px] font-bold text-green-400 uppercase tracking-widest mb-1">Bajos</div>
                <div class="text-3xl font-black text-green-600">{{ $kpis['low'] }}</div>
            </div>
            <div class="{{ $kpis['alert_triggers'] > 0 ? 'bg-red-600' : 'bg-white' }} p-6 rounded-xl shadow-sm border {{ $kpis['alert_triggers'] > 0 ? 'border-red-700' : 'border-gray-100 shadow-lg' }} transition-all">
                <div class="text-[10px] font-bold {{ $kpis['alert_triggers'] > 0 ? 'text-white/70' : 'text-gray-400' }} uppercase tracking-widest mb-1">Disparadores Activos</div>
                <div class="text-3xl font-black {{ $kpis['alert_triggers'] > 0 ? 'text-white' : 'text-gray-900' }}">{{ $kpis['alert_triggers'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            {{-- Heatmap Matrix --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex mb-4">
                    {{-- Y Axis Label --}}
                    <div class="flex items-center [writing-mode:vertical-lr] rotate-180 mr-4">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Severidad / Impacto</span>
                    </div>
                    
                    <div class="flex-1">
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($heatmapData as $s => $probabilities)
                                @foreach($probabilities as $p => $count)
                                    <div class="relative group aspect-video md:aspect-square flex items-center justify-center rounded-lg shadow-sm border-2 border-white {{ $this->getCellColor($s, $p) }} transition-all hover:scale-105 cursor-default">
                                        <span class="text-2xl font-black text-white drop-shadow-md @if($count == 0) opacity-20 @endif">{{ $count }}</span>
                                        
                                        {{-- Tooltip info --}}
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-32 hidden group-hover:block z-30 transition-all">
                                            <div class="bg-gray-900 text-white text-[10px] p-2 rounded shadow-xl leading-snug">
                                                <div class="font-bold border-b border-gray-700 pb-1 mb-1">P:{{$p}} x S:{{$s}} (Score: {{$s*$p}})</div>
                                                <div>{{ $count }} riesgos identificados en esta zona.</div>
                                            </div>
                                            <div class="w-2 h-2 bg-gray-900 rotate-45 mx-auto -mt-1"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                        
                        {{-- X Axis Labels --}}
                        <div class="grid grid-cols-5 gap-2 mt-4 text-center">
                            @for($i=1; $i<=5; $i++)
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Prob. {{ $i }}</div>
                            @endfor
                        </div>
                        <div class="text-center mt-6">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Probabilidad de Ocurrencia</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Legend / Instructions --}}
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Leyenda de Criticidad
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-2 rounded-lg bg-red-50">
                            <span class="flex items-center text-xs font-bold text-red-700"><div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div> Crítico</span>
                            <span class="text-[10px] text-red-400 font-bold">17 - 25</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-orange-50">
                            <span class="flex items-center text-xs font-bold text-orange-700"><div class="w-3 h-3 rounded-full bg-orange-500 mr-2"></div> Alto</span>
                            <span class="text-[10px] text-orange-400 font-bold">10 - 16</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-yellow-50">
                            <span class="flex items-center text-xs font-bold text-yellow-700"><div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div> Medio</span>
                            <span class="text-[10px] text-yellow-400 font-bold">5 - 9</span>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg bg-green-50">
                            <span class="flex items-center text-xs font-bold text-green-700"><div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div> Bajo</span>
                            <span class="text-[10px] text-green-400 font-bold">1 - 4</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-800 text-white">
                    <h3 class="font-bold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Acción Requerida
                    </h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4">
                        Los riesgos situados en la zona **Crítica** requieren intervención inmediata y suspensión de actividades si no hay controles implementados.
                    </p>
                    <a href="{{ route($prefix . 'risk-matrices.index') }}" class="block text-center py-2 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-sm font-bold transition-colors">
                        Revisar Plantillas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
