<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plan Anual de Higiene y Seguridad</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            color: #333;
        }
        .header {
            width: 100%;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1e40af;
        }
        .header table { width: 100%; }
        .logo { width: 25%; }
        .logo img { max-height: 40px; }
        .title { font-size: 14px; font-weight: bold; text-align: right; color: #111; text-transform: uppercase; }
        
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        .info-table td { padding: 4px; border: 1px solid #e5e7eb; }
        .label { font-weight: bold; color: #6b7280; font-size: 7px; text-transform: uppercase; }
        .value { font-weight: bold; font-size: 9px; }

        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-table th {
            background-color: #1e40af;
            color: white;
            padding: 5px 2px;
            font-size: 8px;
            border: 1px solid #1e3a8a;
            text-transform: uppercase;
        }
        .main-table td {
            border: 1px solid #e5e7eb;
            padding: 4px;
            vertical-align: middle;
        }
        .category-row {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            font-size: 7px;
        }
        .cell-status {
            width: 15px;
            height: 15px;
            margin: 0 auto;
            border-radius: 3px;
            display: block;
            text-align: center;
            line-height: 15px;
            font-weight: bold;
            color: white;
            position: relative;
        }
        .bg-completed { background-color: #10b981; }
        .bg-pending { background-color: #4f46e5; }
        .bg-overdue { background-color: #ef4444; }
        .bg-none { background-color: #f3f4f6; border: 1px solid #e5e7eb; }
        
        .count-badge {
            font-size: 6px;
            position: absolute;
            top: -3px;
            right: -3px;
            background: white;
            color: #10b981;
            padding: 0 2px;
            border-radius: 5px;
            border: 1px solid #10b981;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td class="logo">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo">
                </td>
                <td class="title">PROGRAMA ANUAL DE ACTIVIDADES - {{ $year }}</td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td width="25%">
                <div class="label">Empresa</div>
                <div class="value">{{ $sede->empresa->nombre }}</div>
            </td>
            <td width="25%">
                <div class="label">Sede / Establecimiento</div>
                <div class="value">{{ $sede->nombre_sede }}</div>
            </td>
            <td width="25%">
                <div class="label">Año Fiscal</div>
                <div class="value">{{ $year }}</div>
            </td>
            <td width="25%">
                <div class="label">Fecha Emisión</div>
                <div class="value">{{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="20%">Actividad / Programa</th>
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
                    <td><strong>{{ $item['plan']->activity_name }}</strong></td>
                    <td align="center">
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
                                    @else
                                        X
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

    <div style="margin-top: 20px; font-size: 7px; color: #6b7280;">
        <strong>Referencias:</strong> 
        <span style="color: #4f46e5; font-weight: bold;">[X] Azul:</span> Planificado (Pendiente) | 
        <span style="color: #10b981; font-weight: bold;">[✓] Verde:</span> Cumplido | 
        <span style="color: #ef4444; font-weight: bold;">[X] Rojo:</span> Incumplimiento (Vencido)
    </div>

    <div class="footer">
        SOFTWARE DE GESTIÓN XAMASAFE • PROGRAMA ANUAL DE HIGIENE Y SEGURIDAD • GENERADO AUTOMÁTICAMENTE
    </div>
</body>
</html>
