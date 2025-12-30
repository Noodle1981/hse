<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Hallazgo #{{ $hallazgo->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #F69239;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1E1E2D;
            margin: 0;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 8px;
            font-weight: bold;
            border-left: 4px solid #F69239;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 10px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }
        .label {
            font-weight: bold;
            width: 30%;
            color: #666;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            color: white;
            display: inline-block;
        }
        .criticidad-critico { background-color: #ef4444; }
        .criticidad-alto { background-color: #f97316; }
        .criticidad-medio { background-color: #eab308; }
        .criticidad-bajo { background-color: #22c55e; }
        
        .estado-cerrado { background-color: #22c55e; }
        .estado-verificado { background-color: #3b82f6; }
        .estado-resuelto { background-color: #06b6d4; }
        .estado-en_proceso { background-color: #eab308; }
        .estado-pendiente { background-color: #ef4444; }

        .timeline {
            margin-left: 20px;
            border-left: 1px solid #ddd;
            padding-left: 15px;
        }
        .timeline-item {
            margin-bottom: 15px;
            position: relative;
        }
        .timeline-date {
            font-size: 10px;
            color: #888;
        }
        .timeline-user {
            font-weight: bold;
        }
        
        .photo-gallery {
            width: 100%;
            margin-top: 10px;
        }
        .photo-item {
            display: inline-block;
            width: 30%;
            margin-right: 2%;
            margin-bottom: 10px;
            vertical-align: top;
        }
        .photo-img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .photo-caption {
            font-size: 9px;
            text-align: center;
            margin-top: 5px;
            color: #666;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">REPORTE DE HALLAZGO</h1>
        <p>EP - Sitema de Gestión de Seguridad y Salud en el Trabajo</p>
    </div>

    <table class="info-grid">
        <tr>
            <td class="label">ID de Hallazgo:</td>
            <td>#{{ $hallazgo->id }}</td>
            <td class="label">Fecha Detección:</td>
            <td>{{ $hallazgo->fecha_deteccion?->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Título:</td>
            <td colspan="3"><strong>{{ $hallazgo->titulo }}</strong></td>
        </tr>
        <tr>
            <td class="label">Estado:</td>
            <td>
                <span class="badge estado-{{ $hallazgo->estado }}">
                    {{ strtoupper(str_replace('_', ' ', $hallazgo->estado)) }}
                </span>
            </td>
            <td class="label">Criticidad:</td>
            <td>
                <span class="badge criticidad-{{ $hallazgo->criticidad }}">
                    {{ strtoupper($hallazgo->criticidad) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Sede:</td>
            <td>{{ $hallazgo->sede->nombre_sede }}</td>
            <td class="label">Empresa:</td>
            <td>{{ $hallazgo->sede->empresa->nombre_empresa }}</td>
        </tr>
        <tr>
            <td class="label">Técnico Responsable:</td>
            <td>{{ $hallazgo->user->nombres }}</td>
            <td class="label">Plazo Corrección:</td>
            <td>{{ $hallazgo->plazo_correccion?->format('d/m/Y') ?? 'N/A' }}</td>
        </tr>
        @if($hallazgo->ubicacion_especifica)
        <tr>
            <td class="label">Ubicación Específica:</td>
            <td colspan="3">{{ $hallazgo->ubicacion_especifica }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Descripción del Hallazgo</div>
    <div style="padding: 5px;">
        {{ $hallazgo->descripcion }}
    </div>

    @if($hallazgo->responsable_correccion)
    <div class="section-title">Responsable de Corrección</div>
    <div style="padding: 5px;">
        {{ $hallazgo->responsable_correccion }}
    </div>
    @endif

    @if($hallazgo->risk)
    <div class="section-title">Vínculo con Riesgo</div>
    <table class="info-grid">
        <tr>
            <td class="label">Riesgo:</td>
            <td>{{ $hallazgo->risk->hazard }}</td>
        </tr>
        <tr>
            <td class="label">Matriz:</td>
            <td>{{ $hallazgo->risk->matrix->name }}</td>
        </tr>
    </table>
    @endif

    @if($seguimientos->count() > 0)
    <div class="section-title">Historial de Seguimientos</div>
    <div class="timeline">
        @foreach($seguimientos as $seg)
        <div class="timeline-item">
            <div class="timeline-date">{{ $seg->fecha_seguimiento->format('d/m/Y H:i') }}</div>
            <div class="timeline-user">{{ $seg->user->nombres }}</div>
            <div style="font-size: 11px; margin-top: 3px;">
                @if($seg->estado_anterior != $seg->estado_nuevo)
                <span style="color: #666;">Cambio de estado: </span>
                <strong>{{ strtoupper($seg->estado_anterior) }} &rarr; {{ strtoupper($seg->estado_nuevo) }}</strong><br>
                @endif
                {{ $seg->comentario }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($fotos->count() > 0)
    <div class="section-title">Evidencia Fotográfica</div>
    <div class="photo-gallery">
        @foreach($fotos as $foto)
        <div class="photo-item">
            {{-- Usamos path local para dompdf --}}
            @php
                $path = storage_path('app/public/' . $foto->ruta_archivo);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                if (file_exists($path)) {
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                } else {
                    $base64 = ''; // O una imagen por defecto
                }
            @endphp
            @if($base64)
                <img src="{{ $base64 }}" class="photo-img">
            @else
                <div style="height: 100px; background: #eee; text-align: center; line-height: 100px;">Foto no encontrada</div>
            @endif
            <div class="photo-caption">
                <strong>{{ strtoupper($foto->tipo) }}</strong><br>
                {{ $foto->descripcion }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Reporte generado automáticamente por Sistema EP el {{ date('d/m/Y H:i') }} - Página 1
    </div>
</body>
</html>
