<div>
    <div class="mb-6">
        <a href="{{ route('inspections.index') }}" class="text-ep-primary hover:underline">← Volver al Historial</a>
        <h1 class="text-3xl font-bold text-ep-dark mt-2">{{ $inspection->checklist->title }}</h1>
        <div class="flex items-center gap-4 mt-2 text-gray-600">
            <span>
                <strong>Inspector:</strong> 
                {{ $inspection->user->nombres ?? 'N/A' }} {{ $inspection->user->apellidos ?? '' }}
            </span>
            <span>|</span>
            <span>
                <strong>Fecha:</strong> 
                {{ $inspection->completed_at?->format('d/m/Y H:i') ?? 'Sin completar' }}
            </span>
            <span>|</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $inspection->status === 'completed' ? 'Completado' : 'Borrador' }}
            </span>
        </div>
    </div>

    <!-- Respuestas -->
    <div class="bg-white rounded-lg shadow divide-y">
        @forelse($answers as $answer)
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-700">
                            {{ $answer->question->label }}
                            @if($answer->question->is_required)
                                <span class="text-red-500">*</span>
                            @endif
                        </h3>
                        <span class="text-xs text-gray-400">
                            Tipo: {{ ucfirst($answer->question->type) }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-3 p-4 bg-gray-50 rounded-lg">
                    @if($answer->question->type === 'checkbox')
                        @php $values = json_decode($answer->value, true) ?? []; @endphp
                        @if(is_array($values) && count($values) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($values as $val)
                                    <span class="px-3 py-1 bg-ep-primary/10 text-ep-primary rounded-full text-sm">
                                        ✓ {{ $val }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 italic">Sin selección</span>
                        @endif
                    @elseif($answer->question->type === 'signature')
                        @if(str_starts_with($answer->value, 'data:image'))
                            <img src="{{ $answer->value }}" alt="Firma" class="max-h-24 border rounded">
                        @else
                            <span class="italic text-gray-600">{{ $answer->value ?: 'Sin firma' }}</span>
                        @endif
                    @elseif($answer->question->type === 'date')
                        <span class="text-gray-800">
                            {{ $answer->value ? \Carbon\Carbon::parse($answer->value)->format('d/m/Y') : '-' }}
                        </span>
                    @else
                        <span class="text-gray-800 {{ empty($answer->value) ? 'italic text-gray-400' : '' }}">
                            {{ $answer->value ?: 'Sin respuesta' }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-gray-500">
                No hay respuestas registradas para esta inspección.
            </div>
        @endforelse
    </div>

    <!-- Observaciones generales -->
    @if($inspection->notes)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="font-semibold text-gray-700 mb-2">Observaciones Generales</h3>
            <p class="text-gray-600">{{ $inspection->notes }}</p>
        </div>
    @endif

    <!-- Footer con acciones -->
    <div class="mt-6 flex justify-between items-center">
        <a href="{{ route('inspections.index') }}"
           class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            ← Volver al Historial
        </a>
        <div class="flex gap-3">
            <a href="{{ route('inspections.pdf.preview', $inspection) }}"
               target="_blank"
               class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                👁️ Ver PDF
            </a>
            <a href="{{ route('inspections.pdf', $inspection) }}"
               class="px-6 py-3 bg-ep-primary text-white rounded-lg hover:bg-ep-primary-dark">
                📄 Descargar PDF
            </a>
        </div>
    </div>
    <div class="mt-2 text-right text-sm text-gray-400">
        ID: #{{ $inspection->id }} | Creado: {{ $inspection->created_at->format('d/m/Y H:i') }}
    </div>
</div>
