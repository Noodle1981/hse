<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trazabilidad EPP | XamaSafe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased text-gray-800">

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-lg relative pb-10">
        <!-- Header -->
        <div class="bg-slate-800 text-white p-6 text-center shadow-md">
            <h1 class="text-2xl font-bold tracking-tight">XamaSafe</h1>
            <p class="text-sm opacity-80 mt-1">Control de Trazabilidad EPP</p>
            <p class="text-xs mt-1 text-slate-300">Res. SRT 299/11 / Res. SIC 18/2025</p>
        </div>

        @php
            $isBaja = $item->estado === \App\Models\EppDeliveryItem::ESTADO_DADO_DE_BAJA;
            $isVencido = $item->estaVencido() || $item->estado === \App\Models\EppDeliveryItem::ESTADO_VENCIDO;
            $dias = $item->diasParaVencimiento();
            $isWarning = !$isBaja && !$isVencido && $dias !== null && $dias <= 30;
            
            // Colores y textos dinámicos
            $statusColor = 'bg-green-500';
            $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
            $statusText = 'Activo y Vigente';

            if ($isBaja) {
                $statusColor = 'bg-slate-700'; // Gris muy oscuro/casi negro
                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />';
                $statusText = 'DADO DE BAJA';
            } elseif ($isVencido) {
                $statusColor = 'bg-red-500';
                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                $statusText = 'PRODUCTO VENCIDO';
            } elseif ($isWarning) {
                $statusColor = 'bg-orange-500';
                $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                $statusText = 'PRÓXIMO A VENCER (Vence en '.$dias.' días)';
            }
        @endphp

        <!-- Banner de Estado (Semáforo) -->
        <div class="{{ $statusColor }} text-white p-4 flex items-center justify-center space-x-3 shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
            <span class="text-xl font-bold">{{ $statusText }}</span>
        </div>

        @if($isBaja)
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 m-4 text-sm rounded">
                <p><strong>Motivo de baja:</strong> {{ $item->motivo_baja ?? 'Inhabilitado por inspector/técnico' }}</p>
                <p><strong>Fecha:</strong> {{ $item->fecha_baja ? $item->fecha_baja->format('d/m/Y H:i') : 'N/D' }}</p>
            </div>
        @endif

        <div class="p-6">
            <!-- Detalles del Producto -->
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-700 border-b pb-2 mb-3">Detalle del Producto</h2>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><span class="font-semibold text-gray-800">Producto:</span> {{ $item->eppItem->nombre ?? 'N/D' }}</p>
                    <p><span class="font-semibold text-gray-800">Tipo/Categoría:</span> {{ $item->eppItem->tipo ?? 'N/D' }}</p>
                    <p><span class="font-semibold text-gray-800">Normativa:</span> {{ $item->eppItem->codigo_norma ?? 'N/D' }}</p>
                    
                    @if($item->lote)
                        <p><span class="font-semibold text-gray-800">Lote:</span> {{ $item->lote }}</p>
                    @endif
                    
                    @if($item->eppItem->requiere_control_critico)
                         <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 border border-red-200 mt-2">
                             Requiere Control Crítico
                         </span>
                    @endif
                </div>
            </div>

            <!-- Asignación -->
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-700 border-b pb-2 mb-3">Asignación</h2>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><span class="font-semibold text-gray-800">Trabajador:</span> 
                        {{ $item->eppDelivery->employee->name ?? 'N/A' }} {{ $item->eppDelivery->employee->last_name ?? '' }}
                    </p>
                    <p><span class="font-semibold text-gray-800">Sede Operativa:</span> {{ $item->eppDelivery->employee->sede->nombre_sede ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-gray-800">Fecha de Entrega original:</span> {{ $item->eppDelivery->delivery_date instanceof \Carbon\Carbon ? $item->eppDelivery->delivery_date->format('d/m/Y') : $item->eppDelivery->delivery_date }}</p>
                </div>
            </div>

            <!-- Trazabilidad Extendida -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mt-6 shadow-sm">
                <h3 class="font-semibold text-gray-700 mb-2 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Trazabilidad de la Certificación
                </h3>
                
                @if($item->tiene_qr_res18)
                    <div class="text-xs text-gray-600 mb-3 ml-6 border-l-2 border-indigo-400 pl-3 py-1">
                        <strong>Certificación:</strong> Cumple Res. SIC 18/2025<br>
                        <strong>Trazabilidad de Fábrica:</strong> Comprobado vía código unívoco originario.
                    </div>
                @else
                    <div class="text-xs text-gray-600 mb-3 ml-6 border-l-2 border-gray-400 pl-3 py-1 bg-white inline-block">
                        <strong>Certificación:</strong> Producto Pre-norma (Sello S)<br>
                        <strong>Trazabilidad:</strong> Carga manual por Administrador en origen.
                    </div>
                @endif
                
                <div class="text-[10px] text-gray-400 mt-4 text-center break-all">
                    UID: {{ $item->codigo_qr_trazabilidad }}
                </div>
            </div>

        </div>

    </div>

</body>
</html>
