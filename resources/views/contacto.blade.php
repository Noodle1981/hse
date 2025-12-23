
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto</title>
    <link rel="stylesheet" href="{{ public_path('css/stylesPDF.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/stylesPDF.css') }}" type="text/css">
    
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
            <div>
                
                <p>
                    Saludos {{$name}}, hemos recibido tu mensaje. En breve nos estaremos comunicando para brindarte más información sobre su consulta.
                </p>
                <p>
                    Atentamente, <br>
                    EP Consultora.
                </p>
            </div>
        </section>
        
        <div class="logo-wrapper">
            <img id="logo" src="{{ asset('images/EP.png') }}" style="; width:30%; margin:10px auto;" alt="Logo EP">
        </div>
        
        
    </div>
</body>
</html>
