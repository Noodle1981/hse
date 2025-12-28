<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Inspección #{{ $inspection->id }}</title>
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
        }
        .logo-box {
            width: 60pt;
            height: 60pt;
            background-color: #2B6CB0;
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
            border-left: 4pt solid #2B6CB0;
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
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table tr td {
            padding: 8pt 5pt;
            border-bottom: 0.5pt solid #E2E8F0;
            vertical-align: top;
        }
        .question-label {
            font-weight: bold;
            width: 60%;
        }
        .answer-value {
            width: 40%;
            text-align: right;
            font-weight: 500;
        }
        .checkbox-badge {
            display: inline-block;
            background-color: #EBF8FF;
            color: #2B6CB0;
            padding: 2pt 6pt;
            border-radius: 4pt;
            font-size: 8pt;
            margin-left: 4pt;
            border: 0.5pt solid #BEE3F8;
        }
        .notes-box {
            background-color: #FEFCBF;
            border: 1pt solid #ECC94B;
            padding: 12pt;
            margin-top: 20pt;
            border-radius: 4pt;
        }
        .notes-title {
            font-size: 9pt;
            font-weight: bold;
            color: #856404;
            margin-bottom: 5pt;
            text-transform: uppercase;
        }
        .signatures-table {
            width: 100%;
            margin-top: 50pt;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            padding: 0 20pt;
        }
        .signature-line {
            border-top: 1pt solid #CBD5E0;
            margin-bottom: 5pt;
        }
        .signature-img {
            max-height: 80pt;
            margin-bottom: 5pt;
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
        .status-badge {
            font-size: 8pt;
            font-weight: bold;
            padding: 2pt 6pt;
            border-radius: 2pt;
            display: inline-block;
        }
        .status-completed { background-color: #C6F6D5; color: #22543D; }
        .status-draft { background-color: #FEEBC8; color: #744210; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 70pt;">
                <div class="logo-box">EP</div>
            </td>
            <td>
                <div class="report-title">{{ $inspection->checklist->title }}</div>
                <div class="report-metadata">
                    Reporte Digital de Inspección • Generado el {{ now()->format('d/m/Y H:i') }}
                </div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <div class="status-badge {{ $inspection->status === 'completed' ? 'status-completed' : 'status-draft' }}">
                    {{ $inspection->status === 'completed' ? 'COMPLETADO' : 'BORRADOR' }}
                </div>
                <div style="font-size: 9pt; color: #718096; margin-top: 4pt;">ID: #{{ $inspection->id }}</div>
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td style="width: 33%;">
                <div class="info-label">Empresa / Cliente</div>
                <div class="info-value">{{ $inspection->sede->empresa->nombre ?? 'N/A' }}</div>
            </td>
            <td style="width: 33%;">
                <div class="info-label">Sede / Ubicación</div>
                <div class="info-value">{{ $inspection->sede->nombre_sede ?? 'N/A' }}</div>
            </td>
            <td style="width: 33%;">
                <div class="info-label">Fecha Inspección</div>
                <div class="info-value">{{ $inspection->completed_at?->format('d/m/Y') ?? 'En proceso' }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10pt;">
                <div class="info-label">Inspector Asignado</div>
                <div class="info-value">{{ $inspection->user->nombres ?? 'N/A' }} {{ $inspection->user->apellidos ?? '' }}</div>
            </td>
            <td style="padding-top: 10pt;">
                <div class="info-label">Categoría</div>
                <div class="info-value">{{ ucfirst($inspection->checklist->category ?? 'Sin especificar') }}</div>
            </td>
            <td style="padding-top: 10pt;">
                <div class="info-label">Supervisor en Sitio</div>
                <div class="info-value">{{ $inspection->supervisor_name ?: 'No informado' }}</div>
            </td>
        </tr>
    </table>

    @php
        $groupedAnswers = $answers->groupBy(fn($a) => $a->question->section_id);
    @endphp

    @foreach($sections as $section)
        @if($groupedAnswers->has($section->id))
            <div class="section-header">
                <div class="section-title">{{ $section->title }}</div>
            </div>
            
            <table class="data-table">
                @foreach($groupedAnswers->get($section->id)->sortBy('question.id') as $answer)
                    <tr>
                        <td class="question-label">
                            {{ $answer->question->label }}
                        </td>
                        <td class="answer-value">
                            @if($answer->question->type === 'checkbox')
                                @php $vals = json_decode($answer->value, true) ?? []; @endphp
                                @foreach($vals as $v)
                                    <span class="checkbox-badge">✓ {{ $v }}</span>
                                @endforeach
                            @else
                                {{ $answer->value ?: '-' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endforeach

    @if($inspection->notes)
        <div class="notes-box">
            <div class="notes-title">Observaciones y Hallazgos</div>
            <div style="font-size: 9pt; color: #4A5568;">{{ $inspection->notes }}</div>
        </div>
    @endif

    <table class="signatures-table">
        <tr>
            <td class="signature-cell">
                @if($inspection->supervisor_signature)
                    <img src="{{ $inspection->supervisor_signature }}" class="signature-img">
                @else
                    <div style="height: 60pt;"></div>
                @endif
                <div class="signature-line"></div>
                <div class="info-label">Firma Supervisor</div>
                <div class="info-value" style="font-size: 8pt;">{{ $inspection->supervisor_name ?: '................................' }}</div>
            </td>
            <td class="signature-cell">
                @if($inspection->technician_signature)
                    <img src="{{ $inspection->technician_signature }}" class="signature-img">
                @else
                    <div style="height: 60pt;"></div>
                @endif
                <div class="signature-line"></div>
                <div class="info-label">Firma del Profesional</div>
                <div class="info-value" style="font-size: 8pt;">{{ $inspection->user->nombres ?? '' }} {{ $inspection->user->apellidos ?? '' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Este documento es un reporte oficial generado por el sistema EP Checklists.
        Página 1 de 1
    </div>
</body>
</html>
