
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de visita</title>
    <link rel="stylesheet" href="{{ public_path('css/stylesInformePDF.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/stylesInformePDF.css') }}" type="text/css">
    
    <script src="{{ asset('js/scripts.js') }}" type="text/javascript"></script>
</head>
<body>
    <header>
        <div class="amarillo"></div>
        <div class="icono-wrapper">
            <img id="icono" src="{{ public_path('images/HyS.png') }}" onerror="logoError()" alt="Logo EP">
        </div>
            
        <div class="linea"></div>
        <div class="date">2025</div>
        
        <div class="logo-wrapper">
            <img id="logo" src="{{ public_path('images/EP.png') }}" onerror="logoError()" alt="Logo EP">
        </div>
        <div class="titulo-wrapper">
            <div class="titulo">
                INFORME HYS
            </div>
        </div>
        <div class="linea2"></div>
    </header>
</body>
</html>
