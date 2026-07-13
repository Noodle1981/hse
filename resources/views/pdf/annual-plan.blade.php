@php
    $cp = $branding['primary'] ?? '#1e40af';
    $cs = $branding['secondary'] ?? '#6b7280';
    $ca = $branding['accent'] ?? '#16a34a';
@endphp

<x-pdf.layout 
    :title="'PROGRAMA ANUAL DE ACTIVIDADES - ' . $year" 
    :subtitle="'Sede: ' . $sede->nombre_sede" 
    :code="'PA-HYS-' . $year" 
    :branding="$branding"
>
    <style>
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-top: 3px solid {{ $cp }};
        }
        .info-table td { 
            padding: 6px 8px; 
            border: 1px solid #e2e8f0; 
        }
        .info-label { 
            font-size: 7pt; 
            font-weight: bold; 
            color: #64748b; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .info-value { 
            font-size: 9pt; 
            font-weight: bold; 
            color: #1e293b;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .main-table th {
            background-color: {{ $cp }};
            color: white;
            padding: 6px 3px;
            font-size: 7.5pt;
            font-weight: bold;
            border: 1px solid {{ $cp }};
            text-transform: uppercase;
            text-align: center;
        }
        .main-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 4px;
            vertical-align: middle;
            font-size: 7.5pt;
        }
        .category-row {
            background-color: #f1f5f9;
            font-weight: bold;
            color: {{ $cp }};
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
            padding: 6px 8px !important;
            border-left: 4px solid {{ $cp }} !important;
        }
        .cell-status {
            width: 18px;
            height: 18px;
            margin: 0 auto;
            border-radius: 4px;
            display: block;
            text-align: center;
            line-height: 18px;
            font-weight: bold;
            font-size: 8pt;
            color: white;
            position: relative;
        }
        .bg-completed { background-color: #10b981; }
        .bg-pending { background-color: #4f46e5; }
        .bg-overdue { background-color: #ef4444; }
        .bg-suspended { background-color: #f59e0b; }
        .bg-cancelled { background-color: #9ca3af; }
        .bg-none { background-color: #f8fafc; border: 1px solid #e2e8f0; }
        
        .count-badge {
            font-size: 6pt;
            position: absolute;
            top: -4px;
            right: -4px;
            background: white;
            color: #10b981;
            padding: 0 3px;
            border-radius: 6px;
            border: 1px solid #10b981;
            font-weight: bold;
            line-height: 1;
        }

        .references-container {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .references-title {
            font-size: 8pt;
            font-weight: bold;
            color: #475569;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .reference-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 7.5pt;
        }
        .ref-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            margin-right: 4px;
            vertical-align: middle;
        }
    </style>

    {{-- Info Table --}}
    <table class="info-table">
        <tr>
            <td width="25%">
                <div class="info-label">Empresa Mandante</div>
                <div class="info-value">{{ $sede->empresa->nombre }}</div>
            </td>
            <td width="25%">
                <div class="info-label">Activo / Sede Operativa</div>
                <div class="info-value">{{ $sede->nombre_sede }}</div>
            </td>
            <td width="25%">
                <div class="info-label">Año Fiscal</div>
                <div class="info-value">{{ $year }}</div>
            </td>
            <td width="25%">
                <div class="info-label">Fecha Emisión</div>
                <div class="info-value">{{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    {{-- Main Table --}}
    <table class="main-table">
        <thead>
            <tr>
                <th width="20%" style="text-align: left; padding-left: 8px;">Actividad / Programa</th>
                <th width="8%">Código</th>
                @foreach($months as $name => $num)
                    <th width="6%">{{ substr(strtoupper($name), 0, 3) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $currentCategory = ''; @endphp
            @foreach($reportData as $item)
                @if($currentCategory !== $item['plan']->category)
                    <tr>
                        <td colspan="14" class="category-row">{{ $item['plan']->category }}</td>
                    </tr>
                    @php $currentCategory = $item['plan']->category; @endphp
                @endif
                <tr>
                    <td style="font-weight: 600; color: #334155; padding-left: 8px;">
                        {{ $item['plan']->activity_name }}
                    </td>
                    <td align="center" style="font-family: monospace; font-size: 7pt; color: #475569;">
                        {{ $item['plan']->checklist?->registry_code ?: ($item['plan']->activity_code ?: '-') }}
                    </td>
                    @foreach($months as $name => $num)
                        @php 
                            $status = $item['months'][$name]['status'];
                            $cnt = $item['months'][$name]['count'];
                        @endphp
                        <td align="center">
                            @if($status !== 'none')
                                <div class="cell-status bg-{{ $status }}">
                                    @if($status === 'completed')
                                        ✓
                                        @if($cnt > 1)
                                            <span class="count-badge">{{ $cnt }}</span>
                                        @endif
                                    @elseif($status === 'suspended')
                                        S
                                    @elseif($status === 'cancelled')
                                        C
                                    @else
                                        -
                                    @endif
                                </div>
                            @else
                                <div class="cell-status bg-none"></div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- References --}}
    <div class="references-container">
        <div class="references-title">Referencias de Cumplimiento</div>
        <div>
            <div class="reference-item">
                <span class="ref-dot bg-pending"></span>
                <span style="color: #4f46e5; font-weight: bold;">Planificado (Pendiente)</span>
            </div>
            <div class="reference-item">
                <span class="ref-dot bg-completed"></span>
                <span style="color: #10b981; font-weight: bold;">Cumplido</span>
            </div>
            <div class="reference-item">
                <span class="ref-dot bg-overdue"></span>
                <span style="color: #ef4444; font-weight: bold;">Incumplimiento (Vencido)</span>
            </div>
            <div class="reference-item">
                <span class="ref-dot bg-suspended"></span>
                <span style="color: #f59e0b; font-weight: bold;">Suspendido</span>
            </div>
            <div class="reference-item">
                <span class="ref-dot bg-cancelled"></span>
                <span style="color: #9ca3af; font-weight: bold;">Cancelado</span>
            </div>
        </div>
    </div>
</x-pdf.layout>
