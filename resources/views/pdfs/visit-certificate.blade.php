<x-pdf.layout title="Constancia de Visita" :code="'VIS-' . str_pad($visit->id, 6, '0', STR_PAD_LEFT)" :status="$visit->status">
    
    {{-- 1. Resumen Ejecutivo de la Visita --}}
    <x-pdf.card title="Resumen de Visita Técnica">
        <table class="no-border">
            <tr>
                <td style="width: 50%;">
                    <strong>Empresa:</strong> {{ $visit->empresa->nombre ?? 'N/A' }}<br>
                    <strong>Sede:</strong> {{ $visit->sede->nombre_sede ?? 'N/A' }}<br>
                    <strong>Dirección:</strong> {{ $visit->sede->direccion ?? 'N/A' }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Técnico Responsable:</strong> {{ $visit->responsible_name ?? ($visit->user->nombres ?? '') . ' ' . ($visit->user->apellidos ?? '') }}<br>
                    <strong>Inicio:</strong> {{ $visit->start_time ? \Carbon\Carbon::parse($visit->start_time)->format('d/m/Y H:i') : '-' }}<br>
                    <strong>Fin:</strong> {{ $visit->end_time ? \Carbon\Carbon::parse($visit->end_time)->format('d/m/Y H:i') : '-' }}
                </td>
            </tr>
        </table>
        
        <div class="mt-4 mb-2">
            <strong>Observaciones Generales:</strong>
            <p class="bg-gray" style="padding: 10px; border-radius: 4px; margin-top: 5px;">
                {{ $visit->general_observations ?? 'Sin observaciones registradas.' }}
            </p>
        </div>

        {{-- Geolocalización (Si existe) --}}
        @if($visit->latitude && $visit->longitude)
            <div class="mt-4" style="border-top: 1px solid #e2e8f0; padding-top: 10px;">
                <table class="no-border">
                    <tr>
                        <td style="width: 60%;">
                            <strong>Ubicación GPS:</strong><br>
                            Lat: {{ $visit->latitude }} / Long: {{ $visit->longitude }}
                        </td>
                        <td style="width: 40%; text-align: right;">
                            {{-- Placeholder para mapa estático --}}
                            <div style="background: #eee; height: 60px; width: 100px; display: inline-block; text-align: center; line-height: 60px; font-size: 8pt; color: #999;">
                                Mapa GPS
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </x-pdf.card>

    <div class="page-break"></div>

    {{-- 2. Anexos: Checklists Completados --}}
    @if(isset($visit->checklistCompletions) && $visit->checklistCompletions->count() > 0)
        <h3 class="text-primary" style="border-bottom: 2px solid #1e3a8a; margin-bottom: 15px;">Anexo I: Checklists e Inspecciones</h3>
        @foreach($visit->checklistCompletions as $completion)
            <x-pdf.checklist-detail :checklist="$completion->assignment->checklist ?? $completion->checklist" :completion="$completion" />
            <div style="margin-bottom: 20px;"></div>
        @endforeach
        <div class="page-break"></div>
    @endif

    {{-- 3. Anexos: Capacitaciones --}}
    @if(isset($visit->trainings) && $visit->trainings->count() > 0)
        <h3 class="text-primary" style="border-bottom: 2px solid #1e3a8a; margin-bottom: 15px;">Anexo II: Capacitaciones</h3>
        @foreach($visit->trainings as $training)
            <x-pdf.training-record :training="$training" />
            <div style="margin-bottom: 20px;"></div>
        @endforeach
        <div class="page-break"></div>
    @endif

    {{-- 4. Anexos: Entregas EPP --}}
    @if(isset($visit->eppDeliveries) && $visit->eppDeliveries->count() > 0)
        <h3 class="text-primary" style="border-bottom: 2px solid #1e3a8a; margin-bottom: 15px;">Anexo III: Entregas de EPP</h3>
        @foreach($visit->eppDeliveries as $delivery)
            <x-pdf.epp-delivery :delivery="$delivery" />
            <div style="margin-bottom: 20px;"></div>
        @endforeach
        <div class="page-break"></div>
    @endif

    {{-- 5. Cierre y Firmas --}}
    <x-pdf.card title="Conformidad del Servicio">
        <table class="no-border" style="margin-top: 30px;">
            <tr>
                <td style="width: 50%; text-align: center; padding: 10px;">
                    <div style="border-bottom: 1px solid #333; height: 60px; margin-bottom: 5px;">
                         {{-- Firma Técnico --}}
                         @if($visit->responsible_signature)
                            <img src="{{ $visit->responsible_signature }}" style="max-height: 60px;">
                         @endif
                    </div>
                    <strong>{{ $visit->user->nombres ?? '' }} {{ $visit->user->apellidos ?? '' }}</strong><br>
                    <span class="text-secondary text-xs">Técnico Prevencionista</span>
                </td>
                <td style="width: 50%; text-align: center; padding: 10px;">
                    @if($visit->status == 'signed' || $visit->client_signature)
                        <div style="border-bottom: 1px solid #333; height: 60px; margin-bottom: 5px;">
                            <img src="{{ $visit->client_signature }}" style="max-height: 60px;">
                        </div>
                        <strong>Cliente / Responsable Sede</strong>
                    @else
                        <div style="border-bottom: 1px solid #333; height: 60px; margin-bottom: 5px; color: #999; line-height: 60px;">
                            {{ $visit->signature_skip_reason ?? 'Firma Pendiente' }}
                        </div>
                        <strong>Firma Pendiente o No Disponible</strong><br>
                        <span class="text-secondary text-xs">Motivo: {{ $visit->signature_skip_reason }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </x-pdf.card>

</x-pdf.layout>
