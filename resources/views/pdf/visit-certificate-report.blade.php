<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Constancia de Visita #{{ $data['id'] }}</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #F69239;
            padding-bottom: 15px;
            margin-bottom: 20px;
            page-break-before: always;
        }
        .header:first-of-type {
            page-break-before: auto;
        }
        .header table {
            width: 100%;
        }
        .logo {
            width: 120px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #1E1E2D;
            text-align: right;
        }
        .report-title {
            font-size: 14px;
            color: #F69239;
            text-align: right;
            margin-top: 5px;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 6px 10px;
            font-weight: bold;
            border-left: 4px solid #F69239;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 10px;
            color: #1E1E2D;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-grid td {
            padding: 6px;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }
        .label {
            font-weight: bold;
            color: #666;
            width: 25%;
        }
        .value {
            color: #1E1E2D;
        }
        .observation-box {
            padding: 10px;
            background-color: #fafafa;
            border: 1px solid #eee;
            border-radius: 4px;
            min-height: 60px;
        }
        .activity-item {
            padding: 4px 0;
            border-bottom: 1px dashed #eee;
        }
        .activity-time {
            color: #F69239;
            font-weight: bold;
            width: 50px;
            display: inline-block;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            width: 48%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #ccc;
            margin: 10px 20px;
            padding-top: 5px;
        }
        .signature-img {
            max-height: 80px;
            max-width: 150px;
            margin-bottom: 5px;
        }
        .justification-badge {
            display: block;
            background-color: #fff7ed;
            border: 1px dashed #fdba74;
            color: #9a3412;
            padding: 10px;
            margin: 10px 20px;
            font-style: italic;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 8px;
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
            .header:first-of-type {
                margin-top: 0;
            }
            .header {
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
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <img src="{{ public_path('images/logo_ep_viejo.jpg') }}" class="logo">
                </td>
                <td>
                    <div class="company-title">HIGIENE Y SEGURIDAD</div>
                    <div class="report-title">Constancia de Visita #{{ str_pad($data['id'], 6, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-grid">
        <tr>
            <td class="label">Razón Social:</td>
            <td class="value"><strong>{{ $data['razon_social'] }}</strong></td>
            <td class="label">CUIT:</td>
            <td class="value">{{ $data['cuit'] }}</td>
        </tr>
        <tr>
            <td class="label">Sede:</td>
            <td class="value">{{ $data['sede']['nombre_sede'] }}</td>
            <td class="label">Fecha de Visita:</td>
            <td class="value" style="color: #F69239; font-weight: bold;">{{ $data['fecha_visita'] }}</td>
        </tr>
        <tr>
            <td class="label">Dirección:</td>
            <td colspan="3" class="value">
                {{ $data['sede']['direccion'] ?? 'Sin dirección' }}
                @if($data['sede']['localidad']) - {{ $data['sede']['localidad'] }} @endif
            </td>
        </tr>
        <tr>
            <td class="label">Tipo de Visita:</td>
            <td colspan="3" class="value">{{ $data['tipo_visita'] }}</td>
        </tr>
    </table>

    <div class="section-title">Observaciones y Detalles de la Visita</div>
    <div class="observation-box">
        {!! nl2br(e($data['observacion'])) !!}
    </div>

    @if(!empty($data['actividades']['inspections']) && $data['actividades']['inspections']->count() > 0)
        <div class="section-title">Inspecciones / Checklists Realizados</div>
        @foreach($data['actividades']['inspections'] as $inspection)
            <div class="activity-item">
                <span class="activity-time">{{ $inspection->completed_at->format('H:i') }} hs</span>
                {{ $inspection->checklist->title }}
            </div>
        @endforeach
    @endif

    @if(!empty($data['actividades']['trainings']) && $data['actividades']['trainings']->count() > 0)
        <div class="section-title">Capacitaciones Realizadas</div>
        @foreach($data['actividades']['trainings'] as $training)
            <div class="activity-item">
                <span class="activity-time">{{ $training->date->format('H:i') }} hs</span>
                {{ $training->title }}
            </div>
        @endforeach
    @endif

    @if(!empty($data['actividades']['hallazgos']) && $data['actividades']['hallazgos']->count() > 0)
        <div class="section-title">Hallazgos Detectados</div>
        @foreach($data['actividades']['hallazgos'] as $hallazgo)
            <div class="activity-item">
                <span class="activity-time">{{ $hallazgo->created_at->format('H:i') }} hs</span>
                <strong>{{ $hallazgo->titulo }}</strong> 
                <span style="color: #666;">(Criticidad: {{ ucfirst($hallazgo->criticidad) }})</span>
            </div>
        @endforeach
    @endif

    @if(!empty($data['actividades']['riskEvaluations']) && $data['actividades']['riskEvaluations']->count() > 0)
        <div class="section-title">Evaluaciones de Riesgo</div>
        @foreach($data['actividades']['riskEvaluations'] as $evaluation)
            <div class="activity-item">
                <span class="activity-time">{{ $evaluation->evaluation_date->format('H:i') }} hs</span>
                {{ $evaluation->risk->name }}
            </div>
        @endforeach
    @endif

    @if(!empty($data['actividades']['eppDeliveries']) && $data['actividades']['eppDeliveries']->count() > 0)
        <div class="section-title">Entregas de EPP</div>
        @foreach($data['actividades']['eppDeliveries'] as $delivery)
            <div class="activity-item">
                <span class="activity-time">{{ $delivery->delivery_date->format('H:i') }} hs</span>
                {{ $delivery->employee->name }} {{ $delivery->employee->last_name }}
            </div>
        @endforeach
    @endif

    <table class="signatures">
        <tr>
            <td class="signature-box">
                @if($data['justificacion_firma'])
                    <div class="justification-badge">
                        <strong>Firma no disponible:</strong><br>
                        {{ $data['justificacion_firma'] }}
                    </div>
                @else
                    @if($data['firma_responsable'])
                        <img src="{{ $data['firma_responsable'] }}" class="signature-img">
                    @else
                        <div style="height: 80px;"></div>
                    @endif
                @endif
                <div class="signature-line">
                    <strong>{{ $data['aclaracion_responsable'] ?: 'Firma Responsable' }}</strong><br>
                    Responsable de Empresa
                </div>
            </td>
            <td class="signature-box">
                @if($data['firma_instructor'])
                    <img src="{{ $data['firma_instructor'] }}" class="signature-img">
                @else
                    <div style="height: 80px;"></div>
                @endif
                <div class="signature-line">
                    <strong>{{ $data['aclaracion_instructor'] }}</strong><br>
                    Asesor HyS {{ $data['titulo_instructor'] ? '- ' . $data['titulo_instructor'] : '' }}
                    @if($data['matricula_instructor']) <br> M.P. {{ $data['matricula_instructor'] }} @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Constancia generada digitalmente por Sistema EP - {{ date('d/m/Y H:i:s') }}<br>
        Documento válido como comprobante de visita técnica de Higiene y Seguridad.
    </div>
</body>
</html>
