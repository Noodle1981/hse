
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
                <div class="date">Fecha: {{$data['created_at']->format("d/m/Y H:i")}} hs </div>
            </div>
            
            <div class="reason">
                <p class="reason-text">MOTIVO DE LA VISITA</p>
                <div class="options">
                    <div class="option">
                        <div class="check">
                            @if ($data['tipo_visita'] == 'visita-rutina')
                                X
                            @endif
                        </div>
                        <div class="option-text">Visita de rutina</div>
                    </div>
                    <div class="option">
                        <div class="check">
                            @if ($data['tipo_visita'] == 'capacitacion')
                                X
                            @endif
                        </div>
                        <div class="option-text">Capacitación</div>
                    </div>
                    <div class="option">
                        <div class="check">
                            @if ($data['tipo_visita'] == 'medio-ambiente')
                                X
                            @endif
                        </div>
                        <div class="option-text">Medio Ambiente</div>
                    </div>
                    <div class="option">
                        <div class="check">
                            @if ($data['tipo_visita'] == 'otros')
                                X
                            @endif
                        </div>
                        <div class="option-text otros">Otros</div>
                    </div>
                </div>
            </div>
            <div class="observations">
                <div class="obs-title">OBSERVACIONES</div>
                <div class="obs-box">
                    {{$data['observacion']}}
                </div>
            </div>
            <div class="signatures">
                <div class="signature">Responsable
                    <div class="signature-wrapper">
                        <img class="signature-image" id="signature" src="{{ $data['firma_responsable'] }}" onerror="signatureError(this)" alt="Firma responsable">
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
