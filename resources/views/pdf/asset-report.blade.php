<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Informe de Activo - {{ $asset->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* PALETA CORPORATIVA XAMASAFE */
        .text-orange { color: #F69239; }
        .text-dark { color: #1e293b; }
        .bg-dark { background-color: #1e293b; color: #ffffff; }
        
        /* CABECERA A4 */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #F69239;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .report-subtitle {
            color: #F69239;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0;
        }
        .report-num {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            margin: 2px 0;
        }

        /* BADGES DE ESTADO */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-operativo { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-alerta { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-fuera { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* SECCIONES Y ESTRUCTURA */
        .section { margin-bottom: 18px; width: 100%; page-break-inside: avoid; }
        .section-header {
            background-color: #f8fafc;
            border-left: 4px solid #F69239;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .section-header h2 {
            margin: 0;
            font-size: 11px;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 800;
        }

        /* TABLAS DE DATOS */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table td { padding: 8px 6px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 2px; }
        .value { font-size: 10px; color: #1e293b; font-weight: 600; }

        /* MOSAICO FOTOGRÁFICO A4 */
        .photo-table { width: 100%; border-collapse: separate; border-spacing: 8px; margin-top: 5px; }
        .photo-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
        }
        .photo-card img {
            width: 100%;
            max-height: 180px;
            object-fit: contain;
            border-radius: 4px;
            display: block;
            margin: 0 auto 6px auto;
        }
        .photo-title {
            font-size: 9px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
        }

        /* BOTÓN DE IMPRESIÓN */
        .print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-btn {
            background: #F69239;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: #fff; }
        }
    </style>
</head>
<body>

    <!-- BARRA NO IMPRIMIBLE -->
    <div class="print-bar no-print">
        <span style="font-size: 11px; font-weight: bold;">📄 Documento de Ficha Técnica (Formato A4)</span>
        <a href="javascript:window.print()" class="print-btn">🖨️ Imprimir / Guardar PDF</a>
    </div>

    <!-- CABECERA PRINCIPAL -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <p class="report-subtitle">XamaSafe — Ficha de Equipamiento</p>
                <h1 class="report-num">{{ $asset->name }}</h1>
                <div style="font-family: monospace; color: #F69239; font-weight: bold; font-size: 11px;">
                    CÓDIGO / QR: {{ $asset->identifier }}
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle; width: 200px;">
                @if($asset->status === 'operativo')
                    <span class="badge badge-operativo">● Operativo</span>
                @elseif($asset->status === 'alerta')
                    <span class="badge badge-alerta">● En Alerta</span>
                @else
                    <span class="badge badge-fuera">● Fuera de Servicio</span>
                @endif
                <p style="font-size: 8px; color: #64748b; margin: 6px 0 0 0;">
                    Emisión: {{ date('d/m/Y H:i') }}
                </p>
            </td>
        </tr>
    </table>

    <!-- SECCIÓN 1: DATOS GENERALES -->
    <div class="section">
        <div class="section-header">
            <h2>1. Ubicación y Clasificación del Activo</h2>
        </div>
        <table class="data-table">
            <tr>
                <td style="width: 25%;">
                    <span class="label">Empresa</span>
                    <span class="value">{{ $asset->sede?->empresa?->nombre ?? 'N/A' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Sede / Planta</span>
                    <span class="value">{{ $asset->sede?->nombre_sede ?? 'N/A' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Tipo de Activo</span>
                    <span class="value text-orange">{{ $asset->assetType?->name ?? 'Genérico' }}</span>
                </td>
                <td style="width: 25%;">
                    <span class="label">Fecha de Alta</span>
                    <span class="value">{{ $asset->created_at ? $asset->created_at->format('d/m/Y') : 'N/D' }}</span>
                </td>
            </tr>
            @if($asset->grupo)
            <tr>
                <td style="width: 25%;">
                    <span class="label">{{ $asset->assetType?->group_label ?? 'Grupo' }}</span>
                    <span class="value">{{ $asset->grupo }}</span>
                </td>
                <td style="width: 25%;">
                    @if($asset->subgrupo)
                        <span class="label">{{ $asset->assetType?->subgroup_label ?? 'Subgrupo' }}</span>
                        <span class="value">{{ $asset->subgrupo }}</span>
                    @endif
                </td>
                <td style="width: 25%;"></td>
                <td style="width: 25%;"></td>
            </tr>
            @endif
        </table>
    </div>

    <!-- SECCIÓN 2: METADATA DINÁMICA -->
    @if($asset->metadata && count($asset->metadata) > 0)
    <div class="section">
        <div class="section-header">
            <h2>2. Métricas y Ficha Técnica Dinámica</h2>
        </div>
        <table class="data-table">
            @foreach(array_chunk(array_pairs_helper($asset->metadata), 4) as $chunk)
                <tr>
                    @foreach($chunk as $pair)
                        <td style="width: 25%;">
                            <span class="label">{{ $pair['key'] }}</span>
                            <span class="value">{{ is_array($pair['val']) ? json_encode($pair['val']) : $pair['val'] }}</span>
                        </td>
                    @endforeach
                    @for($i = count($chunk); $i < 4; $i++)
                        <td style="width: 25%;"></td>
                    @endfor
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- SECCIÓN 3: EXPEDIENTE FOTOGRÁFICO DE LA UNIDAD -->
    @if($asset->photos && count($asset->photos) > 0)
    <div class="section">
        <div class="section-header">
            <h2>3. Fotos de Identificación del Activo</h2>
        </div>
        <table class="photo-table">
            @foreach(array_chunk(array_pairs_helper($asset->photos), 2) as $row)
                <tr>
                    @foreach($row as $p)
                        <td style="width: 50%;">
                            <div class="photo-card">
                                <img src="{{ $p['val'] }}" alt="{{ $p['key'] }}">
                                <div class="photo-title">{{ str_replace('_', ' ', strtoupper($p['key'])) }}</div>
                            </div>
                        </td>
                    @endforeach
                    @if(count($row) === 1)
                        <td style="width: 50%;"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- SECCIÓN 4: HISTORIAL DE INSPECCIONES -->
    <div class="section">
        <div class="section-header">
            <h2>4. Últimas Inspecciones de Control</h2>
        </div>
        @if($asset->dynamicInspections && $asset->dynamicInspections->count() > 0)
            <table class="data-table">
                <thead>
                    <tr style="background-color: #f1f5f9;">
                        <th style="padding: 6px; text-align: left; font-size: 8px; color: #475569; text-transform: uppercase;">Checklist</th>
                        <th style="padding: 6px; text-align: left; font-size: 8px; color: #475569; text-transform: uppercase;">Fecha</th>
                        <th style="padding: 6px; text-align: left; font-size: 8px; color: #475569; text-transform: uppercase;">Inspector</th>
                        <th style="padding: 6px; text-align: center; font-size: 8px; color: #475569; text-transform: uppercase;">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asset->dynamicInspections->take(6) as $insp)
                        <tr>
                            <td style="font-weight: bold;">{{ $insp->checklist?->title ?? 'Checklist' }}</td>
                            <td>{{ $insp->completed_at ? $insp->completed_at->format('d/m/Y H:i') : 'N/D' }}</td>
                            <td>{{ $insp->user?->name ?? 'Técnico' }}</td>
                            <td style="text-align: center;">
                                <span class="badge {{ $insp->result === 'Pass' ? 'badge-operativo' : 'badge-fuera' }}">
                                    {{ $insp->result }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #94a3b8; font-style: italic; padding: 10px 0;">No se registraron inspecciones previas para esta unidad.</p>
        @endif
    </div>

</body>
</html>

<?php
// Helper local para dividir arreglos asociativos en pares llave/valor para renderizado en tabla Blade
if (! function_exists('array_pairs_helper')) {
    function array_pairs_helper($arr) {
        $result = [];
        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
                $result[] = ['key' => $k, 'val' => $v];
            }
        }
        return $result;
    }
}
?>
