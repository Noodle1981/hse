<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Importar Personal</h1>
            <p class="mt-2 text-sm text-gray-500 mb-4">Carga masiva de empleados mediante archivo Excel.</p>
            <button wire:click="downloadTemplate" class="inline-flex items-center text-xs font-bold text-ep-primary hover:text-ep-primary-dark transition-colors border border-ep-primary/20 rounded-lg px-4 py-2 bg-ep-primary/5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Descargar Plantilla Excel
            </button>
        </div>

        @if ($message)
            <div class="mb-6 p-4 rounded-xl text-sm font-medium {{ str_contains($message, 'Error') || count($errorLog) > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                <div class="font-bold mb-2">{{ $message }}</div>
                @if(count($errorLog) > 0)
                    <ul class="list-disc pl-5 text-xs space-y-1">
                        @foreach($errorLog as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
        @endif

        @if ($manualMessage)
            <div class="mb-6 p-4 rounded-xl text-sm font-medium {{ str_contains($manualMessage, 'Error') ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                <div class="font-bold">{{ $manualMessage }}</div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
            <form wire:submit="import" class="p-8 space-y-8">
                <!-- Empresa y Sede -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Empresa Destino</label>
                        <select wire:model.live="empresa_id" class="w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all">
                            <option value="">Seleccione Empresa...</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        @error('empresa_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">Sede Destino</label>
                        <select wire:model.live="sede_id" {{ !$empresa_id ? 'disabled' : '' }} class="w-full border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-ep-primary focus:border-ep-primary text-sm p-3 transition-all {{ !$empresa_id ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <option value="">Seleccione Sede...</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre_sede }}</option>
                            @endforeach
                        </select>
                        @error('sede_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Archivo -->
                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-700">Archivo Excel (.xlsx, .xls, .csv)</label>
                    <div class="relative group">
                        <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50 hover:bg-gray-100 hover:border-ep-primary cursor-pointer transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-12 h-12 text-gray-400 mb-3 group-hover:text-ep-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-bold">Haga clic para subir</span> o arrastre y suelte</p>
                                <p class="text-xs text-gray-400">Excel o CSV (Máx. 10MB)</p>
                            </div>
                            <input type="file" wire:model="file" class="hidden" />
                        </label>
                        @if ($file)
                            <div class="mt-4 p-3 bg-blue-50 text-blue-700 rounded-xl text-xs flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Archivo cargado: {{ $file->getClientOriginalName() }}
                            </div>
                        @endif
                        @error('file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Botón Importar -->
                <div>
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full flex justify-center items-center px-6 py-4 bg-ep-primary text-white font-bold rounded-2xl shadow-xl shadow-ep-primary/30 hover:bg-ep-primary-dark transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Comenzar Importación</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Procesando...
                        </span>
                    </button>
                </div>

                <!-- Instrucciones -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">Instrucciones del Formato</h4>
                    <ul class="space-y-2 text-xs text-gray-500 list-disc pl-4">
                        <li>El archivo debe contener las columnas: <span class="font-bold text-gray-700">nombre, apellido, dni</span>.</li>
                        <li>Columnas opcionales: <span class="font-bold text-gray-700">cuil, puesto</span>.</li>
                        <li>La primera fila debe ser el encabezado con estos nombres exactos.</li>
                        <li>El DNI debe ser único en el sistema.</li>
                    </ul>
                </div>
            </form>
        </div>
    </div>

    <!-- Sección de Registro Manual -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">Registro Manual Individual</h2>
                <p class="text-xs text-gray-500">¿Solo necesitas agregar uno? Hazlo aquí rápidamente.</p>
            </div>
            
            <form wire:submit="createManual" class="p-8 space-y-6">
                <!-- Usar selección de empresa/sede de arriba si está disponible, o inhabilitar si no -->
                @if(!$empresa_id || !$sede_id)
                    <div class="p-4 bg-blue-50 text-blue-700 rounded-xl text-sm mb-4">
                        <span class="font-bold">Nota:</span> Por favor selecciona Empresa y Sede en la parte superior primero.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                        <input type="text" wire:model="manual_name" class="w-full border-gray-200 rounded-xl focus:ring-ep-primary focus:border-ep-primary text-sm" placeholder="Juan">
                        @error('manual_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Apellido</label>
                        <input type="text" wire:model="manual_lastname" class="w-full border-gray-200 rounded-xl focus:ring-ep-primary focus:border-ep-primary text-sm" placeholder="Pérez">
                        @error('manual_lastname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">DNI (Único)</label>
                        <input type="text" wire:model="manual_dni" class="w-full border-gray-200 rounded-xl focus:ring-ep-primary focus:border-ep-primary text-sm" placeholder="12345678">
                        @error('manual_dni') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">CUIL (Opcional)</label>
                        <input type="text" wire:model="manual_cuil" class="w-full border-gray-200 rounded-xl focus:ring-ep-primary focus:border-ep-primary text-sm" placeholder="20-12345678-9">
                        @error('manual_cuil') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Puesto / Cargo (Opcional)</label>
                    <input type="text" wire:model="manual_puesto" class="w-full border-gray-200 rounded-xl focus:ring-ep-primary focus:border-ep-primary text-sm" placeholder="Operario de Mantenimiento">
                    @error('manual_puesto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            {{ (!$empresa_id || !$sede_id) ? 'disabled' : '' }}
                            class="w-full px-6 py-3 bg-gray-800 text-white font-bold rounded-xl shadow hover:bg-gray-900 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Registrar Empleado
                    </button>
                </div>
            </form>
        </div>
    </div>
