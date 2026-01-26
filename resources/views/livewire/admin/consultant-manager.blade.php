<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Gestión de Consultoras</h2>
            <p class="text-sm text-gray-500">Administra las firmas consultoras y sus límites de técnicos.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg shadow-sm hover:bg-indigo-700 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nueva Consultora
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Consultora</th>
                    <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">CUIT</th>
                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Empresas</th>
                    <th class="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Límite Técnicos</th>
                    <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($consultoras as $c)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $c->nombre }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs text-gray-500">{{ $c->cuit }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full border border-blue-100 italic">
                            {{ $c->empresas_count }} empresas
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center space-x-1">
                            <span class="text-sm font-black text-gray-700">{{ $c->users_count }}</span>
                            <span class="text-gray-400 text-xs">/</span>
                            <span class="text-xs font-bold text-indigo-600">{{ $c->max_tecnicos }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="edit({{ $c->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button onclick="confirm('¿Eliminar consultora?') || event.stopImmediatePropagation()" wire:click="delete({{ $c->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $consultoras->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 opacity-75" wire:click="$set('showModal', false)"></div>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden max-w-lg w-full z-10">
                <form wire:submit.prevent="save">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">{{ $editingId ? 'Editar Consultora' : 'Nueva Consultora' }}</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre Comercial</label>
                            <input type="text" wire:model="nombre" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('nombre') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">CUIT</label>
                            <input type="text" wire:model="cuit" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('cuit') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Límite de Técnicos (Licencias)</label>
                            <input type="number" wire:model="max_tecnicos" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            @error('max_tecnicos') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Cancelar</button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg shadow-sm hover:bg-indigo-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

