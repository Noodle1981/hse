@props(['incident', 'branding' => null])

@php
    $cp = $branding['primary'] ?? '#1e40af';
@endphp

<x-pdf.card :title="'Detalles del ' . ucfirst($incident->tipo ?? 'Incidente')" :branding="$branding">
    <table class="no-border" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 70%;">
                <h3 style="margin-top: 0; color: #1e293b;">{{ $incident->titulo ?? 'Sin título' }}</h3>
                <strong>Descripción:</strong><br>
                <p style="font-size: 9pt; color: #4b5563; margin-top: 5px;">{{ $incident->descripcion }}</p>
                
                <strong>Ubicación Específica:</strong> {{ $incident->ubicacion_especifica ?? 'No especificada' }}<br>
                
                <div style="margin-top: 10px;">
                    <strong>Tipo:</strong> 
                    <span class="badge {{ match($incident->tipo) { 'accidente' => 'badge-danger', 'incidente' => 'badge-warning', default => 'badge-info' } }}">
                        {{ ucfirst(str_replace('_', ' ', $incident->tipo)) }}
                    </span>
                    
                    <span style="margin-left: 10px;"><strong>Gravedad:</strong></span>
                    <span class="badge {{ match($incident->gravedad) { 'fatal' => 'badge-danger', 'grave' => 'badge-danger', 'moderado' => 'badge-warning', default => 'badge-success' } }}">
                        {{ ucfirst($incident->gravedad) }}
                    </span>
                </div>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: top;">
                <div class="text-sm">
                    <strong>Fecha del Evento:</strong><br>
                    {{ $incident->fecha_ocurrencia ? $incident->fecha_ocurrencia->format('d/m/Y H:i') : 'N/A' }}
                </div>
            </td>
        </tr>
    </table>

    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 15px 0;">

    <div class="grid">
        <div style="width: 50%; float: left;">
            <strong>Personas Afectadas:</strong><br>
            <span style="font-size: 14pt; font-bold; color: #ef4444;">{{ $incident->personas_afectadas ?? 0 }}</span>
        </div>
        <div style="width: 50%; float: left;">
            <strong>Días Perdidos:</strong><br>
            <span style="font-size: 14pt; font-bold; color: #f59e0b;">{{ $incident->dias_perdidos ?? 0 }}</span>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="margin-top: 15px;">
        <strong>Medidas Tomadas / Plan de Acción:</strong>
        <div style="background-color: #f8fafc; padding: 10px; border-left: 4px solid {{ $cp }}; margin-top: 5px; font-size: 9pt;">
            {{ $incident->medidas_tomadas ?? 'No se registraron medidas iniciales' }}
        </div>
    </div>

    {{-- Fotos del Incidente --}}
    @if(isset($incident->fotos) && count($incident->fotos) > 0)
        <div class="mt-4">
             <strong class="text-secondary">Evidencia Fotográfica:</strong>
             <table class="no-border mt-2">
                <tr>
                    @foreach($incident->fotos as $photo)
                        <td style="width: 50%; padding: 5px; text-align: center;">
                             @php
                                $filePath = public_path('storage/' . $photo->ruta_archivo);
                             @endphp
                             @if(file_exists($filePath))
                                <img src="{{ $filePath }}" style="max-height: 180px; border: 1px solid #ccc; border-radius: 4px;">
                             @else
                                <div style="font-size: 8pt; color: #999; border: 1px dashed #ccc; padding: 20px;">Imagen no disponible</div>
                             @endif
                        </td>
                        @if($loop->iteration % 2 == 0 && !$loop->last)
                            </tr><tr>
                        @endif
                    @endforeach
                </tr>
             </table>
        </div>
    @endif
</x-pdf.card>
