@props(['finding'])

<style>
    .finding-wrapper { width: 100%; font-family: sans-serif; }
    
    /* Bloques de datos */
    .detail-item { margin-bottom: 15px; width: 100%; }
    .detail-label { 
        font-size: 8pt; 
        color: #64748b; 
        text-transform: uppercase; 
        font-weight: bold; 
        display: block;
        margin-bottom: 3px;
    }
    .detail-value { 
        font-size: 10.5pt; 
        color: #1e293b; 
        line-height: 1.5; /* Esto evita que el texto se vea pegado */
        display: block;
    }

    /* Tabla de dos columnas para datos secundarios */
    .meta-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .meta-table td { width: 50%; vertical-align: top; padding: 10px 0; border-top: 1px solid #f1f5f9; }

    /* Badge de Criticidad */
    .crit-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 9pt;
        font-weight: bold;
        text-transform: uppercase;
    }
    .crit-alto { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .crit-medio { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .crit-bajo { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

    .photo-grid { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .photo-card { 
        border: 1px solid #e2e8f0; 
        padding: 5px; 
        background: #fff; 
        text-align: center;
    }
</style>

<x-pdf.card :title="'Reporte de ' . ucfirst($finding->tipo ?? 'Hallazgo')">
    <div class="finding-wrapper">
        
        {{-- Descripción: Le damos todo el ancho porque suele ser texto largo --}}
        <div class="detail-item">
            <span class="detail-label">Descripción del Hallazgo</span>
            <span class="detail-value">{{ $finding->descripcion }}</span>
        </div>

        {{-- Tabla de Metadatos --}}
        <table class="meta-table">
            <tr>
                <td>
                    <span class="detail-label">Ubicación / Sector</span>
                    <span class="detail-value">{{ $finding->ubicacion_especifica ?? 'General' }}</span>
                </td>
                <td>
                    <span class="detail-label">Criticidad</span>
                    @php
                        $critClass = match($finding->criticidad) { 
                            'alto' => 'crit-alto', 
                            'medio' => 'crit-medio', 
                            default => 'crit-bajo' 
                        };
                    @endphp
                    <span class="crit-badge {{ $critClass }}">
                        {{ $finding->criticidad ?? 'Baja' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="detail-label">Plazo de Corrección</span>
                    <span class="detail-value">
                        {{ $finding->plazo_correccion ? $finding->plazo_correccion->format('d/m/Y') : 'Sin fecha definida' }}
                    </span>
                </td>
                <td>
                    <span class="detail-label">Responsable de Corrección</span>
                    <span class="detail-value">{{ $finding->responsable_correccion ?? 'No asignado' }}</span>
                </td>
            </tr>
        </table>

        {{-- Evidencia Fotográfica --}}
        @if(isset($finding->fotos) && count($finding->fotos) > 0)
            <div style="margin-top: 20px;">
                <span class="detail-label" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px;">
                    Evidencia Fotográfica
                </span>
                <table class="photo-grid">
                    <tr>
                        @foreach($finding->fotos as $index => $photo)
                            <td style="width: 50%; padding: 5px;">
                                <div class="photo-card">
                                    @php
                                        $filePath = public_path('storage/' . $photo->ruta_archivo);
                                    @endphp
                                    @if(file_exists($filePath))
                                        <img src="{{ $filePath }}" style="max-width: 100%; height: 160px; object-fit: contain;">
                                    @else
                                        <div style="height: 100px; line-height: 100px; color: #94a3b8; font-size: 8pt; background: #f8fafc;">
                                            Imagen no disponible
                                        </div>
                                    @endif
                                    <div style="font-size: 8pt; color: #64748b; margin-top: 5px;">{{ $photo->descripcion ?? 'Evidencia ' . ($index + 1) }}</div>
                                </div>
                            </td>
                            @if(($index + 1) % 2 == 0) </tr><tr> @endif
                        @endforeach
                    </tr>
                </table>
            </div>
        @endif

    </div>
</x-pdf.card>