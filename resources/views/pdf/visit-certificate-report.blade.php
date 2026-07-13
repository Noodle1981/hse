@php
    $cp = $branding['primary'] ?? '#1e40af';
    $cs = $branding['secondary'] ?? '#6b7280';
    $ca = $branding['accent'] ?? '#16a34a';
@endphp
<x-pdf.layout :title="'Constancia Técnica de Visita'" :code="'REP-' . str_pad($visit->id, 6, '0', STR_PAD_LEFT)" :status="null" :branding="$branding">
    <style>
        /* Estilos específicos de este reporte */
        .text-accent { color: {{ $ca }}; }
        .text-dark { color: #1e293b; }
        .bg-dark { background-color: {{ $cp }}; color: #ffffff; }
        
        /* Estructura de Secciones */
        .section { margin-bottom: 15px; width: 100%; }
        .section-header { 
            background-color: #f8fafc; 
            border-left: 4px solid {{ $cp }}; 
            padding: 8px 12px; 
            margin-bottom: 10px;
        }
        .section-header h2 { 
            margin: 0; 
            font-size: 12px; 
            color: {{ $cp }}; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }

        /* Grilla de Datos */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table td { padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 2px; }
        .value { font-size: 10px; color: #1e293b; font-weight: 600; }

        /* Cajas de texto */
        .observation-box {
            padding: 12px;
            background-color: #fdfcfb;
            border: 1px solid {{ $ca }};
            border-radius: 4px;
            color: #1e293b;
            font-size: 10px;
        }

        /* Tablas de Detalles (Checklists, Capacitaciones) */
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .detail-table th { 
            background-color: {{ $cp }}; 
            color: white; 
            padding: 7px 10px; 
            font-size: 8px; 
            text-align: left; 
            text-transform: uppercase;
        }
        .detail-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; }
        .row-alt { background-color: #f8fafc; }

        /* Badges */
        .pill {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .pill-success { background-color: #dcfce7; color: #166534; }
        .pill-danger { background-color: #fee2e2; color: #991b1b; }
        .pill-warning { background-color: #fef3c7; color: #92400e; }
        .pill-info { background-color: #e0f2fe; color: #075985; }

        /* Hallazgos e Imágenes */
        .card-finding { border: 1px solid #e2e8f0; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
        .photo-table { width: 100%; margin-top: 10px; }
        .photo-cell { width: 50%; padding: 5px; text-align: center; }
        .photo-img { 
            max-width: 100%; 
            max-height: 250px; 
            width: auto; 
            height: auto; 
            border-radius: 4px; 
            border: 1px solid #cbd5e1; 
        }

        /* Firmas */
        .signature-table { width: 100%; margin-top: 50px; page-break-inside: avoid; }
        .signature-box { width: 50%; text-align: center; padding: 0 40px; }
        .signature-img { max-height: 70px; margin-bottom: 5px; }
        .signature-line { border-top: 1px solid {{ $cp }}; padding-top: 8px; }
        .signature-name { font-weight: bold; font-size: 10px; color: #1e293b; display: block; }
        .signature-role { font-size: 8px; color: #64748b; text-transform: uppercase; }

        /* Failure states */
        .bg-failure { background-color: #fef2f2 !important; }
        .text-failure { color: #b91c1c !important; font-weight: bold !important; }
    </style>

    {{-- 1. INFORMACIÓN GENERAL --}}
    <div class="section">
        <div class="section-header"><h2>1. Datos de la Visita</h2></div>
        <table class="data-table">
            <tr>
                <td style="width: 33%">
                    <span class="label">Empresa Cliente</span>
                    <span class="value">{{ $visit->empresa->nombre ?? 'N/A' }}</span>
                </td>
                <td style="width: 33%">
                    <span class="label">CUIT</span>
                    <span class="value">{{ $visit->empresa->cuit ?? 'N/A' }}</span>
                </td>
                <td style="width: 33%">
                    <span class="label">Fecha del Servicio</span>
                    <span class="value text-accent">{{ $visit->visit_date ? $visit->visit_date->format('d/m/Y') : '-' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Sede / Planta</span>
                    <span class="value">{{ $visit->sede->nombre_sede ?? 'N/A' }}</span>
                </td>
                <td>
                    <span class="label">Técnico Asignado</span>
                    <span class="value">{{ $visit->user->nombres }} {{ $visit->user->apellidos }}</span>
                </td>
                <td>
                    <span class="label">Franja Horaria</span>
                    <span class="value">{{ $visit->start_time ? $visit->start_time->format('H:i') : '--:--' }} - {{ $visit->end_time ? $visit->end_time->format('H:i') : '--:--' }} hs</span>
                </td>
            </tr>
        </table>
        
        <div class="label" style="margin-top: 10px;">Resumen Ejecutivo de Visita:</div>
        <div class="observation-box">
            {{ $visit->general_observations ?: 'No se registraron observaciones adicionales.' }}
        </div>
    </div>

    {{-- 2. AUDITORÍAS / CHECKLISTS --}}
    @if($visit->checklistCompletions->count() > 0)
    <div class="section">
        <div class="section-header"><h2>2. Auditorías e Inspecciones</h2></div>
        @foreach($visit->checklistCompletions as $completion)
            <div style="margin-bottom: 20px;">
                <div style="background: #1e293b; color: white; padding: 5px 10px; font-size: 9px; font-weight: bold;">
                    CONTROL: {{ $completion->assignment?->checklist?->title ?? 'Protocolo No Identificado' }}
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="width: 75%">Punto de Control Evaluado</th>
                            <th style="width: 25%; text-align: center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completion->formatted_responses as $resp)
                            @php 
                                $rawVal = $resp['value'] ?? '';
                                if (is_string($rawVal) && (str_starts_with($rawVal, '[') || str_starts_with($rawVal, '{'))) {
                                    $decoded = json_decode($rawVal, true);
                                    if (json_last_error() === JSON_ERROR_NONE) {
                                        $rawVal = $decoded;
                                    }
                                }
                                $valStr = is_array($rawVal) ? implode(', ', $rawVal) : (string)$rawVal;
                                $val = strtolower($valStr); 
                                $isFailure = in_array($val, ['no', 'no cumple']);
                                $isSuccess = in_array($val, ['si', 'cumple', 'pasa']);
                            @endphp
                            <tr class="{{ $loop->even ? 'row-alt' : '' }}">
                                <td @class(['bg-failure text-failure' => $isFailure]) style="padding: 10px; border-bottom: 1px solid #edf2f7;">
                                    <div style="font-size: 10px; margin-bottom: 2px; color: #1e293b;">
                                        {{ $resp['question_label'] ?? 'Punto de Control' }}
                                    </div>
                                    @if(!empty($resp['observation']))
                                        <div style="font-size: 9px; color: #64748b; font-style: italic; margin-top: 4px; padding-left: 8px; border-left: 2px solid #e2e8f0; line-height: 1.3;">
                                            Nota: {!! nl2br(e($resp['observation'])) !!}
                                        </div>
                                    @endif
                                </td>
                                <td @class(['bg-failure' => $isFailure]) style="width: 80px; text-align: center; vertical-align: middle; border-bottom: 1px solid #edf2f7;">
                                    <span class="pill {{ $isSuccess ? 'pill-success' : ($isFailure ? 'pill-danger' : 'pill-info') }}" style="display: inline-block; min-width: 60px;">
                                        {{ strtoupper($valStr) ?: 'PENDIENTE' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
    @endif

    @php
        $inspectedAssets = collect();
        foreach($visit->checklistCompletions as $completion) {
            $asset = $completion->assignment?->inspection?->asset;
            if ($asset) {
                // Evitar duplicados por id de activo
                if (!$inspectedAssets->contains('asset.id', $asset->id)) {
                    $inspectedAssets->push([
                        'asset' => $asset,
                        'completion' => $completion,
                        'failures' => $completion->failure_analysis['failed'] ?? 0
                    ]);
                }
            }
        }
    @endphp

    {{-- 3. CONTROL Y ESTADO DE ACTIVOS --}}
    @if($inspectedAssets->count() > 0)
    <div class="section">
        <div class="section-header"><h2>3. Control y Estado de Activos Evaluados</h2></div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 35%">Activo / Equipo</th>
                    <th style="width: 25%">Identificador / QR</th>
                    <th style="width: 20%; text-align: center;">Resultado Inspección</th>
                    <th style="width: 20%; text-align: center;">Estado Actual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inspectedAssets as $item)
                    @php
                        $asset = $item['asset'];
                        $failures = $item['failures'];
                        $status = $asset->status;
                    @endphp
                    <tr class="{{ $loop->even ? 'row-alt' : '' }}">
                        <td style="padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px;">
                            <div style="font-weight: bold; color: #1e293b;">
                                {{ $asset->name }}
                            </div>
                            <div style="font-size: 8px; color: #64748b; margin-top: 1px;">
                                Tipo: {{ $asset->assetType?->name ?? 'Genérico' }}
                            </div>
                        </td>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; font-family: monospace;">
                            {{ $asset->identifier }}
                        </td>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; text-align: center; vertical-align: middle;">
                            @if($failures > 0)
                                <span class="pill pill-danger" style="display: inline-block; min-width: 60px;">{{ $failures }} {{ $failures == 1 ? 'Falla' : 'Fallas' }}</span>
                            @else
                                <span class="pill pill-success" style="display: inline-block; min-width: 60px;">Sin Fallas</span>
                            @endif
                        </td>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; text-align: center; vertical-align: middle;">
                            @if($status === 'operativo')
                                <span class="pill pill-success" style="display: inline-block; min-width: 60px;">OPERATIVO</span>
                            @elseif($status === 'alerta')
                                <span class="pill pill-warning" style="display: inline-block; min-width: 60px;">EN ALERTA</span>
                            @else
                                <span class="pill pill-danger" style="display: inline-block; min-width: 60px;">FUERA DE SERVICIO</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- 4. CAPACITACIONES --}}
    @if($visit->trainings->count() > 0)
    <div class="section">
        <div class="section-header"><h2>4. Capacitaciones Realizadas</h2></div>
        @foreach($visit->trainings as $training)
            <div style="border: 1px solid #e2e8f0; margin-bottom: 15px;">
                <table class="data-table" style="background: #f8fafc;">
                    <tr>
                        <td style="width: 50%; border:none;"><span class="label">TEMA</span><span class="value text-accent">{{ $training->title }}</span></td>
                        <td style="width: 20%; border:none; text-align: right;"><span class="label">DURACIÓN</span><span class="value">{{ $training->duration_minutes }} min</span></td>
                        <td style="width: 30%; border:none; text-align: right;"><span class="label">OBSERVACIONES</span><span class="value">{{ $training->notes ?: '-' }}</span></td>
                    </tr>
                </table>
                <table class="detail-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Asistente</th>
                            <th>DNI / Identificación</th>
                            <th style="text-align: center;">Condición</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($training->attendees ?? [] as $att)
                            @if(is_array($att))
                                <tr>
                                    <td>{{ $att['name'] ?? 'N/A' }}</td>
                                    <td>{{ $att['dni'] ?? '-' }}</td>
                                    <td style="text-align: center;"><span class="pill pill-success">Presente</span></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="2">{{ is_array($att) ? ($att['name'] ?? 'N/A') : (string)$att }}</td>
                                    <td style="text-align: center;"><span class="pill pill-success">Presente</span></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
    @endif

    {{-- 5. HALLAZGOS (EL PLATO FUERTE) --}}
    @if($visit->hallazgos->count() > 0)
    <div class="section">
        <div class="section-header"><h2>5. Reporte Detallado de Hallazgos</h2></div>
        @foreach($visit->hallazgos as $hallazgo)
            <div class="card-finding">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 65%; vertical-align: top; padding-right: 15px;">
                            <div style="font-size: 11px; font-weight: bold; color: #1e293b; margin-bottom: 5px;">{{ $hallazgo->titulo }}</div>
                            <span class="label">Descripción técnica:</span>
                            <p style="margin: 5px 0; font-size: 9.5px;">{{ $hallazgo->descripcion }}</p>
                            
                            <table class="data-table" style="margin-top: 10px;">
                                <tr>
                                    <td style="border:none; padding: 0;"><span class="label">Criticidad</span>
                                        <span class="pill {{ $hallazgo->criticidad == 'alto' ? 'pill-danger' : 'pill-warning' }}">
                                            {{ $hallazgo->criticidad }}
                                        </span>
                                    </td>
                                    <td style="border:none; padding: 0;"><span class="label">Plazo</span><span class="value text-accent">{{ $hallazgo->plazo_correccion ? $hallazgo->plazo_correccion->format('d/m/Y') : 'Inmediato' }}</span></td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 35%; text-align: right;">
                            <span class="label">Sector</span>
                            <span class="value">{{ $hallazgo->ubicacion_especifica ?: 'General' }}</span>
                        </td>
                    </tr>
                </table>

                @if($hallazgo->fotos->count() > 0)
                    <table class="photo-table">
                        <tr>
                            @foreach($hallazgo->fotos as $photo)
                                <td class="photo-cell">
                                    @php $path = public_path('storage/' . $photo->ruta_archivo); @endphp
                                    @if(file_exists($path))
                                        <img src="{{ $path }}" class="photo-img">
                                    @endif
                                </td>
                                @if($loop->iteration % 2 == 0 && !$loop->last) </tr><tr> @endif
                            @endforeach
                        </tr>
                    </table>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    {{-- 6. INCIDENTES --}}
    @if($visit->incidentes->count() > 0)
    <div class="section">
        <div class="section-header"><h2>6. Registro de Incidentes</h2></div>
        @foreach($visit->incidentes as $incidente)
            <div class="card-finding" style="border-color: #fee2e2; background-color: #fffafb;">
                <div style="font-size: 11px; font-weight: bold; color: #b91c1c; margin-bottom: 10px;">
                    {{ strtoupper($incidente->tipo) }} - {{ $incidente->titulo }}
                </div>
                <table class="data-table">
                    <tr>
                        <td style="width: 33%; border:none;">
                            <span class="label">Gravedad</span>
                            <span class="pill pill-danger">{{ strtoupper($incidente->gravedad) }}</span>
                        </td>
                        <td style="width: 33%; border:none;">
                            <span class="label">Afectados</span>
                            <span class="value">{{ $incidente->personas_afectadas ?? 0 }} pers.</span>
                        </td>
                        <td style="width: 33%; border:none;">
                            <span class="label">Sector</span>
                            <span class="value">{{ $incidente->ubicacion_especifica ?: 'N/A' }}</span>
                        </td>
                    </tr>
                </table>
                <div class="label" style="margin-top: 10px;">Descripción y Medidas:</div>
                <p style="margin: 5px 0; font-size: 9.5px;">{{ $incidente->descripcion }}</p>
                <div style="margin-top: 8px; padding: 8px; border-left: 2px solid #b91c1c; background: #fff1f2; font-size: 9px; color: #991b1b;">
                    <strong>Acciones:</strong> {{ $incidente->medidas_tomadas }}
                </div>

                @if($incidente->fotos->count() > 0)
                    <table class="photo-table">
                        <tr>
                            @foreach($incidente->fotos as $photo)
                                <td class="photo-cell">
                                    @php $path = public_path('storage/' . $photo->ruta_archivo); @endphp
                                    @if(file_exists($path))
                                        <img src="{{ $path }}" class="photo-img">
                                    @endif
                                </td>
                                @if($loop->iteration % 2 == 0 && !$loop->last) </tr><tr> @endif
                            @endforeach
                        </tr>
                    </table>
                @endif
            </div>
        @endforeach
    </div>
    @endif


    {{-- 7. FIRMAS --}}
    <table class="signature-table">
        <tr>
            <td class="signature-box">
                @if($visit->signature)
                    <img src="{{ $visit->signature }}" class="signature-img">
                @else
                    <div style="height: 70px;">
                        @if($visit->signature_skip_reason)
                            <span style="font-size: 8px; color: #991b1b;">SIN FIRMA: {{ $visit->signature_skip_reason }}</span>
                        @endif
                    </div>
                @endif
                <div class="signature-line">
                    <span class="signature-name">{{ $visit->responsible_name ?: 'RESPONSABLE SEDE' }}</span>
                    <span class="signature-role">Por la Empresa Cliente</span>
                </div>
            </td>
            <td class="signature-box">
                @if($visit->user->signature)
                    @php 
                        $sigPath = public_path('storage/' . $visit->user->signature); 
                    @endphp
                    @if(str_starts_with($visit->user->signature, 'data:image'))
                        <img src="{{ $visit->user->signature }}" class="signature-img">
                    @elseif(file_exists($sigPath))
                        <img src="{{ $sigPath }}" class="signature-img">
                    @else
                        <div style="height: 70px; color: #cbd5e1; font-size: 8px; line-height: 70px;">Firma no encontrada</div>
                    @endif
                @else
                    <div style="height: 70px;"></div>
                @endif
                <div class="signature-line">
                    <span class="signature-name">{{ $visit->user->nombres }} {{ $visit->user->apellidos }}</span>
                    <span class="signature-role">Asesor Profesional {{ $branding['name'] }}</span>
                </div>
            </td>
        </tr>
    </table>

</x-pdf.layout>