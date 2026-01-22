<div>
    @if($visit)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-6 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold">⚠️ Registrar Hallazgo</h2>
                            <p class="text-yellow-100 text-sm mt-1">Detectado durante la visita</p>
                        </div>
                        <button wire:click="$dispatch('closeHallazgoQuick')" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <form wire:submit="save" class="p-6 space-y-6">
                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Título del Hallazgo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="titulo" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all"
                               placeholder="Ej: Escalones oxidados en escalera principal">
                        @error('titulo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Descripción Detallada <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="descripcion" rows="4"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all"
                                  placeholder="Describa el hallazgo en detalle..."></textarea>
                        @error('descripcion') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Ubicación Específica -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Ubicación Específica
                        </label>
                        <input type="text" wire:model="ubicacion_especifica" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all"
                               placeholder="Ej: Escalera principal, 2do piso, sector norte">
                        @error('ubicacion_especifica') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Criticidad -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nivel de Criticidad <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model="criticidad" value="bajo" class="peer sr-only">
                                <div class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50">
                                    <div class="text-2xl mb-1">🟢</div>
                                    <div class="font-semibold text-sm">Bajo</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model="criticidad" value="medio" class="peer sr-only">
                                <div class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50">
                                    <div class="text-2xl mb-1">🟡</div>
                                    <div class="font-semibold text-sm">Medio</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model="criticidad" value="alto" class="peer sr-only">
                                <div class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50">
                                    <div class="text-2xl mb-1">🟠</div>
                                    <div class="font-semibold text-sm">Alto</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model="criticidad" value="critico" class="peer sr-only">
                                <div class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50">
                                    <div class="text-2xl mb-1">🔴</div>
                                    <div class="font-semibold text-sm">Crítico</div>
                                </div>
                            </label>
                        </div>
                        @error('criticidad') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Plazo de Corrección -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Plazo de Corrección <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="plazo_correccion" 
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                        @error('plazo_correccion') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="$dispatch('closeHallazgoQuick')"
                                class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl font-bold hover:from-yellow-600 hover:to-orange-600 transition-all shadow-lg">
                            💾 Registrar Hallazgo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
