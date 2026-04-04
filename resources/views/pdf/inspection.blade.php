<x-pdf.layout :title="$inspection->checklist->title ?? 'Reporte de Inspección'" :code="'INSP-' . str_pad($inspection->id, 6, '0', STR_PAD_LEFT)" :status="$inspection->status">

    <style>
        .text-placeholder { color: #d1d5db; font-weight: 500; }
        .text-value { color: #111827; font-weight: 500; }
    </style>

    {{-- 1. Cabecera (Grid de 3 columnas simulado con Tabla) --}}
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
                            <span style="font-weight: bold; color: #4b5563;">ID Reporte:</span> 
                            #{{ $inspection->id }}
                        </div>
                        <div>
                            <span style="font-weight: bold; color: #4b5563;">Fecha:</span> 
                            {{ $inspection->completed_at ? \Carbon\Carbon::parse($inspection->completed_at)->format('d/m/Y H:i') : 'En proceso' }}
                        </div>
                        <div style="margin-top: 4px;">
                            <span class="badge {{ $inspection->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                {{ $inspection->status === 'completed' ? 'COMPLETADO' : 'BORRADOR' }}
                            </span>
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
                            {{ $inspection->sede->empresa->nombre ?? 'N/A' }}
                        </div>
                        <div style="font-weight: 500; color: #4b5563;">
                            {{ $inspection->sede->nombre_sede ?? 'Sede no especificada' }}
                        </div>
                        <div style="font-size: 8pt; color: #9ca3af; margin-top: 2px;">
                            {{ $inspection->sede->direccion ?? '' }}
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
                            {{ $inspection->user->nombres ?? 'Desconocido' }} {{ $inspection->user->apellidos ?? '' }}
                        </div>
                        <div style="font-size: 9pt; color: #6b7280;">
                            {{ $inspection->user->rol ?? 'Técnico' }}
                        </div>
                        <div style="font-size: 8pt; color: #9ca3af; margin-top: 2px;">
                            {{ $inspection->user->email ?? '' }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 2. Secciones y Preguntas --}}
    @php
        $groupedAnswers = $inspection->answers->sortBy(fn($a) => $a->question->order ?? 0)->groupBy(fn($a) => $a->question->section_id);
        $sections = $inspection->checklist->sections->keyBy('id');
    @endphp

    <div style="margin-top: 20px;">
        @foreach($groupedAnswers as $sectionId => $sectionAnswers)
            <div style="margin-bottom: 25px; page-break-inside: avoid;">
                {{-- Título de Sección --}}
                <h3 style="font-size: 12pt; font-weight: bold; color: #111827; border-left: 4px solid #1e3a8a; padding-left: 10px; margin-bottom: 15px;">
                    {{ $sectionId && isset($sections[$sectionId]) ? $sections[$sectionId]->title : 'Preguntas Generales' }}
                </h3>

                {{-- Tabla de Preguntas --}}
                <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    @foreach($sectionAnswers as $answer)
                        <tr>
                            <td style="border-bottom: 1px solid #f3f4f6; padding: 10px 5px; width: 60%; vertical-align: top;">
                                <div style="font-size: 9pt; font-weight: bold; color: #374151;">
                                    {{ $answer->question->label }}
                                </div>
                                @if($answer->question->help_text)
                                    <div style="font-size: 8pt; color: #9ca3af; font-style: italic; margin-top: 3px;">
                                        {{ $answer->question->help_text }}
                                    </div>
                                @endif
                                {{-- Observaciones por pregunta --}}
                                @if($answer->observation)
                                    <div style="margin-top: 5px; font-size: 8pt; padding: 3px 6px; background-color: #fff7ed; border-left: 2px solid #ea580c; color: #9a3412;">
                                        <strong>Obs:</strong> {{ $answer->observation }}
                                    </div>
                                @endif
                            </td>
                            <td style="border-bottom: 1px solid #f3f4f6; padding: 10px 5px; width: 40%; vertical-align: top; text-align: right;">
                                @if($answer->question->type === 'checkbox')
                                    @php $values = json_decode($answer->value, true) ?? []; @endphp
                                    <div style="text-align: right;">
                                        @forelse($values as $val)
                                            <span style="display: inline-block; background-color: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; padding: 2px 6px; border-radius: 4px; font-size: 8pt; margin-left: 4px; font-weight: bold;">
                                                ✓ {{ $val }}
                                            </span>
                                        @empty
                                            <span style="font-style: italic; color: #d1d5db;">-</span>
                                        @endforelse
                                    </div>
                                @elseif($answer->question->type === 'photo')
                                    <div style="text-align: right;">
                                        <span style="font-size: 8pt; color: #6b7280;">(Ver Anexo Fotos)</span>
                                    </div>
                                @else
                                    <div class="{{ empty($answer->value) ? 'text-placeholder' : 'text-value' }}">
                                        {{ $answer->value ?: '-' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
    </div>

    {{-- 3. Observaciones Generales --}}
    @if($inspection->notes)
        <div style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 8px; padding: 15px; margin-top: 20px; page-break-inside: avoid;">
            <div style="font-size: 9pt; font-weight: bold; color: #1e40af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                Observaciones y Hallazgos
            </div>
            <p style="font-size: 9pt; color: #1e3a8a; line-height: 1.5; margin: 0;">
                {{ $inspection->notes }}
            </p>
        </div>
    @endif

    {{-- 4. Firmas --}}
    <div style="margin-top: 40px; page-break-inside: avoid;">
        <table class="no-border">
            <tr>
                {{-- Firma Supervisor --}}
                <td style="width: 50%; padding: 0 20px; text-align: center;">
                    <div style="border-bottom: 2px solid #d1d5db; height: 80px; margin-bottom: 10px; display: flex; align-items: flex-end; justify-content: center;">
                         @if($inspection->supervisor_signature)
                            <img src="{{ $inspection->supervisor_signature }}" style="max-height: 75px; margin: 0 auto;">
                        @else
                            <span style="color: #e5e7eb; font-size: 9pt; font-style: italic; line-height: 80px;">(Firma vacía)</span>
                        @endif
                    </div>
                    <div style="font-weight: bold; color: #111827; font-size: 10pt;">
                        {{ $inspection->supervisor_name ?: 'Supervisor no especificado' }}
                    </div>
                    <div style="font-size: 8pt; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; font-weight: 600;">
                        Conformidad Supervisor
                    </div>
                </td>

                {{-- Firma Técnico --}}
                <td style="width: 50%; padding: 0 20px; text-align: center;">
                    <div style="border-bottom: 2px solid #d1d5db; height: 80px; margin-bottom: 10px; display: flex; align-items: flex-end; justify-content: center;">
                         @if($inspection->technician_signature)
                            <img src="{{ $inspection->technician_signature }}" style="max-height: 75px; margin: 0 auto;">
                        @else
                            {{-- Intentar usar firma del usuario si no está guardada explícitamente en technical_signature --}}
                            @if($inspection->user->firma)
                                <img src="{{ $inspection->user->firma }}" style="max-height: 75px; margin: 0 auto;">
                            @else
                                <span style="color: #e5e7eb; font-size: 9pt; font-style: italic; line-height: 80px;">(Firma vacía)</span>
                            @endif
                        @endif
                    </div>
                    <div style="font-weight: bold; color: #111827; font-size: 10pt;">
                        {{ $inspection->user->nombres ?? '' }} {{ $inspection->user->apellidos ?? '' }}
                    </div>
                    <div style="font-size: 8pt; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; font-weight: 600;">
                        Firma del Profesional
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 5. Footer de Generación (adicional al global) --}}
    <div style="text-align: center; color: #9ca3af; font-size: 8pt; margin-top: 30px; border-top: 1px solid #f3f4f6; padding-top: 10px;">
        GENERADO AUTOMÁTICAMENTE POR EP CHECKLISTS • {{ now()->format('d/m/Y H:i:s') }}
    </div>

</x-pdf.layout>
