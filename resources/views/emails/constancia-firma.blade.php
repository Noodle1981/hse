<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Solicitud de Firma - Constancia de Visita</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #F69239; text-align: center;">Solicitud de Firma</h2>
        
        <p>Hola <strong>{{ $constancia->aclaracion_responsable ?? $constancia->responsible_name ?? 'Responsable' }}</strong>,</p>
        
        <p>Se ha generado una constancia de visita técnica realizada en <strong>{{ $constancia->sede->nombre_sede }}</strong> el día <strong>{{ ($constancia->fecha_visita ?? $constancia->visit_date)?->format('d/m/Y') }}</strong>.</p>
        
        @if($constancia->tipo_visita ?? false)
        <p>Tipo de Visita: <strong>{{ $constancia->tipo_visita }}</strong></p>
        @endif
        
        <p>Por favor, ingrese al siguiente enlace para revisar y firmar digitalmente la constancia:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('constancia.firmar', ['token' => $constancia->token_responsable]) }}" 
               style="background-color: #F69239; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Firmar Constancia
            </a>
        </div>
        
        <p>Código de seguridad: <strong>{{ $constancia->code }}</strong></p>
        
        <p style="font-size: 0.9em; color: #666;">
            Si el botón no funciona, copie y pegue la siguiente URL en su navegador:<br>
            {{ route('constancia.firmar', ['token' => $constancia->token_responsable]) }}
        </p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        
        <p style="font-size: 0.8em; text-align: center; color: #999;">
            Este es un correo automático de EP-Back. Por favor no responda a este mensaje.
        </p>
    </div>
</body>
</html>
