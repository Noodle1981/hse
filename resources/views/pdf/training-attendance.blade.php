<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Asistencias - {{ $training->title }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #2D3748;
            line-height: 1.5;
        }
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #1A365D;
            padding-bottom: 10pt;
            margin-bottom: 20pt;
            page-break-before: always;
        }
        .header-table:first-of-type {
            page-break-before: auto;
        }
        .logo-box {
            width: 60pt;
            height: 60pt;
            background-color: #F69239;
            color: white;
            font-weight: bold;
            font-size: 24pt;
            text-align: center;
            line-height: 60pt;
            border-radius: 4pt;
        }
        .report-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1A365D;
            margin: 0;
            padding: 0;
        }
        .report-metadata {
            font-size: 9pt;
            color: #718096;
        }
        .section-header {
            background-color: #F7FAFC;
            padding: 8pt 12pt;
            border-left: 4pt solid #F69239;
            margin-top: 20pt;
            margin-bottom: 10pt;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #2D3748;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 15pt;
        }
        .info-label {
            font-size: 8pt;
            font-weight: bold;
            color: #A0AEC0;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 10pt;
            color: #2D3748;
            font-weight: 500;
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15pt;
        }
        .attendance-table thead {
            background-color: #1E1E2D;
            color: white;
        }
        .attendance-table th {
            padding: 8pt;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .attendance-table td {
            padding: 8pt;
            border-bottom: 0.5pt solid #E2E8F0;
            vertical-align: middle;
        }
        .attendance-table tr:nth-child(even) {
            background-color: #F7FAFC;
        }
        .signature-img {
            max-height: 40pt;
            max-width: 100pt;
        }
        .stats-box {
            background-color: #EBF8FF;
            border: 1pt solid #90CDF4;
            padding: 12pt;
            margin-bottom: 20pt;
            border-radius: 4pt;
        }
        .stats-grid {
            display: table;
            width: 100%;
        }
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 5pt;
        }
        .stat-value {
            font-size: 24pt;
            font-weight: bold;
            color: #2B6CB0;
        }
        .stat-label {
            font-size: 8pt;
            color: #4A5568;
            text-transform: uppercase;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 8pt;
            color: #A0AEC0;
            text-align: center;
            padding-top: 10pt;
            border-top: 0.5pt solid #E2E8F0;
        }

        /* Estilos para previsualización en navegador */
        @media screen {
            body {
                background-color: #f0f2f5;
                padding: 30px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .header-table:first-of-type {
                margin-top: 0;
            }
            .header-table {
                margin-top: 50px;
            }
            body > * {
                background: white;
                width: 210mm;
                padding: 40px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .footer {
                position: relative;
                margin-top: 20px;
                background: transparent;
                box-shadow: none;
                width: 100%;
            }
        }
        .status-badge {
            font-size: 8pt;
            font-weight: bold;
            padding: 2pt 6pt;
            border-radius: 2pt;
            display: inline-block;
        }
        .status-scheduled { background-color: #BEE3F8; color: #2C5282; }
        .status-completed { background-color: #C6F6D5; color: #22543D; }
        .status-cancelled { background-color: #FED7D7; color: #742A2A; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 70pt;">
                <div class="logo-box">EP</div>
            </td>
            <td>
                <div class="report-title">Registro de Asistencias</div>
                <div class="report-metadata">
                    Capacitación • Generado el {{ now()->format('d/m/Y H:i') }}
                </div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <div class="status-badge {{ $training->status === 'completed' ? 'status-completed' : ($training->status === 'cancelled' ? 'status-cancelled' : 'status-scheduled') }}">
                    {{ strtoupper($training->status) }}
                </div>
                <div style="font-size: 9pt; color: #718096; margin-top: 4pt;">ID: #{{ $training->id }}</div>
            </td>
        </tr>
    </table>

    {{-- Información de la Capacitación --}}
    <div class="section-header">
        <div class="section-title">Datos de la Capacitación</div>
    </div>

    <table class="info-grid">
        <tr>
            <td style="width: 50%;">
                <div class="info-label">Título</div>
                <div class="info-value">{{ $training->title }}</div>
            </td>
            <td style="width: 50%;">
                <div class="info-label">Tipo</div>
                <div class="info-value">{{ ucfirst($training->type) }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10pt;">
                <div class="info-label">Empresa / Cliente</div>
                <div class="info-value">{{ $training->empresa->nombre ?? 'N/A' }}</div>
            </td>
            <td style="padding-top: 10pt;">
                <div class="info-label">Sede / Ubicación</div>
                <div class="info-value">{{ $training->sede->nombre_sede ?? 'N/A' }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10pt;">
                <div class="info-label">Fecha</div>
                <div class="info-value">{{ $training->date->format('d/m/Y') }}</div>
            </td>
            <td style="padding-top: 10pt;">
                <div class="info-label">Duración</div>
                <div class="info-value">{{ $training->duration }} horas</div>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10pt;">
                <div class="info-label">Capacitador</div>
                <div class="info-value">{{ $training->trainer_name ?? 'No especificado' }}</div>
            </td>
            <td style="padding-top: 10pt;">
                <div class="info-label">Responsable</div>
                <div class="info-value">{{ $training->responsibleUser->name ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    @if($training->description)
        <table class="info-grid">
            <tr>
                <td>
                    <div class="info-label">Descripción</div>
                    <div class="info-value">{{ $training->description }}</div>
                </td>
            </tr>
        </table>
    @endif

    {{-- Estadísticas --}}
    <div class="stats-box">
        <table style="width: 100%;">
            <tr>
                <td class="stat-item">
                    <div class="stat-value">{{ $attendances->count() }}</div>
                    <div class="stat-label">Total Asistentes</div>
                </td>
                <td class="stat-item">
                    <div class="stat-value">{{ $training->duration ?? 0 }}h</div>
                    <div class="stat-label">Duración</div>
                </td>
                <td class="stat-item">
                    <div class="stat-value">{{ $training->date->format('d/m') }}</div>
                    <div class="stat-label">Fecha</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Lista de Asistencias --}}
    <div class="section-header">
        <div class="section-title">Registro de Asistentes</div>
    </div>

    @if($attendances->count() > 0)
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Nombre Completo</th>
                    <th style="width: 15%;">DNI</th>
                    <th style="width: 20%;">Hora Registro</th>
                    <th style="width: 25%;">Firma</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $attendance)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                        <td>{{ $attendance->employee_name }}</td>
                        <td>{{ number_format($attendance->dni, 0, '', '.') }}</td>
                        <td style="font-size: 9pt;">{{ $attendance->created_at->format('d/m/Y H:i') }}</td>
                        <td style="text-align: center;">
                            @if($attendance->signature)
                                <img src="{{ $attendance->signature }}" class="signature-img">
                            @else
                                <span style="color: #A0AEC0;">Sin firma</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40pt; color: #A0AEC0;">
            <div style="font-size: 14pt; margin-bottom: 5pt;">📋</div>
            <div>No se registraron asistencias para esta capacitación</div>
        </div>
    @endif

    {{-- Firma del Capacitador --}}
    @if($training->trainer_name)
        <table style="width: 100%; margin-top: 50pt;">
            <tr>
                <td style="width: 50%; text-align: center; padding: 0 20pt;">
                    <div style="height: 60pt;"></div>
                    <div style="border-top: 1pt solid #CBD5E0; margin-bottom: 5pt;"></div>
                    <div class="info-label">Firma del Capacitador</div>
                    <div class="info-value" style="font-size: 8pt;">{{ $training->trainer_name }}</div>
                </td>
                <td style="width: 50%; text-align: center; padding: 0 20pt;">
                    <div style="height: 60pt;"></div>
                    <div style="border-top: 1pt solid #CBD5E0; margin-bottom: 5pt;"></div>
                    <div class="info-label">Responsable de Capacitación</div>
                    <div class="info-value" style="font-size: 8pt;">{{ $training->responsibleUser->name ?? 'N/A' }}</div>
                </td>
            </tr>
        </table>
    @endif

    <div class="footer">
        Este documento es un reporte oficial generado por el sistema EP Checklists.
        Página 1 de 1 • {{ $attendances->count() }} asistente(s) registrado(s)
    </div>
</body>
</html>
