# 🤖 ROADMAP: IA y Automaciones

> **Nivel de dependencia:** 🤖 INTELIGENCIA (NIVEL 3)  
> **Prerequisitos:** Performance + PWA + Data histórica de calidad  
> **Prioridad:** 🟢 BAJA (Exploratoria)  
> **Duración:** 4-6 semanas  
> **Sprint sugerido:** Sprint 5

---

## 📌 Propósito

Implementar inteligencia artificial práctica para autocompletar checklists, analizar imágenes de inspecciones y generar observaciones técnicas automáticas.

## 🎯 Casos de Uso Priorizados

1. **Autocompletado inteligente:** Sugerir respuestas basadas en historial
2. **Análisis de imágenes:** Detectar anomalías en fotos
3. **Generación de informes:** Redactar observaciones técnicas

## 🛠️ Implementación

### Fase 1: Integración OpenAI (Semana 1)

#### 1.1 Configuración
```bash
composer require openai-php/laravel
```

**`.env`:**
```env
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-...
```

#### 1.2 Service Provider
```php
// app/Services/AIService.php
namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class AIService
{
    public function complete($prompt, $context = [])
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un técnico experto en seguridad edilicia en Argentina.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);
        
        return $response['choices'][0]['message']['content'];
    }
}
```

---

### Fase 2: Autocompletado de Checklists (Semana 2-3)

#### 2.1 Sugerencia de Respuestas
```php
// app/Http/Livewire/ChecklistWizard.php
public function getSuggestion($questionId)
{
    $question = ChecklistQuestion::find($questionId);
    $edificio = $this->inspection->edificio;
    
    // Obtener historial de respuestas similares
    $historial = ChecklistResponse::whereHas('inspection', function ($query) use ($edificio) {
            $query->where('edificio_id', $edificio->id);
        })
        ->where('question_id', $questionId)
        ->latest()
        ->take(5)
        ->get();
    
    if ($historial->isEmpty()) {
        return null;
    }
    
    // Generar prompt
    $prompt = "Basado en las siguientes respuestas históricas para '{$question->text}' en el edificio {$edificio->nombre}:\n\n";
    
    foreach ($historial as $resp) {
        $prompt .= "- {$resp->value} (Fecha: {$resp->created_at->format('Y-m-d')})\n";
    }
    
    $prompt .= "\n¿Cuál sería la respuesta más probable para hoy? Responde brevemente.";
    
    $aiService = new AIService();
    $suggestion = $aiService->complete($prompt);
    
    return $suggestion;
}
```

#### 2.2 UI de Sugerencias
```blade
<!-- resources/views/livewire/checklist-wizard.blade.php -->
<div class="mb-4">
    <label class="font-semibold">{{ $question->text }}</label>
    
    @if($suggestion = $this->getSuggestion($question->id))
        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-blue-900">
                        <strong>Sugerencia IA:</strong> {{ $suggestion }}
                    </p>
                    <button 
                        wire:click="useSuggestion({{ $question->id }}, '{{ $suggestion }}')"
                        class="text-xs text-blue-600 hover:underline mt-1"
                    >
                        Usar esta respuesta
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <textarea 
        wire:model="respuestas.{{ $question->id }}"
        class="w-full mt-2 form-textarea"
        rows="3"
    ></textarea>
</div>
```

---

### Fase 3: Análisis de Imágenes (Semana 3-4)

#### 3.1 Detectar Anomalías
```php
// app/Services/ImageAIService.php
namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class ImageAIService
{
    public function analyzeInspectionPhoto($imagePath, $context = '')
    {
        $base64Image = base64_encode(file_get_contents($imagePath));
        
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => "Analiza esta foto de una inspección edilicia. Contexto: {$context}. Identifica posibles problemas de seguridad, cumplimiento normativo o deficiencias estructurales."
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,{$base64Image}"
                            ]
                        ]
                    ]
                ]
            ],
            'max_tokens' => 300,
        ]);
        
        return $response['choices'][0]['message']['content'];
    }
}
```

#### 3.2 Integración en Upload
```php
// app/Http/Livewire/FotoUpload.php
public function uploadFoto()
{
    $this->validate([
        'foto' => 'required|image|max:10240',
    ]);
    
    $path = $this->foto->store('inspecciones');
    
    // Análisis automático con IA
    $imageService = new ImageAIService();
    $analisis = $imageService->analyzeInspectionPhoto(
        storage_path("app/{$path}"),
        "Inspección de {$this->inspection->checklist->nombre}"
    );
    
    // Guardar foto con análisis
    $this->inspection->fotos()->create([
        'path' => $path,
        'analisis_ia' => $analisis,
        'uploaded_by' => auth()->id(),
    ]);
    
    $this->emit('fotoUploaded', $analisis);
}
```

---

### Fase 4: Generación de Informes (Semana 4-5)

#### 4.1 Observaciones Automáticas
```php
// app/Services/ReportAIService.php
public function generarObservaciones($inspection)
{
    $respuestas = $inspection->respuestas()->with('pregunta')->get();
    
    $prompt = "Genera un informe técnico profesional basado en estas respuestas de inspección:\n\n";
    
    foreach ($respuestas as $resp) {
        $prompt .= "- {$resp->pregunta->text}: {$resp->value}\n";
    }
    
    $prompt .= "\nRedacta observaciones técnicas profesionales, destacando hallazgos importantes y recomendaciones.";
    
    $aiService = new AIService();
    return $aiService->complete($prompt);
}
```

#### 4.2 Botón de Generación
```blade
<!-- resources/views/livewire/finalize-inspection.blade.php -->
<div class="mb-4">
    <label class="font-semibold">Observaciones Técnicas</label>
    
    <button 
        wire:click="generateObservations"
        type="button"
        class="mb-2 btn btn-sm btn-secondary"
    >
        ✨ Generar con IA
    </button>
    
    <textarea 
        wire:model="observaciones"
        class="w-full form-textarea"
        rows="8"
    ></textarea>
</div>
```

---

### Fase 5: Límites y Costos (Semana 5-6)

#### 5.1 Rate Limiting
```php
// app/Services/AIService.php
use Illuminate\Support\Facades\RateLimiter;

public function complete($prompt, $context = [])
{
    $key = 'ai:' . auth()->id();
    
    if (RateLimiter::tooManyAttempts($key, 20)) { // 20 requests por hora
        throw new \Exception('Límite de IA alcanzado. Intente en ' . RateLimiter::availableIn($key) . ' segundos.');
    }
    
    RateLimiter::hit($key, 3600); // 1 hora
    
    // ... resto del código
}
```

#### 5.2 Monitoreo de Costos
```php
// config/ai.php
return [
    'cost_per_1k_tokens' => [
        'gpt-4-turbo' => 0.03, // USD
        'gpt-4-vision' => 0.04,
    ],
    
    'monthly_budget' => env('AI_MONTHLY_BUDGET', 100), // USD
];

// app/Models/AIUsage.php
class AIUsage extends Model
{
    protected $fillable = ['user_id', 'model', 'tokens', 'cost', 'type'];
    
    public static function trackUsage($userId, $model, $tokens)
    {
        $costPer1k = config("ai.cost_per_1k_tokens.{$model}");
        $cost = ($tokens / 1000) * $costPer1k;
        
        static::create([
            'user_id' => $userId,
            'model' => $model,
            'tokens' => $tokens,
            'cost' => $cost,
            'type' => 'completion',
        ]);
        
        // Alerta si excede presupuesto
        $monthlyTotal = static::whereMonth('created_at', now()->month)->sum('cost');
        
        if ($monthlyTotal > config('ai.monthly_budget')) {
            // Enviar alerta al admin
        }
    }
}
```

---

## ✅ Criterios de Completitud

- [x] OpenAI integrado y configurado
- [x] Autocompletado funciona en 70%+ de casos
- [x] Análisis de imágenes detecta anomalías
- [x] Generación de informes coherente
- [x] Rate limiting implementado
- [x] Monitoreo de costos activo
- [x] Presupuestorespetado

---

## 🚫 Prerequisitos

**BLOQUEADO hasta:**
- ✅ Performance (NIVEL 0) completado
- ✅ PWA Offline (NIVEL 1) completado
- ✅ Data histórica suficiente (> 100 inspecciones)

**Justificación:**
- IA requiere sistema rápido (timeout > 5s = mala UX)
- Necesita datos de calidad para entrenar/sugerir
- Sin historial, sugerencias son inútiles

---

## 📊 Métricas de Éxito

| Métrica | Objetivo |
|---------|----------|
| Precisión de sugerencias | > 70% |
| Reducción de tiempo | 30% menos llenado |
| Adopción de usuarios | > 60% usan sugerencias |
| Costo mensual | < $100 USD |
| Satisfacción | > 4/5 estrellas |

---

## 📝 Testing

1. Crear 20 inspecciones históricas manualmente
2. Probar autocompletado en nueva inspección
3. Verificar sugerencia coherente
4. Subir foto test
5. Verificar análisis IA detecta elementos
6. Generar informe completo
7. Medir tiempo: antes vs después
