<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Constancia de Entrega de EPP - Res. 299/11</title>
    <style>
        @page {
            margin: 1.5cm 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #2d3748;
            line-height: 1.5;
        }
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #1a365d;
            padding-bottom: 10pt;
            margin-bottom: 20pt;
        }
        .logo-box {
            width: 50pt;
            height: 50pt;
            background-color: #2b6cb0;
            color: white;
            font-weight: bold;
            font-size: 20pt;
            text-align: center;
            line-height: 50pt;
            border-radius: 4pt;
        }
        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1a365d;
            margin: 0;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 9pt;
            color: #718096;
            margin-top: 2pt;
        }
        .section-header {
            background-color: #f7fafc;
            padding: 6pt 10pt;
            border-left: 4pt solid #2b6cb0;
            margin-top: 15pt;
            margin-bottom: 8pt;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }
        .info-table td {
            padding: 6pt 8pt;
            border-bottom: 0.5pt solid #e2e8f0;
            vertical-align: top;
        }
        .label {
            font-size: 8pt;
            font-weight: bold;
            color: #718096;
            text-transform: uppercase;
            width: 100pt;
        }
        .value {
            font-size: 10pt;
            color: #2d3748;
            font-weight: 500;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10pt;
        }
        .content-table th {
            background-color: #1a365d;
            color: white;
            padding: 8pt;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            border: 0.5pt solid #1a365d;
        }
        .content-table td {
            border: 0.5pt solid #e2e8f0;
            padding: 8pt;
            text-align: center;
            color: #4a5568;
        }
        .signature-container {
            margin-top: 30pt;
            width: 100%;
        }
        .signature-box {
            border: 1pt dashed #cbd5e0;
            background-color: #f8fafc;
            padding: 15pt;
            text-align: center;
            border-radius: 8pt;
            height: 100pt;
        }
        .signature-img {
            max-height: 70pt;
            max-width: 180pt;
        }
        .obs-box {
            border: 1pt solid #e2e8f0;
            padding: 12pt;
            background-color: #fff;
            border-radius: 8pt;
            height: 100pt;
            font-size: 9pt;
            color: #4a5568;
        }
        .footer {
            margin-top: 30pt;
            padding-top: 10pt;
            border-top: 0.5pt solid #e2e8f0;
            font-size: 7.5pt;
            color: #a0aec0;
            text-align: center;
        }
        .law-tag {
            background-color: #1a365d;
            color: white;
            padding: 2pt 8pt;
            border-radius: 10pt;
            font-size: 7.5pt;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 55pt;">
                <div class="logo-box">EP</div>
            </td>
            <td>
                <div class="report-title">Constancia de Entrega de EPP</div>
                <div class="report-subtitle">Sistema de Gestión de Higiene y Seguridad Laboral</div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div class="law-tag">RESOLUCIÓN S.R.T. 299/11</div>
                <div style="font-size: 8pt; color: #718096; margin-top: 4pt;">ID Entrega: #{{ $delivery->id }}</div>
            </td>
        </tr>
    </table>

    <div class="section-header">
        <div class="section-title">Información del Empleador</div>
    </div>
    <table class="info-table">
        <tr>
            <td class="label">Razón Social</td>
            <td class="value">{{ $delivery->employee->empresa->razon_social ?: $delivery->employee->empresa->nombre }}</td>
            <td class="label">C.U.I.T.</td>
            <td class="value">{{ $delivery->employee->empresa->cuit ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Establecimiento / Sede</td>
            <td class="value" colspan="3">{{ $delivery->employee->sede->nombre_sede }} - {{ $delivery->employee->sede->direccion }}, {{ $delivery->employee->sede->localidad }}</td>
        </tr>
    </table>

    <div class="section-header">
        <div class="section-title">Información del Trabajador</div>
    </div>
    <table class="info-table">
        <tr>
            <td class="label">Nombre y Apellido</td>
            <td class="value">{{ $delivery->employee->full_name }}</td>
            <td class="label">D.N.I.</td>
            <td class="value">{{ $delivery->employee->dni }}</td>
        </tr>
        <tr>
            <td class="label">C.U.I.L.</td>
            <td class="value">{{ $delivery->employee->cuil ?: 'N/A' }}</td>
            <td class="label">Puesto de Trabajo</td>
            <td class="value">{{ $delivery->employee->puesto ?: 'No especificado' }}</td>
        </tr>
    </table>

    <div class="section-header">
        <div class="section-title">Equipamiento Entregado</div>
    </div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 40pt;">Cant.</th>
                <th style="text-align: left;">Descripción del EPP</th>
                <th>Tipo</th>
                <th>Certificación / Norma</th>
                <th style="width: 70pt;">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delivery->items as $item)
            <tr>
                <td><strong>{{ $item->pivot->cantidad }}</strong></td>
                <td style="text-align: left; color: #2d3748; font-weight: 500;">{{ $item->nombre }}</td>
                <td><span style="font-size: 8pt; color: #718096;">{{ $item->tipo }}</span></td>
                <td>{{ $item->codigo_norma ?: 'S/D' }}</td>
                <td>{{ $delivery->delivery_date->format('d/m/Y') }}</td>
            </tr>
            @endforeach
            @for($i = count($delivery->items); $i < 4; $i++)
            <tr>
                <td style="height: 20pt;">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 25pt; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="font-size: 8pt; font-weight: bold; color: #718096; text-transform: uppercase; margin-bottom: 5pt;">Firma del Trabajador</div>
                <div class="signature-box">
                    @if($delivery->signature)
                        <img src="{{ $delivery->signature }}" class="signature-img">
                    @else
                        <div style="margin-top: 35pt; color: #cbd5e0; font-size: 8pt;">SIN FIRMA REGISTRADA</div>
                    @endif
                </div>
                <div style="text-align: center; margin-top: 5pt; font-size: 8pt; color: #718096;">
                    {{ $delivery->employee->full_name }}<br>
                    DNI: {{ $delivery->employee->dni }}
                </div>
            </td>
            <td style="width: 50%; padding-left: 20pt; vertical-align: top;">
                <div style="font-size: 8pt; font-weight: bold; color: #718096; text-transform: uppercase; margin-bottom: 5pt;">Observaciones de Entrega</div>
                <div class="obs-box">
                    {{ $delivery->observaciones ?: 'Sin observaciones adicionales registradas para esta entrega.' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <div style="margin-bottom: 8pt; line-height: 1.6;">
            <strong>Declaración Jurada:</strong> El trabajador declara haber recibido los elementos detallados, haber sido capacitado en su uso correcto 
            y se compromete a utilizarlos obligatoriamente durante su jornada laboral conforme a la normativa vigente.
        </div>
        Documento generado digitalmente por <strong>EP-Back Platform</strong> • {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>

</body>
</html>
