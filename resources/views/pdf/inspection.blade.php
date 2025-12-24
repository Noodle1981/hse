<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inspección #{{ $inspection->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            background-color: #1d2d35;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 12px;
            opacity: 0.8;
        }
        .logo {
            width: 60px;
            height: 60px;
            background-color: #FF8F12;
            color: white;
            font-weight: bold;
            font-size: 24px;
            text-align: center;
            line-height: 60px;
            border-radius: 8px;
            float: left;
            margin-right: 15px;
        }
        .info-box {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 5px 10px;
        }
        .info-box .label {
            color: #666;
            font-weight: bold;
            width: 150px;
        }
        .questions {
            margin-top: 20px;
        }
        .question {
            border: 1px solid #ddd;
            margin-bottom: 15px;
            border-radius: 5px;
            overflow: hidden;
        }
        .question-header {
            background-color: #f9f9f9;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }
        .question-label {
            font-weight: bold;
            color: #1d2d35;
        }
        .question-type {
            font-size: 10px;
            color: #FF8F12;
            margin-left: 10px;
        }
        .question-answer {
            padding: 15px;
            background-color: white;
        }
        .answer-value {
            color: #333;
        }
        .answer-empty {
            color: #999;
            font-style: italic;
        }
        .checkbox-item {
            display: inline-block;
            background-color: #FF8F12;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff8e6;
            border: 1px solid #FF8F12;
            border-radius: 5px;
        }
        .notes-section h3 {
            color: #FF8F12;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #1d2d35;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-draft {
            background-color: #fff3cd;
            color: #856404;
        }
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">EP</div>
        <h1>{{ $inspection->checklist->title }}</h1>
        <div class="subtitle">
            Inspección #{{ $inspection->id }} | 
            {{ $inspection->completed_at?->format('d/m/Y H:i') ?? 'Sin completar' }}
        </div>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <table>
            <tr>
                <td class="label">Inspector:</td>
                <td>{{ $inspection->user->nombres ?? 'N/A' }} {{ $inspection->user->apellidos ?? '' }}</td>
                <td class="label">Email:</td>
                <td>{{ $inspection->user->email ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Checklist:</td>
                <td>{{ $inspection->checklist->title }}</td>
                <td class="label">Categoría:</td>
                <td>{{ ucfirst($inspection->checklist->category ?? 'Sin categoría') }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de Inspección:</td>
                <td>{{ $inspection->completed_at?->format('d/m/Y') ?? '-' }}</td>
                <td class="label">Estado:</td>
                <td>
                    <span class="status-badge {{ $inspection->status === 'completed' ? 'status-completed' : 'status-draft' }}">
                        {{ $inspection->status === 'completed' ? 'Completado' : 'Borrador' }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Questions and Answers -->
    <div class="questions">
        <h2 style="margin-bottom: 15px; color: #1d2d35;">Respuestas</h2>
        
        @foreach($answers as $answer)
            <div class="question">
                <div class="question-header">
                    <span class="question-label">
                        {{ $answer->question->label }}
                        @if($answer->question->is_required)
                            <span class="required">*</span>
                        @endif
                    </span>
                    <span class="question-type">[{{ ucfirst($answer->question->type) }}]</span>
                </div>
                <div class="question-answer">
                    @if($answer->question->type === 'checkbox')
                        @php $values = json_decode($answer->value, true) ?? []; @endphp
                        @if(is_array($values) && count($values) > 0)
                            @foreach($values as $val)
                                <span class="checkbox-item">✓ {{ $val }}</span>
                            @endforeach
                        @else
                            <span class="answer-empty">Sin selección</span>
                        @endif
                    @elseif($answer->question->type === 'date' && $answer->value)
                        <span class="answer-value">{{ \Carbon\Carbon::parse($answer->value)->format('d/m/Y') }}</span>
                    @elseif($answer->question->type === 'signature')
                        @if(str_starts_with($answer->value ?? '', 'data:image'))
                            <img src="{{ $answer->value }}" alt="Firma" style="max-height: 60px; border: 1px solid #ddd; border-radius: 4px;">
                        @else
                            <span class="answer-value">{{ $answer->value ?: 'Sin firma' }}</span>
                        @endif
                    @else
                        @if($answer->value)
                            <span class="answer-value">{{ $answer->value }}</span>
                        @else
                            <span class="answer-empty">Sin respuesta</span>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Notes Section -->
    @if($inspection->notes)
        <div class="notes-section">
            <h3>📝 Observaciones Generales</h3>
            <p>{{ $inspection->notes }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>EP - Sistema de Checklists Dinámicos V1.0.0.0</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }} | Inspección ID: #{{ $inspection->id }}</p>
    </div>
</body>
</html>
