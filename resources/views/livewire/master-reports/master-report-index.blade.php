<div>
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input wire:model.live="search" type="text" placeholder="Buscar por sede o título..." class="w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            <select wire:model.live="status" class="w-full sm:w-auto rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                <option value="">Todos los estados</option>
                <option value="solicitado">Solicitados</option>
                <option value="en_redaccion">En Redacción</option>
                <option value="finalizado">Finalizados</option>
            </select>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="w-full md:w-auto flex">
                <a href="{{ route('gestor.master-reports.create') }}" class="w-full justify-center inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:border-orange-900 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Solicitar Informe
                </a>
            </div>
        @endif
    </div>

    <!-- Desktop Table (visible from md) -->
    <div class="hidden md:block overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Informe / Periodo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Técnico</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reports as $report)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $report->titulo }}</div>
                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($report->periodo)->translatedFormat('F Y') }}</div>
                            @if(auth()->user()->isTechnician() && $report->estado === 'en_redaccion' && $report->observacion_revision)
                                <div class="mt-1 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-xxs text-yellow-800 rounded">
                                    <strong>Nota de Corrección:</strong> {{ $report->observacion_revision }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $report->sede->nombre_sede }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $report->estado === 'finalizado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ strtoupper($report->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->tecnico->nombres }} {{ $report->tecnico->apellidos }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($report->estado === 'finalizado')
                                <a href="{{ route(auth()->user()->isAdmin() ? 'gestor.master-reports.pdf.loading' : 'tecnico.master-reports.pdf.loading', $report->id) }}" target="_blank" class="text-orange-600 hover:text-orange-900 mr-3">Ver PDF</a>
                                @if(auth()->user()->isAdmin())
                                    <button wire:click="prepararRevision({{ $report->id }})" class="text-red-600 hover:text-red-900 mr-3">Corregir</button>
                                @endif
                            @endif
                            
                            @if(auth()->user()->isTechnician() && $report->estado !== 'finalizado')
                                <a href="{{ route('tecnico.master-reports.edit', $report->id) }}" class="text-indigo-600 hover:text-indigo-900">Redactar</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron informes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile View (visible strictly on < md) -->
    <div class="md:hidden flex flex-col gap-4">
        @forelse($reports as $report)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:border-ep-primary/30 transition-all">
                <!-- Header: Title & Period -->
                <div class="flex justify-between items-start mb-3">
                    <div class="max-w-[70%]">
                        <div class="font-bold text-gray-900 leading-tight">
                            {{ $report->titulo }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            {{ \Carbon\Carbon::parse($report->periodo)->translatedFormat('F Y') }}
                        </div>
                        @if(auth()->user()->isTechnician() && $report->estado === 'en_redaccion' && $report->observacion_revision)
                            <div class="mt-1 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-xxs text-yellow-800 rounded">
                                <strong>Nota de Corrección:</strong> {{ $report->observacion_revision }}
                            </div>
                        @endif
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase
                        {{ $report->estado === 'finalizado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $report->estado === 'finalizado' ? 'Finalizado' : 'Redacción' }}
                    </span>
                </div>

                <!-- Body: Sede and Técnico -->
                <div class="mb-4">
                    <div class="text-sm font-semibold text-ep-primary">Sede: {{ $report->sede->nombre_sede }}</div>
                    @if(auth()->user()->isAdmin())
                        <div class="text-xs text-gray-500 mt-1">Técnico: {{ $report->tecnico->nombres }} {{ $report->tecnico->apellidos }}</div>
                    @endif
                </div>

                <!-- Footer: Actions -->
                <div class="flex justify-end items-center gap-3 pt-3 border-t border-gray-100">
                    @if($report->estado === 'finalizado')
                        <a href="{{ route(auth()->user()->isAdmin() ? 'gestor.master-reports.pdf.loading' : 'tecnico.master-reports.pdf.loading', $report->id) }}" target="_blank" class="text-orange-600 hover:text-orange-900 font-bold text-xs flex items-center gap-1">
                            Ver PDF
                        </a>
                        @if(auth()->user()->isAdmin())
                            <button wire:click="prepararRevision({{ $report->id }})" class="text-red-600 hover:text-red-900 font-bold text-xs flex items-center gap-1">
                                Corregir
                            </button>
                        @endif
                    @endif
                    
                    @if(auth()->user()->isTechnician() && $report->estado !== 'finalizado')
                        <a href="{{ route('tecnico.master-reports.edit', $report->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs flex items-center gap-1">
                            Redactar
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-white rounded-xl border border-gray-200">
                <p class="text-sm text-gray-500">No se encontraron informes.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
    @if($showRevisarModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-lg w-full m-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Solicitar Corrección al Técnico</h3>
                <form wire:submit.prevent="enviarRevision">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones / Motivo del Rechazo</label>
                        <textarea wire:model="observacion_revision" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 text-sm" placeholder="Escribe las correcciones que debe hacer el técnico..."></textarea>
                        @error('observacion_revision') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showRevisarModal', false)" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50 text-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Enviar Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
