<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $report->titulo }}</title>
    <style>
        /* Configuración de Página */
        @page {
            margin: 0; /* Margen cero para que la portada sea total */
        }
        
        body { 
            font-family: 'Helvetica', sans-serif; 
            color: #1e1e2d; 
            line-height: 1.4; 
            margin: 0; 
            padding: 0; 
            font-size: 10px; 
        }

        /* Utilidades de Salto de Página */
        .page-break { page-break-after: always; }
        .no-break { page-break-inside: avoid; }

        /* PORTADA */
        .cover { 
            width: 100%; 
            height: 1050px; /* Altura fija para A4 @ 96dpi aprox */
            background-color: #1e1e2d; 
            color: white; 
            position: relative;
        }
        .cover-content { 
            padding-top: 200px; 
            text-align: center; 
        }
        .logo { max-width: 250px; margin-bottom: 30px; }
        .cover h1 { 
            font-size: 36px; 
            color: {{ $branding['primary'] }}; 
            text-transform: uppercase; 
            margin: 0; 
            letter-spacing: 2px; 
        }
        .cover h2 { font-size: 18px; color: #B5B5C3; margin: 10px 0; font-weight: normal; }
        
        .cover-info-table { 
            margin: 80px auto 0; 
            width: 70%; 
            border: none; 
        }
        .cover-info-table td { 
            color: white; 
            border: none; 
            padding: 10px; 
            font-size: 14px; 
            background: transparent !important;
        }

        /* CONTENIDO DE PÁGINAS */
        .content-wrapper { 
            padding: 40px 50px; 
            position: relative; 
            min-height: 970px;
        }

        /* Header y Footer */
        .header { border-bottom: 2px solid {{ $branding['primary'] }}; margin-bottom: 20px; padding-bottom: 10px; width: 100%; }
        .header-title { font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .header-meta { float: right; font-size: 9px; color: #7e8299; }
        
        .footer { 
            position: fixed; 
            bottom: 20px; 
            left: 50px; 
            right: 50px; 
            font-size: 8px; 
            color: #b5b5c3; 
            border-top: 1px solid #ebedf3; 
            padding-top: 5px; 
            text-align: center;
        }
        .page-number:before { content: "Página " counter(page); }

        /* Secciones */
        .section { margin-bottom: 30px; }
        .section-header { 
            background: #f4f4f7; 
            padding: 8px 12px; 
            border-left: 4px solid {{ $branding['primary'] }}; 
            margin-bottom: 10px; 
        }
        .section-header h3 { margin: 0; font-size: 10px; text-transform: uppercase; color: #1e1e2d; }

        /* KPIs */
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 20px; }
        .kpi-box { 
            background: #ffffff; 
            border: 1px solid #ebedf3; 
            border-radius: 6px; 
            padding: 15px 5px; 
            text-align: center; 
            width: 25%;
        }
        .kpi-val { font-size: 20px; font-weight: bold; color: {{ $branding['primary'] }}; display: block; }
        .kpi-label { font-size: 7px; text-transform: uppercase; color: #7e8299; font-weight: bold; }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #1e1e2d; color: white; padding: 6px; text-align: left; font-size: 8px; text-transform: uppercase; border: 1px solid #1e1e2d; }
        td { padding: 7px; border: 1px solid #ebedf3; vertical-align: top; font-size: 9px; }
        tr:nth-child(even) { background-color: #fcfcfd; }
        
        /* Cajas de Observación */
        .obs-container { margin: 10px 0; }
        .observation-label { font-size: 8px; font-weight: bold; color: #7e8299; text-transform: uppercase; margin-bottom: 3px; }
        .observation-box { 
            background: #fffcf5; 
            border: 1px solid #fff4de; 
            padding: 10px; 
            border-radius: 4px; 
            color: #3f4254; 
            font-style: italic; 
            border-left: 3px solid #ffcd13;
            white-space: pre-wrap;
        }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7px; font-weight: bold; text-transform: uppercase; }
        .badge-warning { background: #fff4de; color: #ffa800; }
        .badge-danger { background: #ffe2e5; color: #f64e60; }
        .badge-success { background: #c9f7f5; color: #1bc5bd; }

        .clear { clear: both; }
    </style>
</head>
<body>

    {{-- 1. PORTADA --}}
    <div class="cover">
        <div class="cover-content">
            @if(file_exists($branding['logo'] ?? ''))
                <img src="{{ $branding['logo'] }}" class="logo">
            @else
                <div style="font-size: 40px; font-weight: bold; color: {{ $branding['primary'] }}; margin-bottom: 40px;">{{ $branding['name'] ?? 'EP SALUD Y SEGURIDAD' }}</div>
            @endif
            
            <h1>INFORME DE GESTIÓN</h1>
            <h2>{{ strtoupper(\Carbon\Carbon::parse($report->periodo)->translatedFormat('F Y')) }}</h2>
            
            <table class="cover-info-table">
                <tr>
                    <td style="text-align: right; color: #B5B5C3; width: 40%;">CLIENTE:</td>
                    <td style="text-align: left; font-weight: bold;">{{ $report->sede->empresa->nombre }}</td>
                </tr>
                <tr>
                    <td style="text-align: right; color: #B5B5C3;">SEDE:</td>
                    <td style="text-align: left; font-weight: bold;">{{ $report->sede->nombre_sede }}</td>
                </tr>
                <tr>
                    <td style="text-align: right; color: #B5B5C3;">TÉCNICO:</td>
                    <td style="text-align: left; font-weight: bold;">{{ $report->tecnico->nombres }} {{ $report->tecnico->apellidos }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- 2. RESUMEN Y VISITAS --}}
    <div class="content-wrapper">
        <div class="header">
            <span class="header-meta">REPORTE #{{ $report->id }} | {{ date('d/m/Y') }}</span>
            <span class="header-title">Resumen de Actividades</span>
        </div>

        <table class="kpi-table">
            <tr>
                <td class="kpi-box"><span class="kpi-val">{{ count($data['visits']) }}</span><span class="kpi-label">Visitas</span></td>
                <td class="kpi-box"><span class="kpi-val">{{ count($data['checklists']) }}</span><span class="kpi-label">Controles</span></td>
                <td class="kpi-box"><span class="kpi-val">{{ count($data['trainings']) }}</span><span class="kpi-label">Formación</span></td>
                <td class="kpi-box"><span class="kpi-val" style="color:#f64e60;">{{ count($data['hallazgos']) }}</span><span class="kpi-label">Hallazgos</span></td>
            </tr>
        </table>

        <div class="section">
            <div class="section-header"><h3>1. Cronograma de Visitas Técnicas</h3></div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%">Fecha</th>
                        <th style="width: 20%">Referencia</th>
                        <th>Observaciones de Campo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['visits'] as $visit)
                        <tr>
                            <td><strong>{{ $visit->visit_date->format('d/m/Y') }}</strong></td>
                            <td style="color: #7e8299;">{{ $visit->code ?? 'VIS-'.$visit->id }}</td>
                            <td>{{ $visit->general_observations ?: 'Sin observaciones' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($report->observaciones_visitas)
                <div class="obs-container">
                    <div class="observation-label">Comentarios del Profesional:</div>
                    <div class="observation-box">{{ $report->observaciones_visitas }}</div>
                </div>
            @endif
        </div>

        <div class="section no-break">
            <div class="section-header"><h3>2. Controles Operativos (Checklists)</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Auditoría / Inspección</th>
                        <th style="width: 20%">Fecha</th>
                        <th style="width: 15%; text-align: center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['checklists'] as $ch)
                        <tr>
                            <td>{{ $ch->assignment->checklist->title ?? 'Checklist Técnico' }}</td>
                            <td>{{ \Carbon\Carbon::parse($ch->completed_at)->format('d/m/Y H:i') }}</td>
                            <td style="text-align: center;"><span class="badge badge-success">Finalizado</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($report->observaciones_checklists)
                <div class="obs-container">
                    <div class="observation-label">Análisis de Inspecciones:</div>
                    <div class="observation-box">{{ $report->observaciones_checklists }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="page-break"></div>

    {{-- 3. CAPACITACIÓN Y RIESGOS --}}
    <div class="content-wrapper">
        <div class="header">
            <span class="header-meta">{{ $report->sede->nombre_sede }}</span>
            <span class="header-title">Formación y Gestión de Riesgos</span>
        </div>

        <div class="section">
            <div class="section-header"><h3>3. Capacitación del Personal</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Tema Desarrollado</th>
                        <th style="text-align: center; width: 15%">Asistentes</th>
                        <th>Notas del Instructor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['trainings'] as $tr)
                        <tr>
                            <td><strong>{{ $tr->title }}</strong></td>
                            <td style="text-align: center;">{{ $tr->attendees_count }}</td>
                            <td>{{ $tr->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($report->observaciones_capacitaciones)
                <div class="obs-container">
                    <div class="observation-label">Evaluación Formativa:</div>
                    <div class="observation-box">{{ $report->observaciones_capacitaciones }}</div>
                </div>
            @endif
        </div>

        <div class="section">
            <div class="section-header"><h3>4. Análisis Operativo: Hallazgos e Incidentes</h3></div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%; background: #ffcd13; border: 1px solid #ffcd13;">Hallazgos Recalificantes ({{ count($data['hallazgos']) }})</th>
                        <th style="width: 50%; background: #f64e60; border: 1px solid #f64e60;">Incidentes Registrados ({{ count($data['incidentes']) }})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 0;">
                            <table style="margin: 0; border: none;">
                                @forelse($data['hallazgos'] as $hal)
                                    <tr>
                                        <td style="border: none; border-bottom: 1px solid #ebedf3; padding: 10px;">
                                            <div style="font-weight: bold;">{{ $hal->titulo }} <span class="badge {{ $hal->criticidad == 'Alta' ? 'badge-danger' : 'badge-warning' }}">{{ $hal->criticidad }}</span></div>
                                            <div style="color: #7e8299; font-size: 8px; margin-top: 3px;">{{ $hal->descripcion }}</div>
                                            @if(count($hal->fotos ?? []) > 0)
                                                <div style="margin-top: 5px;">
                                                    @foreach($hal->fotos as $foto)
                                                        @php $path = public_path('storage/' . $foto->ruta_archivo); @endphp
                                                        @if(file_exists($path))
                                                            <img src="{{ $path }}" style="width: 120px; height: 120px; border-radius: 3px; margin-right: 2px; object-fit: cover;">
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td style="border: none; color: #b5b5c3; padding: 20px; text-align: center;">Sin hallazgos en el periodo</td></tr>
                                @endforelse
                            </table>
                        </td>
                        <td style="padding: 0;">
                            <table style="margin: 0; border: none;">
                                @forelse($data['incidentes'] as $inc)
                                    <tr>
                                        <td style="border: none; border-bottom: 1px solid #ebedf3; padding: 10px;">
                                            <div style="font-weight: bold;">{{ $inc->tipo_evento }}</div>
                                            <div style="color: #f64e60; font-size: 8px; margin-top: 2px;">FECHA: {{ \Carbon\Carbon::parse($inc->fecha_hora)->format('d/m/Y') }}</div>
                                            <div style="color: #7e8299; font-size: 8px; margin-top: 3px;">{{ $inc->descripcion }}</div>
                                            @if(count($inc->fotos ?? []) > 0)
                                                <div style="margin-top: 5px;">
                                                    @foreach($inc->fotos as $foto)
                                                        @php $path = public_path('storage/' . $foto->ruta_archivo); @endphp
                                                        @if(file_exists($path))
                                                            <img src="{{ $path }}" style="width: 120px; height: 120px; border-radius: 3px; margin-right: 2px; object-fit: cover;">
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td style="border: none; color: #b5b5c3; padding: 20px; text-align: center;">Sin incidentes reportados</td></tr>
                                @endforelse
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($report->observaciones_hallazgos_incidentes)
                <div class="obs-container">
                    <div class="observation-label">Resumen de Seguridad Operativa:</div>
                    <div class="observation-box">{{ $report->observaciones_hallazgos_incidentes }}</div>
                </div>
            @endif
        </div>

        {{-- SECCIÓN 5: CONCLUSIÓN --}}
        <div class="section" style="margin-top: 50px;">
            <div style="background: #1e1e2d; color: white; padding: 30px; border-radius: 8px; border-top: 10px solid {{ $branding['primary'] }}; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 15px 0; font-size: 16px; text-transform: uppercase; color: {{ $branding['primary'] }}; letter-spacing: 1px;">5. Resumen Ejecutivo Final</h3>
                <div style="font-size: 12px; line-height: 1.8; color: #e2e8f0;">
                    {{ $report->resumen_ejecutivo ?: 'No se registró una conclusión final detallada para este periodo.' }}
                </div>
            </div>
        </div>

        <div style="position: absolute; bottom: 50px; left: 0; right: 0; text-align: center;">
            <div style="width: 250px; margin: 0 auto; border-top: 1px solid #1e1e2d; padding-top: 15px;">
                <div style="font-weight: bold; font-size: 12px;">{{ strtoupper($report->tecnico->nombres) }} {{ strtoupper($report->tecnico->apellidos) }}</div>
                <div style="font-size: 10px; color: #7e8299; margin-top: 5px;">Técnico Responsable HyS</div>
                <div style="font-size: 9px; color: #b5b5c3;">XamaSafe Reporting System</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div style="float: left;">XamaSafe v2.0 | {{ $report->sede->nombre_sede }}</div>
        <div style="float: right;" class="page-number"></div>
        <div class="clear"></div>
    </div>
</body>
</html>