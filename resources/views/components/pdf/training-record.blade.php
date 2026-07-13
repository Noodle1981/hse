@props(['training', 'branding' => null])

@php
    $attendeesList = [];
    if (isset($training->attendances) && $training->attendances->isNotEmpty()) {
        $attendeesList = $training->attendances;
    } elseif (isset($training->attendees)) {
        $attendeesList = is_string($training->attendees) ? json_decode($training->attendees, true) : $training->attendees;
    }

    $cp = $branding['primary'] ?? '#1e40af';
@endphp

<style>
    .pdf-content-wrapper { font-family: sans-serif; }
    
    /* Separador para evitar que choque con el título del card */
    .header-spacer { height: 10px; width: 100%; }

    .main-info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .main-info-table td { vertical-align: top; }
    
    .data-label { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; margin-bottom: 3px; display: block; }
    .data-value { font-size: 12px; color: #1e293b; font-weight: 500; }
    .data-title { font-size: 15px; font-weight: bold; color: #111827; }

    .section-divider { 
        background-color: #f1f5f9; 
        padding: 6px 10px; 
        margin: 20px 0 10px 0; 
        font-size: 11px; 
        font-weight: bold; 
        color: #475569; 
        text-transform: uppercase;
        border-left: 3px solid {{ $cp }};
    }

    .attendees-table { width: 100%; border-collapse: collapse; }
    .attendees-table th { background-color: #ffffff; padding: 8px; text-align: left; font-size: 10px; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    .attendees-table td { padding: 10px 8px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }

    .signature-box { height: 35px; text-align: center; vertical-align: middle; }
    .signature-img { max-height: 30px; }
    
    .notes-area { margin-top: 15px; padding: 10px; border: 1px solid #e2e8f0; background-color: #fafafa; border-radius: 4px; font-size: 11px; }
</style>

<x-pdf.card title="Registro de Capacitación">
    <div class="pdf-content-wrapper">
        
        {{-- Espaciador de seguridad al inicio --}}
        <div class="header-spacer"></div>

        <table class="main-info-table">
            <tr>
                <td style="width: 70%; padding-right: 15px;">
                    <span class="data-label">Tema / Título</span>
                    <span class="data-title">{{ $training->title }}</span>
                    
                    <div style="margin-top: 12px;">
                        <span class="data-label">Instructor</span>
                        <span class="data-value">{{ $training->trainer_name ?? 'Instructor Externo' }}</span>
                    </div>
                </td>
                <td style="width: 30%; border-left: 1px solid #e2e8f0; padding-left: 15px;">
                    <span class="data-label">Fecha del Evento</span>
                    <span class="data-value">{{ $training->date instanceof \Carbon\Carbon ? $training->date->format('d/m/Y') : $training->date }}</span>
                    
                    <div style="margin-top: 12px;">
                        <span class="data-label">Duración</span>
                        <span class="data-value">{{ $training->duration ?? 0 }} minutos</span>
                    </div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 10px;">
            <span class="data-label">Descripción</span>
            <span class="data-value" style="font-size: 11px; line-height: 1.4;">{{ $training->description }}</span>
        </div>

        <div class="section-divider">Lista de Asistencia</div>

        <table class="attendees-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Nombre y Apellido</th>
                    <th style="width: 20%;">DNI</th>
                    <th style="width: 30%; text-align: center;">Firma</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendeesList as $index => $attendee)
                    @php
                        $name = is_object($attendee) ? $attendee->employee_name : ($attendee['employee_name'] ?? $attendee['name'] ?? 'N/A');
                        $dni = is_object($attendee) ? $attendee->dni : ($attendee['dni'] ?? 'N/A');
                        $signature = is_object($attendee) ? $attendee->signature : ($attendee['signature'] ?? null);
                    @endphp
                    <tr>
                        <td style="color: #94a3b8;">{{ $index + 1 }}</td>
                        <td>{{ $name }}</td>
                        <td>{{ $dni }}</td>
                        <td class="signature-box">
                            @if($signature)
                                <img src="{{ $signature }}" class="signature-img">
                            @else
                                <span style="color: #cbd5e1;">__________</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #94a3b8;">No hay asistentes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($training->notes)
            <div class="notes-area">
                <span class="data-label" style="color: #475569;">Observaciones:</span>
                {{ $training->notes }}
            </div>
        @endif

        @if(isset($training->photos) && count($training->photos) > 0)
            <div class="section-divider">Evidencia Fotográfica</div>
            <div style="margin-top: 10px;">
                @foreach($training->photos as $photo)
                    <div style="display: inline-block; margin: 5px; border: 1px solid #e2e8f0; padding: 2px;">
                        <img src="{{ $photo->url }}" style="height: 100px; width: auto;">
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-pdf.card>