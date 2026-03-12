<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    {{-- KPI Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Empresas Activas</div>
            <div class="text-3xl font-black text-indigo-600">{{ $stats['total_companies'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Sedes</div>
            <div class="text-3xl font-black text-gray-900">{{ $stats['total_sedes'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Inspecciones Totales</div>
            <div class="text-3xl font-black text-green-600">{{ $stats['total_inspections'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Usuarios Online</div>
            <div class="text-3xl font-black text-blue-600">{{ $stats['total_active_users'] }}</div>
        </div>
    </div>

    {{-- Monitoring Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="font-bold text-gray-800">Monitoreo de Actividad por Empresa</h3>
            <span class="text-[10px] bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full font-bold uppercase self-start sm:self-auto w-max">Actualizado en tiempo real</span>
        </div>
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/50">
                <tr>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Empresa</th>
                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Sedes</th>
                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Checklists / Asign.</th>
                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Capacitaciones</th>
                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Hallazgos</th>
                    <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Historial</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($companies as $company)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center font-bold text-gray-400 text-xs uppercase">
                                {{ substr($company->nombre, 0, 2) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $company->nombre }}</div>
                                <div class="text-[10px] text-gray-400">{{ $company->cuit }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-gray-700">{{ $company->sedes_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                        {{ $company->assignments_count }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                        {{-- Placeholder para capacitaciones --}}
                        <span class="text-gray-300">-</span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                        {{-- Placeholder para hallazgos --}}
                        <span class="text-gray-300">-</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    </div>
</div>

