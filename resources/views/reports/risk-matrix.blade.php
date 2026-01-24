<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Matriz de Riesgos</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .logo {
            width: 30%;
        }
        .logo img {
            max-height: 45px;
        }
        .report-title {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-grid td {
            padding: 5px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
            font-size: 8px;
            text-transform: uppercase;
        }
        .value {
            font-size: 10px;
            font-weight: bold;
            color: #111;
        }
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.main-table th {
            background-color: #1e40af;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        table.main-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            vertical-align: top;
        }
        .risk-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        .control-item {
            display: block;
            margin-bottom: 2px;
            font-size: 8px;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            width: 100%;
            text-align: center;
            font-size: 8px;
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
                <td class="report-title">MATRIZ DE IDENTIFICACIÓN DE PELIGROS Y EVALUACIÓN DE RIESGOS</td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td width="33%">
                <div class="label">Empresa</div>
                <div class="value">{{ $matrix->empresa->nombre }}</div>
            </td>
            <td width="33%">
                <div class="label">Sede / Establecimiento</div>
                <div class="value">{{ $matrix->sede->nombre_sede }}</div>
            </td>
            <td width="34%">
                <div class="label">Título de la Matriz</div>
                <div class="value">{{ $matrix->title }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Área</div>
                <div class="value">{{ $matrix->area ?: 'No definida' }}</div>
            </td>
            <td>
                <div class="label">Proceso</div>
                <div class="value">{{ $matrix->process ?: 'No definido' }}</div>
            </td>
            <td>
                <div class="label">Fecha del Informe</div>
                <div class="value">{{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">N°</th>
                <th width="15%">Peligro</th>
                <th width="10%">Tipo</th>
                <th width="15%">Consecuencia</th>
                <th width="20%">Medidas de Control</th>
                <th width="5%">Prob.</th>
                <th width="5%">Sev.</th>
                <th width="7%">Score</th>
                <th width="10%">Nivel Riesgo</th>
                <th width="10%">Últ. Eval.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($matrix->risks as $index => $risk)
                @php
                    $eval = $risk->latestEvaluation;
                @endphp
                <tr>
                    <td align="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $risk->hazard }}</strong></td>
                    <td>{{ $risk->type }}</td>
                    <td>{{ $risk->consequence }}</td>
                    <td>
                        @foreach($risk->controls as $control)
                            <div class="control-item">• [{{ $control->type }}] {{ $control->measure }}</div>
                        @endforeach
                    </td>
                    <td align="center">{{ $eval ? $eval->probability : '-' }}</td>
                    <td align="center">{{ $eval ? $eval->severity : '-' }}</td>
                    <td align="center"><strong>{{ $eval ? $eval->score : '-' }}</strong></td>
                    <td align="center">
                        @if($eval)
                            <span class="risk-badge" style="background-color: {{ $eval->color }}">
                                {{ $eval->level }}
                            </span>
                        @else
                            <span style="color: #9ca3af; font-style: italic;">Sin evaluar</span>
                        @endif
                    </td>
                    <td align="center">{{ $eval ? ($eval->evaluation_date instanceof \Carbon\Carbon ? $eval->evaluation_date->format('d/m/Y') : $eval->evaluation_date) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        SOFTWARE DE GESTIÓN XAMASAFE • GENERADO AUTOMÁTICAMENTE
    </div>
</body>
</html>
