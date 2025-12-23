
<!DOCTYPE html>
<html>
<head>
    <title>Constancia de visita</title>
    <link rel="stylesheet" href="{{ public_path('css/stylesPDF.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/stylesPDF.css') }}" type="text/css">
    
    <script src="{{ asset('js/scripts.js') }}" type="text/javascript"></script>
</head>
<body>
    <div class="">
        
        <div class="logo-wrapper">
            <img id="logo" src="{{ public_path('images/equipo-preventivo-logo.png') }}" onerror="logoError()"  alt="Logo EP">
        </div>
        <div class="title-wrapper">
            <h1 class="title">HIGIENE Y SEGURIDAD</h1>
        </div>
        <header>
            <div class="box">
                <div>Razón Social:</div>
                <div>{{$data['razon_social']}}</div>
            </div>
            <div class="box">
                <div>C.U.I.T:</div>
                <div>{{$data['cuit']}}</div>
            </div>
            <div class="box">
                <div>Dirección:</div>
                <div>{{$data['sede']['direccion']}}</div>
            </div>
            <div class="box">
                <div>Localidad:</div>
                <div>
                    @if ($data['sede']['localidad'])
                        $data['sede']['localidad']
                    @else 
                        Sin localidad
                    @endif
                </div>
            </div>
        </header>
        <section>
            <div class="details">
                <div class="place">Lugar: {{$data['sede']['nombre_sede']}}</div>
                <div class="date">Fecha: {{$data['created_at']->format("m/d/Y")}} </div>
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
                        <div class="option-text">Otros</div>
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
                    </div>
                </div>
                <div class="signature">Firma
                    <div class="signature-wrapper">
                        <img class="signature-image" id="signature2" src="{{ $data['firma_instructor'] }}" onerror="signatureError(this)" alt="Firma">
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
