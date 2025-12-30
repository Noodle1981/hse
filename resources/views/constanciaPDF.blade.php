
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de visita</title>
    <link rel="stylesheet" href="{{ public_path('css/stylesPDF.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/stylesPDF.css') }}" type="text/css">
    
    <script src="{{ asset('js/scripts.js') }}" type="text/javascript"></script>
</head>
<body>
    <div class="">
        <header>
            <div class="header-wrapper">
                <div class="logo-wrapper">
                    <img id="logo" src="{{ public_path('images/logo_ep_viejo.jpg') }}" onerror="logoError()" alt="Logo EP">
                </div>
                <div class="titulo-wrapper">
                    <div class="titulo">
                        HIGIENE Y SEGURIDAD
                    </div>
                </div>
            </div>
        </header>
        <div class="separator">Detalle:</div>
        <section class="recuadros">
            
            <div class="box">
                <div>Razón Social:</div>
                <div class="data-box">{{$data['razon_social']}}</div>
            </div>
            <div class="box">
                <div>C.U.I.T:</div>
                <div class="data-box">{{$data['cuit']}}</div>
            </div>
            <div class="box">
                <div>Dirección:</div>
                <div class="data-box">
                    @if ($data['sede']['direccion'])
                        {{$data['sede']['direccion']}}
                    @else 
                        Sin Dirección
                    @endif
                </div>
            </div>
            <div class="box">
                <div>Localidad:</div>
                <div class="data-box">
                    @if ($data['sede']['localidad'])
                        {{$data['sede']['localidad']}}
                    @else 
                        Sin Localidad
                    @endif
                </div>
            </div>
        </section>
        <section>
            <div class="details">
                <div class="place">Lugar: {{$data['sede']['nombre_sede']}}</div>
                <div class="date">Fecha de Visita: {{$data['fecha_visita']}}</div>
            </div>
            
            <div class="reason">
                <p class="reason-text">MOTIVO DE LA VISITA</p>
                <div class="options">
                    <div class="option">
                        <div class="check">
                            @if ($data['tipo_visita'] == 'Inspección de Seguridad')
                                X
                            @endif
                        </div>
                        <div class="option-text">Inspección</div>
                    </div>
                    <div class="option">
                        <div class="check">
                            @if ($data['tipo_visita'] == 'Capacitación')
                                X
                            @endif
                        </div>
                        <div class="option-text">Capacitación</div>
                    </div>
                    <div class="option">
                        <div class="check">
                            @if ($data['tipo_visita'] == 'Auditoría')
                                X
                            @endif
                        </div>
                        <div class="option-text">Auditoría</div>
                    </div>
                    <div class="option">
                        <div class="check">
                            @if (!in_array($data['tipo_visita'], ['Inspección de Seguridad', 'Capacitación', 'Auditoría']))
                                X
                            @endif
                        </div>
                        <div class="option-text otros">Otros: {{ $data['tipo_visita'] }}</div>
                    </div>
                </div>
            </div>
            <div class="observations">
                <div class="obs-title">OBSERVACIONES</div>
                <div class="obs-box">
                    {{$data['observacion']}}
                </div>
            </div>

            <!-- Actividades Realizadas -->
            @if(isset($data['actividades']) && 
                ($data['actividades']['inspections']->isNotEmpty() || 
                 $data['actividades']['trainings']->isNotEmpty() || 
                 $data['actividades']['riskEvaluations']->isNotEmpty() || 
                 $data['actividades']['eppDeliveries']->isNotEmpty() ||
                 $data['actividades']['hallazgos']->isNotEmpty()))
                <div class="observations" style="margin-top: 10px;">
                    <div class="obs-title">ACTIVIDADES REALIZADAS DURANTE LA VISITA</div>
                    <div class="obs-box">
                        @if($data['actividades']['inspections']->isNotEmpty())
                            <strong>Inspecciones/Checklists:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                @foreach($data['actividades']['inspections'] as $inspection)
                                    <li>{{ $inspection->checklist->title }} ({{ $inspection->completed_at->format('H:i') }})</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($data['actividades']['trainings']->isNotEmpty())
                            <strong>Capacitaciones:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                @foreach($data['actividades']['trainings'] as $training)
                                    <li>{{ $training->title }} ({{ $training->date->format('H:i') }})</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($data['actividades']['hallazgos']->isNotEmpty())
                            <strong>Hallazgos Detectados:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                @foreach($data['actividades']['hallazgos'] as $hallazgo)
                                    <li>{{ $hallazgo->titulo }} ({{ $hallazgo->criticidad }}, {{ $hallazgo->created_at->format('H:i') }})</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($data['actividades']['riskEvaluations']->isNotEmpty())
                            <strong>Evaluaciones de Riesgo:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                @foreach($data['actividades']['riskEvaluations'] as $evaluation)
                                    <li>{{ $evaluation->risk->name }} ({{ $evaluation->evaluation_date->format('H:i') }})</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($data['actividades']['eppDeliveries']->isNotEmpty())
                            <strong>Entregas de EPP:</strong>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                @foreach($data['actividades']['eppDeliveries'] as $delivery)
                                    <li>{{ $delivery->employee->name }} {{ $delivery->employee->last_name }} ({{ $delivery->delivery_date->format('H:i') }})</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            <div class="signatures">
                <div class="signature">Responsable
                    <div class="signature-wrapper">
                        @if($data['justificacion_firma'])
                            <div style="font-style: italic; color: #666; margin: 20px 0; border: 1px dashed #ccc; padding: 10px;">
                                <strong>Firma no disponible:</strong><br>
                                {{ $data['justificacion_firma'] }}
                            </div>
                        @else
                            <img class="signature-image" id="signature" src="{{ $data['firma_responsable'] }}" onerror="signatureError(this)" alt="Firma responsable">
                        @endif
                        <p>{{ $data['aclaracion_responsable'] }}</p>
                    </div>
                </div>
                <div class="signature">Asesor HyS
                    <div class="signature-wrapper">
                        <img class="signature-image" id="signature2" src="{{ $data['firma_instructor'] }}" onerror="signatureError(this)" alt="Firma">
                        <p>{{ $data['aclaracion_instructor'] }}</p>
                        <p>{{ $data['titulo_instructor'] }}</p>
                        <p>M.P. {{ $data['matricula_instructor'] }}</p>
                    </div>
                </div>
            </div>
        </section>
        
    </div>
</body>
</html>
