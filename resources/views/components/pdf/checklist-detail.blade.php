@props(['checklist', 'completion', 'branding' => null])

@php
    $cp = $branding['primary'] ?? '#1e40af';
@endphp

<style>
    .pdf-content { font-family: sans-serif; padding-top: 10px; }
    
    /* Info superior */
    .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .info-label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: bold; }
    .info-value { font-size: 12px; color: #1e293b; }

    /* Tabla de respuestas */
    .responses-table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #e2e8f0; }
    .responses-table th { background-color: #f8fafc; padding: 8px; text-align: left; font-size: 10px; color: #475569; border-bottom: 2px solid #e2e8f0; }
    .responses-table td { padding: 8px; font-size: 11px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }

    /* SECCIÓN DE FIRMAS (Lo que querías mejorar) */
    .signature-container { margin-top: 30px; page-break-inside: avoid; }
    .signature-title { 
        font-size: 11px; 
        font-weight: bold; 
        color: {{ $cp }}; 
        border-bottom: 1px solid {{ $cp }}; 
        padding-bottom: 5px; 
        margin-bottom: 15px; 
        text-transform: uppercase;
    }
    
    .signature-table { width: 100%; border-collapse: collapse; }
    .signature-cell { width: 50%; vertical-align: top; padding-right: 20px; }
    .signature-line { border-top: 1px solid #94a3b8; margin-top: 40px; padding-top: 5px; width: 90%; }
    
    .name-label { font-size: 11px; font-weight: bold; color: #1e293b; display: block; margin-top: 5px; }
    .role-label { font-size: 9px; color: #64748b; text-transform: uppercase; display: block; }

    .badge-check { background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; }
</style>

<x-pdf.card :title="$checklist->title ?? 'Reporte de Inspección'" :branding="$branding">
    <div class="pdf-content">
        
        {{-- Encabezado --}}
        <table class="info-grid">
            <tr>
                <td style="width: 60%;">
                    <span class="info-label">Descripción:</span><br>
                    <span class="info-value">{{ $checklist->description ?? 'Sin descripción' }}</span>
                    <div style="margin-top: 8px;">
                        <span class="info-label">Categoría:</span><br>
                        <span class="info-value">{{ $checklist->category ?? 'General' }}</span>
                    </div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <span class="badge-check">Completado</span>
                    <div style="margin-top: 10px;">
                        <span class="info-label">Fecha de Finalización:</span><br>
                        <span class="info-value">
                            {{ $completion->completed_at instanceof \Carbon\Carbon ? $completion->completed_at->format('d/m/Y H:i') : $completion->completed_at }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Tabla de Respuestas --}}
        <table class="responses-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Pregunta</th>
                    <th style="width: 25%;">Respuesta</th>
                    <th style="width: 30%;">Observación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completion->responses ?? [] as $response)
                    <tr>
                        <td style="font-weight: 500;">{{ $response['question_label'] ?? 'Pregunta' }}</td>
                        <td>
                            @if(is_array($response['value']))
                                <span style="background: #f1f5f9; padding: 2px 4px; border-radius: 3px;">
                                    {{ implode(', ', $response['value']) }}
                                </span>
                            @else
                                {{ $response['value'] }}
                            @endif
                        </td>
                        <td style="color: #64748b; font-style: italic;">{{ $response['observation'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8; padding: 20px;">No hay respuestas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Firmas y Conformidad --}}
        <div class="signature-container">
            <div class="signature-title">Conformidad y Validación</div>
            
            <table class="signature-table">
                <tr>
                    {{-- Lado del Operario / Responsable --}}
                    <td class="signature-cell">
                        <div style="height: 100px; display: flex; align-items: flex-end; justify-content: center; padding-bottom: 5px;">
                            {{-- Si tienes la firma del operario en imagen, ponla aquí --}}
                            @if(isset($completion->operator_signature))
                                <img src="{{ $completion->operator_signature }}" style="max-height: 90px; max-width: 100%;">
                            @endif
                        </div>
                        <div class="signature-line">
                            <span class="name-label">{{ $completion->user->name ?? 'Firma Pendiente' }}</span>
                            <span class="role-label">Responsable de Ejecución</span>
                        </div>
                    </td>

                    {{-- Lado del Supervisor / Profesional --}}
                    <td class="signature-cell">
                        <div style="height: 100px; display: flex; align-items: flex-end; justify-content: center; padding-bottom: 5px;">
                            @if(isset($completion->supervisor_signature))
                                <img src="{{ $completion->supervisor_signature }}" style="max-height: 90px; max-width: 100%;">
                            @endif
                        </div>
                        <div class="signature-line">
                            <span class="name-label">
                                {{ $completion->supervisor_name ?? 'Supervisor no especificado' }}
                            </span>
                            <span class="role-label">Firma del Profesional / Supervisor</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Galería de Fotos --}}
        @if(isset($completion->photos) && count($completion->photos) > 0)
            <div style="margin-top: 30px; page-break-inside: avoid;">
                <div class="signature-title">Evidencia Fotográfica</div>
                <div style="margin-top: 10px;">
                    @foreach($completion->photos as $index => $photo)
                        <div style="display: inline-block; width: 31%; margin-right: 1%; margin-bottom: 10px; border: 1px solid #e2e8f0; background: #fff; padding: 3px; text-align: center;">
                            <img src="{{ $photo->url }}" style="max-width: 100%; height: 100px; object-fit: cover;">
                            <div style="font-size: 8px; color: #64748b; margin-top: 4px; overflow: hidden; height: 10px;">
                                {{ $photo->description ?? 'Foto ' . ($index + 1) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-pdf.card>