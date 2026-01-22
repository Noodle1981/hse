<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de Visita #{{ $visit->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1E1E2D; padding-bottom: 15px; }
        .header h1 { font-size: 24pt; color: #1E1E2D; margin-bottom: 5px; }
        .header .subtitle { font-size: 10pt; color: #666; }
        .info-grid { display: table; width: 100%; margin-bottom: 20px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; font-weight: bold; width: 30%; padding: 5px; background: #f5f5f5; }
        .info-value { display: table-cell; padding: 5px; border-bottom: 1px solid #ddd; }
        .section { margin-bottom: 25px; page-break-inside: avoid; }
        .section-title { font-size: 14pt; font-weight: bold; color: #1E1E2D; margin-bottom: 10px; border-bottom: 2px solid #FF6B35; padding-bottom: 5px; }
        .item { margin-bottom: 10px; padding: 8px; background: #f9f9f9; border-left: 3px solid #FF6B35; }
        .item-title { font-weight: bold; color: #1E1E2D; }
        .item-detail { font-size: 9pt; color: #666; margin-top: 3px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 2px solid #ddd; text-align: center; font-size: 9pt; color: #666; }
        .empty-section { font-style: italic; color: #999; padding: 10px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #1E1E2D; color: white; padding: 8px; text-align: left; font-size: 9pt; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9pt; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>CONSTANCIA DE VISITA</h1>
        <div class="subtitle">Documento ID: {{ str_pad($visit->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <!-- Visit Information -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Empresa:</div>
            <div class="info-value">{{ $visit->empresa->nombre }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Sede:</div>
            <div class="info-value">{{ $visit->sede->nombre_sede }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Técnico:</div>
            <div class="info-value">{{ $visit->user->nombres }} {{ $visit->user->apellidos }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Fecha de Visita:</div>
            <div class="info-value">{{ $visit->start_time->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Duración:</div>
            <div class="info-value">
                @if($visit->end_time)
                    {{ $visit->start_time->diffForHumans($visit->end_time, true) }}
                @else
                    En progreso
                @endif
            </div>
        </div>
    </div>

    <!-- Checklists Completed -->
    <div class="section">
        <div class="section-title">1. Checklists Completados</div>
        @if($visit->checklistCompletions->count() > 0)
            @foreach($visit->checklistCompletions as $completion)
                <div class="item">
                    <div class="item-title">{{ $completion->assignment->checklist->title }}</div>
                    <div class="item-detail">
                        Categoría: {{ $completion->assignment->checklist->categoryRelation->name ?? 'N/A' }} | 
                        Completado: {{ $completion->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-section">No se completaron checklists durante esta visita</div>
        @endif
    </div>

    <!-- Trainings -->
    <div class="section">
        <div class="section-title">2. Capacitaciones Realizadas</div>
        @if($visit->trainings->count() > 0)
            @foreach($visit->trainings as $training)
                <div class="item">
                    <div class="item-title">
                        {{ $training->title }}
                        @if($training->training && $training->training->status === 'completed')
                            <span class="badge badge-success">Completada</span>
                        @else
                            <span class="badge badge-warning">Programada</span>
                        @endif
                    </div>
                    <div class="item-detail">
                        Duración: {{ $training->duration_minutes }} min | 
                        Asistentes: {{ $training->attendees_count }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-section">No se realizaron capacitaciones durante esta visita</div>
        @endif
    </div>

    <!-- EPP Deliveries -->
    <div class="section">
        <div class="section-title">3. Entregas de EPP</div>
        @if($visit->eppDeliveries->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Ítems Entregados</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($visit->eppDeliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->employee->name }} {{ $delivery->employee->last_name }}</td>
                            <td>{{ count($delivery->epp_items ?? []) }} ítems</td>
                            <td>{{ $delivery->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-section">No se realizaron entregas de EPP durante esta visita</div>
        @endif
    </div>

    <!-- Findings -->
    <div class="section">
        <div class="section-title">4. Hallazgos Reportados</div>
        @if($visit->hallazgos->count() > 0)
            @foreach($visit->hallazgos as $hallazgo)
                <div class="item">
                    <div class="item-title">
                        {{ $hallazgo->titulo }}
                        <span class="badge badge-{{ $hallazgo->severidad === 'alta' ? 'danger' : ($hallazgo->severidad === 'media' ? 'warning' : 'success') }}">
                            {{ ucfirst($hallazgo->severidad) }}
                        </span>
                    </div>
                    <div class="item-detail">{{ $hallazgo->descripcion }}</div>
                </div>
            @endforeach
        @else
            <div class="empty-section">No se reportaron hallazgos durante esta visita</div>
        @endif
    </div>

    <!-- Incidents -->
    <div class="section">
        <div class="section-title">5. Incidentes Reportados</div>
        @if($visit->incidentes->count() > 0)
            @foreach($visit->incidentes as $incidente)
                <div class="item">
                    <div class="item-title">
                        {{ $incidente->titulo }}
                        <span class="badge badge-danger">{{ ucfirst($incidente->gravedad) }}</span>
                    </div>
                    <div class="item-detail">{{ $incidente->descripcion }}</div>
                </div>
            @endforeach
        @else
            <div class="empty-section">No se reportaron incidentes durante esta visita</div>
        @endif
    </div>

    <!-- Observations -->
    @if($visit->notes)
    <div class="section">
        <div class="section-title">Observaciones</div>
        <div style="padding: 10px; background: #f9f9f9;">
            {{ $visit->notes }}
        </div>
    </div>
    @endif

    <!-- Signature Section -->
    @if($visit->responsible_name || $visit->responsible_signature)
    <div class="section" style="margin-top: 40px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; text-align: center; vertical-align: bottom;">
                @if($visit->responsible_signature)
                    <img src="{{ $visit->responsible_signature }}" alt="Firma" style="max-height: 80px; max-width: 200px; margin-bottom: 10px;">
                @endif
                <div style="border-top: 2px solid #333; padding-top: 5px; margin: 0 20px;">
                    <strong>{{ $visit->responsible_name ?? 'Responsable de Sede' }}</strong><br>
                    <span style="font-size: 9pt; color: #666;">Responsable de Sede</span>
                </div>
            </div>
            <div style="display: table-cell; width: 50%; text-align: center; vertical-align: bottom;">
                <div style="border-top: 2px solid #333; padding-top: 5px; margin: 0 20px; margin-top: 60px;">
                    <strong>{{ $visit->user->nombres }} {{ $visit->user->apellidos }}</strong><br>
                    <span style="font-size: 9pt; color: #666;">Técnico XamaSafe</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Documento generado automáticamente</strong></p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema XamaSafe v2.0.0</p>
    </div>
</body>
</html>
