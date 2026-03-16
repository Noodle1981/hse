<div class="py-6">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Executivo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <svg class="w-32 h-32 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h2 class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Contrato Anual Higiene y Seguridad</h2>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight">Tablero de Cumplimiento {{ $year }}</h1>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select wire:model.live="sedeId" class="rounded-xl border-gray-200 text-sm font-bold text-gray-700 focus:ring-indigo-500 min-w-[200px] shadow-sm">
                        @foreach($sedes as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre_sede }}</option>
                        @endforeach
                    </select>

                    @if(auth()->user()->isAdmin())
                    <button wire:click="$toggle('editMode')" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm flex items-center {{ $editMode ? 'bg-indigo-600 text-white shadow-indigo-100' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ $editMode ? 'Finalizar Edición' : 'Planificar Actividades' }}
                    </button>
                    @endif
                    
                    @if(!$editMode && $sedeId)
                    <button wire:click="downloadPdf" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-black transition-all shadow-lg flex items-center group">
                        <svg class="w-4 h-4 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Exportar Reporte PDF
                    </button>
                    @endif
                </div>
            </div>

            {{-- Legend --}}
            <div class="mt-8 flex flex-wrap items-center gap-6 text-[10px] uppercase font-bold tracking-tighter text-gray-400">
                <span class="flex items-center"><div class="w-3 h-3 rounded bg-gray-100 border border-gray-200 mr-2"></div> No Planificado</span>
                <span class="flex items-center"><div class="w-3 h-3 rounded bg-yellow-400 mr-2"></div> Planificado (Pendiente)</span>
                <span class="flex items-center"><div class="w-3 h-3 rounded bg-emerald-500 mr-2"></div> Cumplimiento ✅</span>
                <span class="flex items-center"><div class="w-3 h-3 rounded bg-red-500 mr-2 shadow-sm"></div> Vencimiento / Incumplimiento 🔴</span>
            </div>
        </div>

        {{-- Formulario de Planificación (Solo en editMode y Admin) --}}
        @if($editMode && auth()->user()->isAdmin())
        <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-6 mb-8 animate-in fade-in duration-500">
            <h3 class="text-sm font-bold text-indigo-700 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Programar Nueva Actividad Anual
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Categoría</label>
                    <select wire:model.live="category" class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500">
                        <option value="Capacitación">Capacitación (E)</option>
                        <option value="Inspección">Inspección (I)</option>
                        <option value="Informe">Informe (M/B/T)</option>
                    </select>
                </div>

                @if($category === 'Informe')
                <div class="md:col-span-1 animate-in fade-in slide-in-from-left-4 duration-300">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Frecuencia</label>
                    <select wire:model="frequency" class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500 font-bold text-indigo-700 bg-indigo-50">
                        <option value="none">Manual</option>
                        <option value="mensual">Mensual (12)</option>
                        <option value="bimestral">Bimestral (6)</option>
                        <option value="trimestral">Trimestral (4)</option>
                    </select>
                </div>
                @else
                <div class="md:col-span-1 animate-in fade-in slide-in-from-right-4 duration-300">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Checklist Vinculado <span class="text-indigo-400">(auto-rellena campos)</span></label>
                    <select wire:model.live="checklist_id" class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500">
                        <option value="">— Seleccionar checklist —</option>
                        @foreach($checklists as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="md:col-span-{{ $category === 'Informe' ? '1' : '1' }}">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">
                        Nombre Actividad
                        @if($activity_name && $category !== 'Informe') <span class="text-emerald-500 normal-case font-normal">✓ auto</span> @endif
                    </label>
                    <input type="text" wire:model="activity_name" placeholder="{{ $category === 'Informe' ? 'Ej: Informe Mensual' : 'Ej: Control Matafuegos' }}" class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500 {{ $activity_name && $category !== 'Informe' ? 'bg-emerald-50 border-emerald-200' : '' }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">
                        Código Interno
                        @if($activity_code && $category !== 'Informe') <span class="text-emerald-500 normal-case font-normal">✓ auto</span> @endif
                    </label>
                    <div class="flex gap-4">
                        <input type="text" wire:model="activity_code" placeholder="Ej: EP-REG-001" class="w-full rounded-lg border-gray-200 text-sm focus:ring-indigo-500 {{ $activity_code && $category !== 'Informe' ? 'bg-emerald-50 border-emerald-200' : '' }}">
                        <button wire:click="addActivity" class="w-full py-2 bg-indigo-600 text-white font-bold rounded-lg text-sm hover:bg-indigo-700 transition-all shadow-md whitespace-nowrap px-4">
                            Añadir
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Master Board Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <!-- Desktop Table (visible from md) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="sticky left-0 z-20 bg-gray-50 px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest min-w-[300px]">Actividades del Plan Anual</th>
                            @foreach($months as $name => $num)
                                <th class="px-2 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest min-w-[70px]">
                                    {{ substr(strtoupper($name), 0, 3) }}
                                </th>
                            @endforeach
                            @if($editMode) <th class="px-6 py-4 text-right"></th> @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php $currentCategory = ''; @endphp
                        @forelse($plans as $plan)
                            @if($currentCategory !== $plan->category)
                                <tr class="bg-gray-50/50">
                                    <td colspan="14" class="px-6 py-2 text-[10px] font-black text-indigo-400 uppercase tracking-wider">
                                        {{ $plan->category }}
                                    </td>
                                </tr>
                                @php $currentCategory = $plan->category; @endphp
                            @endif
                                <tr class="group hover:bg-gray-50/80 transition-colors">
                                <td class="sticky left-0 z-10 bg-white group-hover:bg-gray-50 px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full mr-3 {{ $plan->category === 'Capacitación' ? 'bg-blue-400' : ($plan->category === 'Inspección' ? 'bg-orange-400' : 'bg-indigo-600') }}"></div>
                                        <div>
                                            <div class="text-xs font-bold text-gray-800">{{ $plan->activity_name }}</div>
                                            <div class="text-[9px] text-gray-400 font-medium tracking-tighter">{{ $plan->activity_code ?: 'S/C' }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                @foreach($months as $name => $num)
                                    @php 
                                        $data = $complianceData[$plan->id][$name] ?? ['status' => 'none', 'count' => 0];
                                        $status = $data['status'];
                                        $cnt = $data['count'];
                                    @endphp
                                    <td class="px-1 py-4 text-center">
                                        <div 
                                            @if($editMode) wire:click="toggleMonth({{ $plan->id }}, '{{ $name }}')" @endif
                                            class="relative mx-auto w-8 h-8 md:w-10 md:h-10 rounded-xl flex items-center justify-center text-xs font-black transition-all border-2 
                                            {{ $status === 'completed' ? 'bg-emerald-500 border-white text-white shadow-sm ring-1 ring-emerald-100 scale-110' : '' }}
                                            {{ $status === 'overdue' ? 'bg-red-500 border-white text-white shadow-md animate-pulse' : '' }}
                                            {{ $status === 'pending' ? 'bg-yellow-400 border-white text-white shadow-sm ring-1 ring-yellow-100' : '' }}
                                            {{ $status === 'none' ? 'bg-gray-50 border-gray-100 text-gray-200' : '' }}
                                            {{ $editMode ? 'cursor-pointer hover:scale-105 active:scale-95' : 'cursor-default' }}"
                                            title="{{ $cnt > 0 ? "$cnt ejecuciones registradas" : '' }}">
                                            
                                            @if($status !== 'none')
                                                @if($status === 'completed')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    @if($cnt > 1)
                                                        <span class="absolute -top-1 -right-1 bg-white text-emerald-600 text-[8px] px-1 rounded-full border border-emerald-100 shadow-sm">{{ $cnt }}</span>
                                                    @endif
                                                @else
                                                    X
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
    
                                @if($editMode)
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="deleteActivity({{ $plan->id }})" class="text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-6 py-20 text-center">
                                    <div class="text-gray-400 text-xs font-bold uppercase tracking-widest">No hay actividades planificadas para este año</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    
            <!-- Mobile View (visible strictly on < md) -->
            <div class="md:hidden flex flex-col gap-4 p-4">
                @php $mobileCategory = ''; @endphp
                @forelse($plans as $plan)
                    @if($mobileCategory !== $plan->category)
                        <div class="text-[10px] font-black text-indigo-500 uppercase tracking-wider mt-4 first:mt-0">
                            — {{ $plan->category }}
                        </div>
                        @php $mobileCategory = $plan->category; @endphp
                    @endif
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:border-indigo-100 transition-all">
                        <!-- Header: Title & Code -->
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-bold text-gray-900 leading-tight">
                                    {{ $plan->activity_name }}
                                </div>
                                <div class="text-[9px] text-gray-400 font-medium mt-0.5 tracking-tighter">
                                    {{ $plan->activity_code ?: 'S/C' }}
                                </div>
                            </div>
                            <div class="w-2 h-2 rounded-full {{ $plan->category === 'Capacitación' ? 'bg-blue-400' : ($plan->category === 'Inspección' ? 'bg-orange-400' : 'bg-indigo-600') }}"></div>
                        </div>
    
                        <!-- Body: Matrix of 12 Months -->
                        <div class="grid grid-cols-4 gap-3 pt-3 border-t border-gray-50">
                            @foreach($months as $name => $num)
                                @php 
                                    $data = $complianceData[$plan->id][$name] ?? ['status' => 'none', 'count' => 0];
                                    $status = $data['status'];
                                    $cnt = $data['count'];
                                @endphp
                                <div class="flex flex-col items-center">
                                    <div class="text-[8px] font-black text-gray-400 uppercase mb-1">
                                        {{ substr(strtoupper($name), 0, 3) }}
                                    </div>
                                    <div 
                                        @if($editMode) wire:click="toggleMonth({{ $plan->id }}, '{{ $name }}')" @endif
                                        class="relative w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black transition-all border
                                        {{ $status === 'completed' ? 'bg-emerald-500 border-white text-white shadow-sm' : '' }}
                                        {{ $status === 'overdue' ? 'bg-red-500 border-white text-white shadow-sm animate-pulse' : '' }}
                                        {{ $status === 'pending' ? 'bg-yellow-400 border-white text-white shadow-sm' : '' }}
                                        {{ $status === 'none' ? 'bg-gray-50 border-gray-100 text-gray-200' : '' }}
                                        {{ $editMode ? 'cursor-pointer hover:scale-105 active:scale-95' : 'cursor-default' }}">
                                        
                                        @if($status === 'completed')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($status !== 'none')
                                            X
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
    
                        <!-- Footer: Delete Action (if Edit Mode) -->
                        @if($editMode)
                            <div class="flex justify-end pt-2 mt-2 border-t border-gray-50">
                                <button wire:click="deleteActivity({{ $plan->id }})" class="text-gray-400 hover:text-red-500 text-xs flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500 text-xs font-bold uppercase tracking-widest">
                        No hay actividades planificadas para este año
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('open-url', (event) => {
            window.open(event.url, '_blank');
        });
    </script>
    @endscript
</div>
