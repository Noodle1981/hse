
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
                <div class="titulo-wrapper">
                    <h1>
                        EQUIPO PREVENTIVO - HIGIENE Y SEGURIDAD
                    </h1>
                </div>
            </div>
            
        </header>
        <section>
            <div>{{ $texto }}</div>
        </section>
        
        @isset ($link )

            <p>Para firmar el documento ingresa al siguiente link</p>
            
            <p><a href="{{ $link }}">Firmar documento</a></p>
            <p>Cuando sea solicitado, ingresa el código:</p>
            <p>{{$code}}</p>

        @endif
        <div class="logo-wrapper">
            <img id="logo" src="{{ asset('images/EP.png') }}" style="width:30%; text-align:center;" alt="Logo EP">
        </div>
        
        
    </div>
</body>
</html>
