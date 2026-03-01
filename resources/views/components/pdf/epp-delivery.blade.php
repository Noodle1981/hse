@props(['delivery'])

<x-pdf.card title="Entrega de Elementos de Protección Personal (EPP)">
    <table class="no-border" style="margin-bottom: 15px;">
        <tr>
            <td style="width: 50%;">
                <strong>Empleado:</strong> <span class="text-primary">{{ $delivery->employee->name ?? 'N/A' }} {{ $delivery->employee->last_name ?? '' }}</span><br>
                <strong>DNI/CUIL:</strong> {{ $delivery->employee->dni ?? 'N/A' }}<br>
                <strong>Puesto:</strong> {{ $delivery->employee->puesto ?? 'N/A' }}
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>Fecha de Entrega:</strong> {{ $delivery->delivery_date instanceof \Carbon\Carbon ? $delivery->delivery_date->format('d/m/Y') : $delivery->delivery_date }}<br>
                <strong>Entregado por:</strong> {{ $delivery->user->nombres ?? 'Sistema' }}
            </td>
        </tr>
    </table>

    <table class="table-bordered">
        <thead>
            <tr>
                <th>Elemento (EPP)</th>
                <th>Tipo</th>
                <th class="text-center">Cant.</th>
                <th>Estado/Motivo</th>
                <th class="text-center">Trazabilidad (QR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delivery->items as $item)
                <tr>
                    <td>{{ $item->nombre }}</td>
                    <td>{{ $item->tipo }}</td>
                    <td class="text-center">{{ $item->pivot->cantidad ?? 1 }}</td>
                    <td>{{ $delivery->observaciones ?? 'Entrega inicial' }}</td>
                    <td class="text-center" style="vertical-align: middle;">
                        @if($item->pivot->codigo_qr_trazabilidad)
                            @php
                                // Soporte para DOMPDF inyectando SVG en codificación base64
                                $qrBase64 = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                    ->size(50)
                                    ->margin(0)
                                    ->generate(route('epp.qr.scan', $item->pivot->codigo_qr_trazabilidad)));
                            @endphp
                            <img src="data:image/svg+xml;base64,{!! $qrBase64 !!}" style="display:inline-block; width:50px; height:50px;" alt="QR Trazabilidad">
                            <div style="font-size: 7px; color: #555; margin-top:2px;">UID: {{ substr($item->pivot->codigo_qr_trazabilidad, 0, 8) }}</div>
                        @elseif($item->pivot->tiene_qr_res18 === false)
                            <div style="font-size: 9px; font-weight:bold; color:#777; padding: 10px 0;">[SELLO S]<br><span style="font-weight:normal">Pre-norma</span></div>
                        @else
                            <span style="font-size: 10px; color: #999;">N/D</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Bloque de Firmas --}}
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <table class="no-border">
            <tr>
                <td style="width: 50%; text-align: center;">
                    @if($delivery->signature)
                        <img src="{{ $delivery->signature }}" style="max-height: 60px; border-bottom: 1px solid #333;">
                    @else
                        <div style="height: 60px; border-bottom: 1px solid #333;"></div>
                    @endif
                    <div class="text-xs mt-2">Firma del Empleado</div>
                    <div class="text-xs font-bold">{{ $delivery->employee->name ?? '' }}</div>
                </td>
                <td style="width: 50%; text-align: center;">
                     {{-- Firma del Responsable/Técnico (Opcional si se requiere) --}}
                     <div style="height: 60px; border-bottom: 1px solid #ccc;"></div>
                     <div class="text-xs mt-2">Firma Responsable de Entrega</div>
                </td>
            </tr>
        </table>
        
        <div class="text-xs text-secondary mt-4" style="text-align: justify;">
            Declaro haber recibido los elementos de protección personal detallados, encontrándose los mismos en buen estado.
            Me comprometo a utilizarlos correctamente y solicitar su reposición ante cualquier deterioro.
        </div>
    </div>
</x-pdf.card>
