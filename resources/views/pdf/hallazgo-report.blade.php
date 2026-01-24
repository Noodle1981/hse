<x-pdf.layout :title="'Reporte de Hallazgo/Incidente'" :code="'FIND-'.$finding->id" :status="$finding->status">
    
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
            font-weight: 500;
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
    </style>

    {{-- Datos de Contexto (Ficha Técnica) --}}
    <x-pdf.card title="Información General del Reporte">
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
                        {{-- Esto asume que tienes badges configurados en tu CSS --}}
                        <span style="text-transform: uppercase; font-size: 8pt; font-weight: bold; color: {{ $finding->status == 'open' ? '#dc2626' : '#16a34a' }}">
                            ● {{ $finding->status_label ?? $finding->status }}
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
        <x-pdf.finding-detail :finding="$finding" />
    </div>

</x-pdf.layout>