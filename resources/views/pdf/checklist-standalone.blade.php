<x-pdf.layout :title="$completion->assignment->checklist->title ?? 'Reporte de Inspección'" :code="'INSP-V-' . str_pad($completion->id, 6, '0', STR_PAD_LEFT)" :status="'completed'">

    {{-- 1. Cabecera --}}
    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        <table class="no-border">
            <tr>
                {{-- Col 1: Info General --}}
                <td style="width: 33%; vertical-align: top;">
                    <div style="font-size: 8pt; font-weight: bold; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                        Información General
                    </div>
                    <div style="line-height: 1.6;">
                        <div>
                            <span style="font-weight: bold; color: #4b5563;">ID Inspección:</span> 
                            #{{ $completion->id }}
                        </div>
                        <div>
                            <span style="font-weight: bold; color: #4b5563;">Fecha:</span> 
                            {{ $completion->completed_at ? $completion->completed_at->format('d/m/Y H:i') : 'N/A' }}
                        </div>
                        <div style="margin-top: 4px;">
                            <span class="badge badge-success">COMPLETADO</span>
                        </div>
                    </div>
                </td>

                {{-- Col 2: Ubicación --}}
                <td style="width: 33%; vertical-align: top; border-left: 1px solid #f3f4f6; padding-left: 15px;">
                    <div style="font-size: 8pt; font-weight: bold; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                        Ubicación
                    </div>
                    <div style="line-height: 1.4;">
                        <div style="font-size: 11pt; font-weight: bold; color: #1e3a8a;">
                            {{ $completion->visit->empresa->nombre ?? 'N/A' }}
                        </div>
                        <div style="font-weight: 500; color: #4b5563;">
                            {{ $completion->visit->sede->nombre_sede ?? 'N/A' }}
                        </div>
                    </div>
                </td>

                {{-- Col 3: Responsable --}}
                <td style="width: 33%; vertical-align: top; border-left: 1px solid #f3f4f6; padding-left: 15px;">
                    <div style="font-size: 8pt; font-weight: bold; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                        Responsable
                    </div>
                    <div style="line-height: 1.4;">
                        <div style="font-weight: bold; color: #1f2937;">
                            {{ $completion->visit->user->full_name ?? 'N/A' }}
                        </div>
                        <div style="font-size: 9pt; color: #6b7280;">
                            Técnico de Visita
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 2. Preguntas y Respuestas --}}
    <div style="margin-top: 20px;">
        @php
            $responses = $completion->formatted_responses;
            // Si el checklist tiene secciones, intentamos agrupar
            $checklist = $completion->assignment->checklist;
            $sections = $checklist->sections->keyBy('id');
            
            // Detectar si hay al menos una observación con contenido
            $hasObservations = collect($responses)->contains(fn($r) => !empty(trim($r['observation'] ?? '')));

    <style>
        .checklist-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .th-base {
            padding: 12px 15px;
            font-size: 9pt;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }
        .tr-even {
            background-color: #f9fafb;
        }
        .td-base {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .question-label {
            font-size: 9pt;
            color: #1e293b;
            font-weight: 500;
        }
        /* Clases para anchos dinamicos - Puras para evitar errores de IDE */
        .col-point-obs { width: 60%; text-align: left; }
        .col-point-no-obs { width: 80%; text-align: left; }
        .col-compliance-obs { width: 15%; text-align: center; }
        .col-compliance-no-obs { width: 20%; text-align: center; }
        .col-obs { width: 25%; text-align: left; }
    </style>

    {{-- 2. Preguntas y Respuestas --}}
    <div style="margin-top: 20px;">
        @php
            $responses = $completion->formatted_responses;
            // Si el checklist tiene secciones, intentamos agrupar
            $checklist = $completion->assignment->checklist;
            
            // Detectar si hay al menos una observación con contenido
            $hasObservations = collect($responses)->contains(fn($r) => !empty(trim($r['observation'] ?? '')));
        @endphp

        <table class="checklist-table">
            <thead>
                <tr style="background-color: #f8fafc;">
                    <th class="th-base {{ $hasObservations ? 'col-point-obs' : 'col-point-no-obs' }}">Punto de Control</th>
                    <th class="th-base {{ $hasObservations ? 'col-compliance-obs' : 'col-compliance-no-obs' }}">Cumplimiento</th>
                    @if($hasObservations)
                        <th class="th-base col-obs">Observaciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($responses as $resp)
                    @php
                        $val = strtolower((string)($resp['value'] ?? ''));
                        $isFailure = in_array($val, ['no', 'no cumple']);
                    @endphp
                    <tr class="{{ $loop->even ? 'tr-even' : '' }}">
                        <td class="td-base">
                            <div class="question-label">
                                {{ $resp['question_label'] ?? 'Sin etiqueta' }}
                            </div>
                        </td>
                        <td class="td-base" style="text-align: center;">
                            @if($isFailure)
                                <span style="color: #b91c1c; font-weight: bold; text-transform: uppercase; font-size: 8pt; background-color: #fef2f2; padding: 2px 6px; border-radius: 4px; border: 1px solid #fee2e2;">
                                    {{ $resp['value'] ?? '-' }}
                                </span>
                            @else
                                <span style="color: #15803d; font-weight: bold; text-transform: uppercase; font-size: 8pt; background-color: #f0fdf4; padding: 2px 6px; border-radius: 4px; border: 1px solid #dcfce7;">
                                    {{ $resp['value'] ?? '-' }}
                                </span>
                            @endif
                        </td>
                        @if($hasObservations)
                            <td style="padding: 12px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: top;">
                                <div style="font-size: 8pt; color: #64748b; line-height: 1.4;">
                                    {{ $resp['observation'] ?? '-' }}
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 3. Notas Finales de la Visita (Contexto) --}}
    @if($completion->visit->general_observations)
        <div style="margin-top: 30px; padding: 15px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; page-break-inside: avoid;">
            <div style="font-size: 8pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">
                Observaciones Generales de la Visita
            </div>
            <p style="font-size: 9pt; color: #334155; line-height: 1.5; margin: 0;">
                {{ $completion->visit->general_observations }}
            </p>
        </div>
    @endif

    {{-- 4. Firmas --}}
    <div style="margin-top: 50px; page-break-inside: avoid;">
        <table class="no-border">
            <tr>
                <td style="width: 50%; text-align: center; padding: 0 40px;">
                    <div style="border-bottom: 1px solid #cbd5e1; height: 80px; margin-bottom: 10px; display: flex; align-items: flex-end; justify-content: center;">
                        @if($completion->visit->signature)
                            <img src="{{ $completion->visit->signature }}" style="max-height: 70px; margin: 0 auto;">
                        @endif
                    </div>
                    <div style="font-size: 9pt; font-weight: bold; color: #1e293b;">{{ $completion->visit->user->full_name }}</div>
                    <div style="font-size: 7pt; color: #94a3b8; text-transform: uppercase;">Firma del Técnico</div>
                </td>
                <td style="width: 50%; text-align: center; padding: 0 40px;">
                    <div style="border-bottom: 1px solid #cbd5e1; height: 80px; margin-bottom: 10px;"></div>
                    <div style="font-size: 9pt; font-weight: bold; color: #1e293b;">Supervisor / Cliente</div>
                    <div style="font-size: 7pt; color: #94a3b8; text-transform: uppercase;">Sello o Firma Recepción</div>
                </td>
            </tr>
        </table>
    </div>

</x-pdf.layout>
