<div>
    @if($mode === 'create')
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sede / Cliente</label>
                    <select wire:model.live="sede_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Seleccione una sede</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nombre_sede }} ({{ $sede->empresa->nombre }})</option>
                        @endforeach
                    </select>
                    @error('sede_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Técnico Responsable</label>
                    <select wire:model="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Seleccione un técnico</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}">{{ $tecnico->nombres }} {{ $tecnico->apellidos }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Título del Informe</label>
                    <input wire:model="titulo" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Periodo (Mes/Año)</label>
                    <input wire:model.live="periodo" type="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    @error('periodo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-700">Solicitar Informe</button>
            </div>
        </form>
    @else
        <div class="space-y-8">
            {{-- Encabezado de Contexto --}}
            <div class="bg-ep-dark p-6 rounded-lg border-l-8 border-ep-primary shadow-xl">
                <h3 class="text-2xl font-bold text-white uppercase tracking-wider">{{ $report->titulo }}</h3>
                <div class="flex items-center mt-2 text-ep-text space-x-4">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ \Carbon\Carbon::parse($report->periodo)->translatedFormat('F Y') }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $report->sede->nombre_sede }}
                    </span>
                </div>
            </div>

            {{-- Formulario de Redacción --}}
            <form wire:submit.prevent="save" class="space-y-10">
                
                {{-- SECCIÓN 1: VISITAS TÉCNICAS --}}
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-orange-100">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold mr-3 text-sm">1</div>
                            <h4 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Visitas ({{ count($monthlyData['visits'] ?? []) }} Realizadas)</h4>
                        </div>
                    </div>
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-600">
                                <tr>
                                    <th class="px-4 py-2 text-left">Fecha</th>
                                    <th class="px-4 py-2 text-left">Referencia / Código</th>
                                    <th class="px-4 py-2 text-left">Técnico</th>
                                    <th class="px-4 py-2 text-left">Observaciones de Visita</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($monthlyData['visits'] ?? [] as $visit)
                                    <tr>
                                        <td class="px-4 py-3 font-medium">{{ $visit->visit_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-gray-500">#{{ $visit->id }} - {{ $visit->code }}</td>
                                        <td class="px-4 py-3">{{ $visit->user->nombres }} {{ $visit->user->apellidos }}</td>
                                        <td class="px-4 py-3 truncate max-w-xs">{{ $visit->general_observations }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">No hay visitas en este periodo</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase italic">Observaciones de la Gestión de Visitas</label>
                        <textarea wire:model="observaciones_visitas" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Redacte aquí sus comentarios sobre las visitas del mes..."></textarea>
                    </div>
                </div>

                {{-- SECCIÓN 2: CHECKLISTS REALIZADOS --}}
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <div class="flex items-center mb-4 pb-2 border-b border-orange-100">
                        <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold mr-3 text-sm">2</div>
                        <h4 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Checklists ({{ count($monthlyData['checklists'] ?? []) }} Realizados)</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        @foreach($monthlyData['checklists'] ?? [] as $completion)
                            <div class="p-3 border rounded-lg bg-gray-50 flex flex-col justify-between text-sm">
                                <div class="font-bold text-gray-800">{{ $completion->assignment->checklist->title ?? 'Checklist Ad-hoc' }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Finalizado: {{ \Carbon\Carbon::parse($completion->completed_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        @endforeach
                        @if(count($monthlyData['checklists'] ?? []) === 0)
                            <div class="col-span-full text-center py-4 text-gray-400">Sin checklists completados</div>
                        @endif
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase italic">Observaciones de los Controles (Checklists)</label>
                        <textarea wire:model="observaciones_checklists" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Comentarios sobre los resultados de las inspecciones técnicas..."></textarea>
                    </div>
                </div>

                {{-- SECCIÓN 3: CAPACITACIONES --}}
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <div class="flex items-center mb-4 pb-2 border-b border-orange-100">
                        <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold mr-3 text-sm">3</div>
                        <h4 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Capacitación ({{ count($monthlyData['trainings'] ?? []) }} Actividades)</h4>
                    </div>
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 uppercase text-xs font-bold text-gray-600">
                                <tr>
                                    <th class="px-4 py-2 text-left">Tema / Título</th>
                                    <th class="px-4 py-2 text-center">Asistentes</th>
                                    <th class="px-4 py-2 text-left">Notas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($monthlyData['trainings'] ?? [] as $training)
                                    <tr>
                                        <td class="px-4 py-3 font-medium">{{ $training->title ?: 'Sin título' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $training->attendees_count }}</td>
                                        <td class="px-4 py-3 italic text-gray-500">{{ $training->notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase italic">Observaciones de la Formación (Capacitaciones)</label>
                        <textarea wire:model="observaciones_capacitaciones" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Comentarios sobre el nivel de formación y participación del personal..."></textarea>
                    </div>
                </div>

                {{-- SECCIÓN 4: MATRIZ DE RIESGO Y COMENTARIOS --}}
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <div class="flex items-center mb-4 pb-2 border-b border-orange-100">
                        <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold mr-3 text-sm">4</div>
                        <h4 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Matriz de Riesgo e Información de la Matriz</h4>
                    </div>
                    
                    @if($monthlyData['risk_matrix'])
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                            <div class="flex items-center justify-between mb-4 border-b pb-2">
                                <div>
                                    <div class="text-xs font-bold text-gray-500 uppercase">Matriz de Riesgo Activa</div>
                                    <div class="text-lg font-bold text-ep-primary">{{ $monthlyData['risk_matrix']->title }}</div>
                                    <div class="text-sm text-gray-600">{{ $monthlyData['risk_matrix']->description }}</div>
                                </div>
                                <div class="bg-white p-2 rounded border shadow-sm">
                                    <span class="text-xs font-bold uppercase text-gray-400 block border-b mb-1">Área / Proceso</span>
                                    <span class="text-sm font-bold text-gray-700">{{ $monthlyData['risk_matrix']->area }} / {{ $monthlyData['risk_matrix']->process }}</span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full text-[10px] bg-white border rounded">
                                    <thead class="bg-gray-100 font-bold uppercase">
                                        <tr>
                                            <th class="px-3 py-2 text-left border">Peligro / Riesgo</th>
                                            <th class="px-3 py-2 text-left border">Tipo</th>
                                            <th class="px-3 py-2 text-left border">Consecuencia / Daño</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($monthlyData['risk_matrix']->risks as $risk)
                                            <tr>
                                                <td class="px-3 py-2 border font-medium text-gray-800">{{ $risk->hazard }}</td>
                                                <td class="px-3 py-2 border">{{ $risk->type }}</td>
                                                <td class="px-3 py-2 border text-gray-600 italic">{{ $risk->consequence }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400">No hay riesgos definidos en esta matriz</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-800 mb-2 uppercase italic">Evolución del Riesgo y Observaciones Técnicas</label>
                        <textarea wire:model="observaciones_matriz" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Comentarios sobre la evolución de riesgos..."></textarea>
                    </div>

                    {{-- MAPA DE CALOR REAL --}}
                    <div class="mt-8">
                        <div class="flex">
                            <div class="flex items-center [writing-mode:vertical-lr] rotate-180 mr-4">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Impacto</span>
                            </div>
                            <div class="flex-1 max-w-[500px]">
                                <div class="grid grid-cols-5 gap-1">
                                    @foreach($monthlyData['heatmap'] as $s => $probabilities)
                                        @foreach($probabilities as $p => $count)
                                            @php
                                                $score = $s * $p;
                                                $bgColor = $score >= 17 ? 'bg-red-500' : ($score >= 10 ? 'bg-orange-500' : ($score >= 5 ? 'bg-yellow-500' : 'bg-green-500'));
                                            @endphp
                                            <div class="aspect-square flex items-center justify-center rounded-sm text-white font-bold text-lg {{ $bgColor }} {{ $count == 0 ? 'opacity-20' : '' }}">
                                                {{ $count ?: '' }}
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-5 gap-1 mt-2 text-center">
                                    @for($i=1; $i<=5; $i++)
                                        <div class="text-[8px] font-bold text-gray-400 uppercase">P{{ $i }}</div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 5: HALLAZGOS e INCIDENTES (debajo de Matriz) --}}
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <div class="flex items-center mb-4 pb-2 border-b border-orange-100">
                        <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center text-white font-bold mr-3 text-sm">5</div>
                        <h4 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Análisis de Hallazgos e Incidentes</h4>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Hallazgos --}}
                        <div class="bg-white p-4 rounded-lg border border-l-4 border-l-yellow-400">
                            <h5 class="font-bold text-gray-800 uppercase mb-3 text-sm">Hallazgos Detectados ({{ count($monthlyData['hallazgos'] ?? []) }})</h5>
                            <ul class="space-y-3">
                                @forelse($monthlyData['hallazgos'] ?? [] as $hallazgo)
                                    <li class="text-xs border-b pb-2">
                                        <div class="flex justify-between font-bold">
                                            <span>#{{ $hallazgo->id }} - {{ $hallazgo->titulo }}</span>
                                            <span class="px-1 rounded bg-yellow-100 text-yellow-800">{{ $hallazgo->criticidad }}</span>
                                        </div>
                                        <div class="text-gray-600 mt-1">{{ $hallazgo->descripcion }}</div>
                                    </li>
                                @empty
                                    <li class="text-xs text-gray-400">Sin hallazgos reportados en este periodo.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Incidentes --}}
                        <div class="bg-white p-4 rounded-lg border border-l-4 border-l-red-500">
                            <h5 class="font-bold text-gray-800 uppercase mb-3 text-sm">Incidentes Registrados ({{ count($monthlyData['incidentes'] ?? []) }})</h5>
                            <ul class="space-y-3">
                                @forelse($monthlyData['incidentes'] ?? [] as $incidente)
                                    <li class="text-xs border-b pb-2">
                                        <div class="flex justify-between font-bold">
                                            <span>{{ $incidente->tipo_evento }}</span>
                                            <span class="text-red-600">{{ \Carbon\Carbon::parse($incidente->fecha_hora)->format('d/m/y') }}</span>
                                        </div>
                                        <div class="text-gray-600 mt-1">{{ $incidente->descripcion }}</div>
                                    </li>
                                @empty
                                    <li class="text-xs text-gray-400">Sin incidentes reportados en este periodo.</li>
                                @endforelse
                            </ul>
                        </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase italic">Observaciones de Hallazgos e Incidentes</label>
                        <textarea wire:model="observaciones_hallazgos_incidentes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" placeholder="Comentarios sobre los desvíos y eventos detectados..."></textarea>
                    </div>
                </div>

                {{-- SECCIÓN 6: RESUMEN EJECUTIVO (Debajo de TODO) --}}
                <div class="bg-ep-dark p-8 rounded-lg shadow-2xl border-t-8 border-ep-primary">
                    <div class="flex items-center mb-6 pb-2 border-b border-gray-700">
                        <div class="w-10 h-10 bg-ep-primary rounded-full flex items-center justify-center text-white font-black mr-4 text-xl">6</div>
                        <h4 class="text-xl font-black text-white uppercase tracking-widest">Observación General (Resumen Ejecutivo Final)</h4>
                    </div>
                    <textarea wire:model="resumen_ejecutivo" rows="8" class="w-full rounded-md bg-gray-800 border-gray-600 text-white placeholder-gray-500 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-lg p-6" placeholder="Redacte aquí el resumen técnico final de la gestión del mes..."></textarea>
                    @error('resumen_ejecutivo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end pt-10 border-t-2 border-gray-100 pb-12">
                    <button type="submit" class="bg-ep-primary text-white px-12 py-4 rounded-xl font-black text-lg hover:bg-ep-primary-dark shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        GENERAR INFORME
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
