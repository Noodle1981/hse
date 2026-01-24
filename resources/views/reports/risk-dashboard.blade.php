<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Riesgos Operativos</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        .header { border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 20px; }
        .header table { width: 100%; }
        .logo img { max-height: 50px; }
        .report-title { text-align: right; font-size: 16px; font-weight: bold; color: #111; text-transform: uppercase; }
        
        .info-grid { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-grid td { padding: 8px; background: #f9fafb; border: 1px solid #e5e7eb; }
        .label { font-weight: bold; color: #6b7280; font-size: 8px; text-transform: uppercase; }
        .value { font-size: 11px; font-weight: bold; color: #111; }

        .kpi-container { width: 100%; margin-bottom: 25px; }
        .kpi-card { width: 19%; display: inline-block; padding: 10px; border-radius: 8px; border: 1px solid #e5e7eb; text-align: center; margin-right: 1%; }
        .kpi-label { font-size: 7px; text-transform: uppercase; color: #6b7280; margin-bottom: 5px; font-weight: bold; }
        .kpi-value { font-size: 18px; font-weight: 900; }
        
        /* Heatmap Styles */
        .heatmap-container { width: 100%; margin-top: 20px; }
        .heatmap-table { border-collapse: separate; border-spacing: 5px; width: 100%; }
        .heatmap-cell { width: 18%; height: 60px; text-align: center; vertical-align: middle; border-radius: 6px; color: white; font-weight: bold; font-size: 16px; border: 1px solid rgba(0,0,0,0.1); }
        .axis-label { font-size: 8px; font-weight: bold; color: #94a3b8; text-transform: uppercase; text-align: center; }
        
        .bg-red { background-color: #ef4444; }
        .bg-orange { background-color: #f97316; }
        .bg-yellow { background-color: #eab308; }
        .bg-green { background-color: #22c55e; }
        .opacity-20 { opacity: 0.2; }

        .legend { margin-top: 30px; padding: 15px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; }
        .legend-title { font-weight: bold; margin-bottom: 10px; font-size: 10px; color: #1e40af; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        .legend-item { width: 24%; display: inline-block; font-size: 9px; }
        .dot { width: 8px; height: 8px; display: inline-block; border-radius: 50%; margin-right: 5px; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td class="logo"><img src="{{ public_path('images/logo.png') }}" alt="Logo"></td>
                <td class="report-title">Dashboard de Riesgos Operativos</td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td width="33%">
                <div class="label">Filtro Aplicado</div>
                <div class="value">{{ $matrix ? $matrix->title : 'Vista Global (Todas)' }}</div>
            </td>
            @if($matrix)
            <td width="33%">
                <div class="label">Sede</div>
                <div class="value">{{ $matrix->sede->nombre_sede }}</div>
            </td>
            @endif
            <td width="{{ $matrix ? '34%' : '67%' }}">
                <div class="label">Fecha del Reporte</div>
                <div class="value">{{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <div class="kpi-container">
        <div class="kpi-card" style="background: #f8fafc;">
            <div class="kpi-label">Total Riesgos</div>
            <div class="kpi-value" style="color: #1e293b;">{{ $kpis['total_risks'] }}</div>
        </div>
        <div class="kpi-card" style="background: #fef2f2; border-color: #fee2e2;">
            <div class="kpi-label" style="color: #ef4444;">Críticos</div>
            <div class="kpi-value" style="color: #b91c1c;">{{ $kpis['critical'] }}</div>
        </div>
        <div class="kpi-card" style="background: #fff7ed; border-color: #ffedd5;">
            <div class="kpi-label" style="color: #f97316;">Altos</div>
            <div class="kpi-value" style="color: #c2410c;">{{ $kpis['high'] }}</div>
        </div>
        <div class="kpi-card" style="background: #fefce8; border-color: #fef9c3;">
            <div class="kpi-label" style="color: #eab308;">Medios</div>
            <div class="kpi-value" style="color: #a16207;">{{ $kpis['medium'] }}</div>
        </div>
        <div class="kpi-card" style="background: #f0fdf4; border-color: #dcfce7;">
            <div class="kpi-label" style="color: #22c55e;">Bajos</div>
            <div class="kpi-value" style="color: #15803d;">{{ $kpis['low'] }}</div>
        </div>
    </div>

    <div class="heatmap-container">
        <table class="heatmap-table">
            @for($s = 5; $s >= 1; $s--)
                <tr>
                    @for($p = 1; $p <= 5; $p++)
                        @php
                            $count = $heatmapData[$s][$p];
                            $score = $s * $p;
                            $color = 'bg-green';
                            if ($score >= 17) $color = 'bg-red';
                            elseif ($score >= 10) $color = 'bg-orange';
                            elseif ($score >= 5) $color = 'bg-yellow';
                        @endphp
                        <td class="heatmap-cell {{ $color }} {{ $count == 0 ? 'opacity-20' : '' }}">
                            {{ $count }}
                        </td>
                    @endfor
                </tr>
            @endfor
            <tr>
                @for($p = 1; $p <= 5; $p++)
                    <td class="axis-label" style="padding-top: 10px;">Prob. {{ $p }}</td>
                @endfor
            </tr>
        </table>
        <div style="text-align: center; margin-top: 10px; font-weight: bold; color: #94a3b8; font-size: 8px; text-transform: uppercase;">
            Probabilidad de Ocurrencia
        </div>
    </div>

    <div class="legend">
        <div class="legend-title">Leyenda de Criticidad (ISO 31000)</div>
        <div class="legend-item"><span class="dot bg-red"></span> <strong>Crítico</strong> (17-25)</div>
        <div class="legend-item"><span class="dot bg-orange"></span> <strong>Alto</strong> (10-16)</div>
        <div class="legend-item"><span class="dot bg-yellow"></span> <strong>Medio</strong> (5-9)</div>
        <div class="legend-item"><span class="dot bg-green"></span> <strong>Bajo</strong> (1-4)</div>
    </div>

    <div class="footer">
        SOFTWARE DE GESTIÓN XAMASAFE • REPORTE EJECUTIVO DE RIESGOS • GENERADO AUTOMÁTICAMENTE
    </div>
</body>
</html>
