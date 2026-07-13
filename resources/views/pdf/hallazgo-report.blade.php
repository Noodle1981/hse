<x-pdf.layout :title="'Reporte de Hallazgo/Incidente'" :code="'FIND-'.$finding->id" :status="$finding->estado" :branding="$branding">
    
    <style>
        .context-table { width: 100%; border-collapse: collapse; }
        .context-table td { vertical-align: top; padding: 10px 5px; }
        
        /* Etiquetas pequeñas y grises */
        .label-sm { 
            font-size: 8pt; 
            color: #64748b; 
            text-transform: uppercase; 
            font-weight: bold; 
            display: block;
            margin-bottom: 2px;
        }
        
        /* Valores en negro y legibles */
        .value-md { 
            font-size: 10.5pt; 
            color: #1e293b; 
            font-weight: 600;
        }

        /* Indicador de ID */
        .id-badge {
            background-color: #f1f5f9;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            display: inline-block;
        }

        .separator { height: 15px; }

        /* Colores de estado dinamicos para evitar errores de IDE */
        .status-pendiente { color: #dc2626; font-weight: bold; }
        .status-en_proceso { color: #ca8a04; font-weight: bold; }
        .status-resuelto { color: #16a34a; font-weight: bold; }
        .status-cerrado { color: #16a34a; font-weight: bold; }
        .status-default { color: #64748b; }
    </style>

    @php
        $statusClass = match($finding->estado) {
            'pendiente' => 'status-pendiente',
            'en_proceso' => 'status-en_proceso',
            'resuelto' => 'status-resuelto',
            'cerrado' => 'status-cerrado',
            default => 'status-default'
        };
    @endphp

    <x-pdf.card title="Información General del Reporte" :branding="$branding">
        <table class="context-table">
            <tr>
                {{-- Columna 1: Ubicación --}}
                <td style="width: 35%; border-right: 1px solid #f1f5f9;">
                    <span class="label-sm">Ubicación / Sede</span>
                    <span class="value-md">{{ $finding->sede->nombre_sede ?? 'N/A' }}</span>
                    
                    <div style="margin-top: 10px;">
                        <span class="label-sm">Empresa Cliente</span>
                        <span class="value-md">{{ $finding->sede->empresa->nombre ?? 'N/A' }}</span>
                    </div>
                </td>

                {{-- Columna 2: Reportante --}}
                <td style="width: 35%; padding-left: 15px; border-right: 1px solid #f1f5f9;">
                    <span class="label-sm">Reportado por</span>
                    <span class="value-md">{{ $finding->user->nombres ?? '' }} {{ $finding->user->apellidos ?? 'N/A' }}</span>
                    
                    <div style="margin-top: 10px;">
                        <span class="label-sm">Fecha de Registro</span>
                        <span class="value-md">{{ $finding->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </td>

                {{-- Columna 3: Referencias --}}
                <td style="width: 30%; padding-left: 15px; text-align: right;">
                    <span class="label-sm">Identificador</span>
                    <div class="id-badge">
                        <span style="font-size: 11pt; font-weight: bold; color: #1e40af;">
                            #{{ $finding->visit_counter }}
                        </span>
                        <span style="font-size: 8pt; color: #64748b;">(ID: {{ $finding->id }})</span>
                    </div>
                    
                    <div style="margin-top: 10px;">
                        <span class="label-sm">Estado Actual</span>
                        <span class="{{ $statusClass }}" style="text-transform: uppercase; font-size: 8pt; font-weight: bold;">
                            ● {{ ucfirst(str_replace('_', ' ', $finding->estado)) }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </x-pdf.card>

    <div class="separator"></div>

    {{-- Detalle del Hallazgo (Descripción, Riesgo, Acciones) --}}
    {{-- 
        Sugerencia: Si puedes editar x-pdf.finding-detail, 
        asegúrate de que use width: 100% y padding consistente.
    --}}
    <div style="width: 100%;">
        <x-pdf.finding-detail :finding="$finding" :branding="$branding" />
    </div>

    @if(isset($seguimientos) && $seguimientos->count() > 0)
        <div class="separator" style="height: 20px;"></div>
        <x-pdf.card title="Historial de Seguimientos e Intervenciones" :branding="$branding">
            <table style="width: 100%; border-collapse: collapse; font-size: 10pt; text-align: left;">
                <thead>
                    <tr style="background-color: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                        <th style="padding: 10px; width: 15%; color: #334155;">Fecha</th>
                        <th style="padding: 10px; width: 25%; color: #334155;">Responsable</th>
                        <th style="padding: 10px; width: 20%; color: #334155;">Evolución</th>
                        <th style="padding: 10px; width: 40%; color: #334155;">Comentario Técnico</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seguimientos as $seguimiento)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 10px; color: #475569;">{{ $seguimiento->fecha_seguimiento->format('d/m/Y H:i') }}</td>
                            <td style="padding: 10px; color: #1e293b; font-weight: bold;">{{ $seguimiento->user->nombres ?? 'Sistema' }} {{ $seguimiento->user->apellidos ?? '' }}</td>
                            <td style="padding: 10px;">
                                @if($seguimiento->estado_anterior !== $seguimiento->estado_nuevo)
                                    <span style="color: #64748b; font-size: 8pt;">De: {{ ucfirst($seguimiento->estado_anterior) }}</span><br>
                                    <span style="color: #0f172a; font-weight: bold;">A: {{ ucfirst($seguimiento->estado_nuevo) }}</span>
                                @else
                                    <span style="color: #64748b;">Mantenimiento: {{ ucfirst($seguimiento->estado_nuevo) }}</span>
                                @endif
                            </td>
                            <td style="padding: 10px; color: #334155;">{{ $seguimiento->comentario }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-pdf.card>
    @endif

</x-pdf.layout>