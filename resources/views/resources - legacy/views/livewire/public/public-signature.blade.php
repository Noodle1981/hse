<div class="w-full max-w-lg mt-10">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Firma de Constancia</h1>
        <p class="text-gray-600 mt-2">Sistema de Gestión EP-Back</p>
    </div>

    <!-- Mensajes de Estado -->
    @if($error_message)
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ $error_message }}</p>
        </div>
    @elseif(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow" role="alert">
            <p class="font-bold">¡Firmado!</p>
            <p>{{ session('success') }}</p>
        </div>
    @else

        <!-- Paso 1: Verificación de Código -->
        @if(!$verified)
            <div class="bg-white shadow-xl rounded-2xl p-8">
                <h2 class="text-xl font-semibold mb-6 border-b pb-2">1. Verificación de Identidad</h2>
                
                <p class="mb-4 text-gray-600 text-sm">
                    Para acceder a la constancia, por favor ingrese el código de seguridad de 4 dígitos que recibió en el correo electrónico.
                </p>

                <form wire:submit="verifyCode">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Código de Seguridad</label>
                        <input type="text" wire:model="code" 
                               class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-ep-primary"
                               placeholder="Ej: 1234" maxlength="10">
                        @error('code') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full bg-ep-primary hover:bg-ep-primary-dark text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150">
                        Verificar Código
                    </button>
                </form>
            </div>
        @else
            <!-- Paso 2: Firma -->
            <div class="bg-white shadow-xl rounded-2xl p-8">
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">2. Revisión y Firma</h2>
                
                <!-- Resumen -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm">
                    <p><strong>Sede:</strong> {{ $certificate->sede->nombre_sede }} ({{ $certificate->sede->empresa->nombre }})</p>
                    <p><strong>Fecha:</strong> {{ ($certificate->fecha_visita ?? $certificate->visit_date)->format('d/m/Y') }}</p>
                    @if($certificate->tipo_visita ?? false)
                        <p><strong>Tipo:</strong> {{ $certificate->tipo_visita }}</p>
                    @endif
                    <p class="mt-2"><strong>Observaciones:</strong></p>
                    <p class="text-gray-600 italic">{{ Str::limit($certificate->observacion ?? $certificate->general_observations, 100) }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Su Firma Digital</label>
                    
                    <div x-data="signaturePad(@entangle('signature'))" class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50 flex flex-col items-center">
                        <canvas x-ref="canvas" class="bg-white border border-gray-200 rounded shadow-sm w-full h-48 cursor-crosshair touch-none"></canvas>
                        
                        <div class="flex gap-3 mt-3 w-full">
                            <button type="button" @click="clear()" class="flex-1 py-2 px-4 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-medium text-sm">
                                Borrar
                            </button>
                        </div>
                    </div>
                    @error('signature') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>

                <button wire:click="saveSignature" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150">
                    Confirmar y Firmar Constancia
                </button>
            </div>
        @endif

    @endif

    <!-- AlpineJS for Signature Pad -->
    @script
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
    <script>
        Alpine.data('signaturePad', (modelVal) => ({
            signaturePad: null,
            init() {
                const canvas = this.$refs.canvas;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                
                // Adjust canvas size
                const resize = () => {
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                };
                
                resize();
                window.addEventListener("resize", resize);

                this.signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });

                this.signaturePad.addEventListener("endStroke", () => {
                    this.$wire.set('signature', this.signaturePad.toDataURL());
                });
            },
            clear() {
                this.signaturePad.clear();
                this.$wire.set('signature', '');
            }
        }))
    </script>
    @endscript
</div>
