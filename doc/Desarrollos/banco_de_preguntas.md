# BANCO DE PREGUNTAS - EP-BACK

## 📋 GUÍA PARA LA IA

Este documento centraliza preguntas técnicas y arquitectónicas del proyecto EP-Back. Cada pregunta debe incluir:
- **Categoría**: Área técnica
- **Prioridad**: Alta/Media/Baja
- **Estado**: Pendiente/Respondida/Implementada
- **Fecha**: Última actualización
- **Respondido por**: IA/Human

---

## PREGUNTA 1: Gestión de PDFs

**Categoría:** Librerías  
**Prioridad:** Media  
**Estado:** Respondida  
**Fecha:** 2025-12-31  
**Respondido por:** IA

### Pregunta
Las librerías DomPDF + Spatie PDF (Reportes), ¿por qué tiene dos librerías para generar PDFs? ¿Cuál es la diferencia entre ellas? ¿No es más factible usar una sola?

### Respuesta

**Situación actual en EP-Back:**
```json
"barryvdh/laravel-dompdf": "^3.1"  // Motor de bajo nivel
"spatie/laravel-pdf": "^1.5"       // Wrapper moderno
```

**Diferencias clave:**

1. **DomPDF (barryvdh/laravel-dompdf)**
   - Motor de renderizado PHP nativo
   - Convierte HTML/CSS a PDF
   - Más control granular
   - Compatible con PHP 8.2+

2. **Spatie PDF**
   - Wrapper que usa Chromium/Puppeteer por defecto
   - Mejor renderizado CSS moderno (Grid, Flexbox)
   - Soporta JavaScript en PDFs
   - Puede usar DomPDF como fallback

**Recomendación:** ✅ **Mantener ambas**

**Estrategia de uso:**
- **Spatie PDF**: Reportes complejos (dashboards, gráficos, tablas avanzadas)
- **DomPDF**: Certificados, constancias simples (más rápido, menos recursos)

**Acción sugerida:**
Documentar en código cuándo usar cada una:
```php
// Para reportes simples
use Barryvdh\DomPDF\Facade\Pdf;

// Para reportes complejos con CSS moderno
use Spatie\LaravelPdf\Facades\Pdf;
```

---

## PREGUNTA 2: Firmas Digitales

**Categoría:** Seguridad/UX  
**Prioridad:** Alta  
**Estado:** Respondida  
**Fecha:** 2025-12-31  
**Respondido por:** IA

### Pregunta
Cuello de botella con las firmas digitales en cuanto a legibilidad. ¿Cómo se puede mejorar? ¿Qué tecnologías se requieren?

### Respuesta

**Problemas identificados:**
1. Firma manuscrita en canvas con baja resolución
2. Se almacena como imagen PNG/JPEG con pérdida de calidad
3. Al incrustar en PDFs se degrada más

**Solución Técnica:**

### Corto Plazo (2-4 semanas)
1. **Mejorar captura actual:**
   ```javascript
   // Aumentar resolución del canvas
   const canvas = document.querySelector('canvas');
   const scale = 3; // 3x resolución
   canvas.width = width * scale;
   canvas.height = height * scale;
   canvas.getContext('2d').scale(scale, scale);
   ```

2. **Almacenar como SVG:**
   - Usar librería `signature_pad` con exportación SVG
   - Vector escalable sin pérdida
   - Tamaño de archivo menor

3. **Optimizar almacenamiento:**
   ```php
   // Guardar en base64 optimizado
   Storage::put("firmas/{$id}.svg", $signatureSVG);
   ```

### Mediano Plazo (1-3 meses)
**Implementar firma digital certificada:**

1. **Tecnología: PKCS#7 / PDF Signing**
   ```bash
   composer require tecnickcom/tcpdf
   # o alternativamente
   composer require setasign/fpdf
   ```

2. **Flujo:**
   - Usuario firma con certificado digital (token USB/archivo .p12)
   - Backend valida certificado
   - PDF se firma criptográficamente
   - Verificable por terceros

3. **Proveedores en Argentina:**
   - AFIP (Certificados tributarios)
   - Anses
   - Proveedores privados certificados

### Largo Plazo (6+ meses)
**Firma biométrica avanzada:**
- Captura presión, velocidad, aceleración
- Análisis forense de autenticidad
- Evidencia legal robusta

**Recomendación inmediata:**
Implementar SVG + aumento de resolución (1-2 días de desarrollo)

---

## PREGUNTA 3: Blockchain, Tokens e IA

**Categoría:** Innovación  
**Prioridad:** Baja (Exploratoria)  
**Estado:** Respondida  
**Fecha:** 2025-12-31  
**Respondido por:** IA

### Pregunta
Blockchain, tokens e IA, ¿cómo se puede implementar en el sistema?

### Respuesta

**Análisis de viabilidad:**

### 1. Blockchain
**Caso de uso real para EP-Back:**
Inmutabilidad de inspecciones y certificados

**Implementación:**
```php
// Usar Laravel con blockchain privado
composer require web3p/web3.php

// Hash de certificado almacenado en blockchain
$certificateHash = hash('sha256', json_encode($certificateData));
$blockchain->storeHash($certificateHash, $certificateId);
```

**Tecnologías:**
- **Hyperledger Fabric**: Blockchain privado para entidades
- **Ethereum (privado)**: Smart contracts
- **IPFS**: Almacenamiento descentralizado de PDFs

**ROI:** ⚠️ Alto costo, beneficio limitado para escala actual

### 2. Tokens
**Posible aplicación:**
- Token por inspección completada (gamificación técnicos)
- NFT de certificados únicos
- Sistema de recompensas interno

**Complejidad vs Beneficio:** ❌ No justificado actualmente

### 3. IA (Implementación práctica)
**Casos de uso REALES y viables:**

**A. Análisis de Imágenes en Inspecciones**
```php
use OpenAI\Client;

// Detectar anomalías en fotos de inspección
$response = $client->images()->createVariation([
    'image' => fopen('foto_inspeccion.jpg', 'r'),
    'n' => 1,
]);
```

**B. Asistente para llenar checklists**
```javascript
// Autocompletar basado en historial
const suggestion = await openai.complete({
    prompt: `Basado en inspecciones previas del edificio ${id}, sugiere respuestas`,
    model: 'gpt-4'
});
```

**C. Generación automática de informes**
```php
// Redactar observaciones técnicas
$observaciones = OpenAI::chat([
    'role' => 'system', 
    'content' => 'Eres un técnico de seguridad edilicia',
    'user' => "Genera informe de: {$checklistData}"
]);
```

**Recomendación:** ✅ Priorizar IA para automaciones, posponer blockchain

---

## PREGUNTA 4: Sistema Offline

**Categoría:** Funcionalidad  
**Prioridad:** Alta  
**Estado:** Respondida  
**Fecha:** 2025-12-31  
**Respondido por:** IA

### Pregunta
Sistema offline para trabajo de campo sin internet en zonas alejadas.

### Respuesta

**Arquitectura recomendada: PWA (Progressive Web App)**

### Implementación en EP-Back

**1. Service Worker (Cache de datos)**
```javascript
// resources/js/sw.js
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('ep-back-v1').then(cache => {
            return cache.addAll([
                '/offline',
                '/css/app.css',
                '/js/app.js',
                '/api/checklists/templates'
            ]);
        })
    );
});
```

**2. IndexedDB para almacenamiento local**
```javascript
import Dexie from 'dexie';

const db = new Dexie('EPBackOffline');
db.version(1).stores({
    inspecciones: '++id, tecnico_id, fecha, synced',
    fotos: '++id, inspeccion_id, blob',
    checklists: 'id, template'
});

// Guardar offline
await db.inspecciones.add({
    tecnico_id: userId,
    datos: checklistData,
    synced: false
});
```

**3. Sincronización en background**
```php
// Laravel: Job de sincronización
class SyncOfflineInspections implements ShouldQueue
{
    public function handle()
    {
        $pendientes = OfflineInspection::where('synced', false)->get();
        foreach ($pendientes as $inspeccion) {
            Inspection::create($inspeccion->data);
            $inspeccion->update(['synced' => true]);
        }
    }
}
```

**4. Manifest PWA**
```json
{
    "name": "EP-Back Inspecciones",
    "short_name": "EP-Back",
    "start_url": "/tecnico/dashboard",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#4F46E5",
    "icons": [...]
}
```

**Flujo de trabajo:**
1. Técnico descarga checklists asignados antes de salir (WiFi)
2. Completa inspecciones offline
3. Captura fotos (almacenadas localmente)
4. Al regresar con internet, sincroniza automáticamente
5. Sistema detecta conflictos y los resuelve

**Tiempo de implementación:** 3-4 semanas

---

## PREGUNTA 5: App Móvil vs Web

**Categoría:** Plataforma  
**Prioridad:** Media  
**Estado:** Respondida  
**Fecha:** 2025-12-31  
**Respondido por:** IA

### Pregunta
¿Es necesaria una app de celular? ¿La vía web cumple con los requisitos?

### Respuesta

**Comparativa técnica:**

| Característica | PWA (Web) | App Nativa |
|---|---|---|
| Desarrollo | 1 codebase | 2 codebases (iOS/Android) |
| Costo | ~$5K USD | ~$20K USD |
| Offline | ✅ Service Workers | ✅ Nativo |
| Cámara | ✅ Web API | ✅ Mejor calidad |
| GPS | ✅ Geolocation API | ✅ Más preciso |
| Notificaciones | ✅ Push | ✅ Push |
| Instalación | Navegador | App Store/Play Store |
| Actualizaciones | Inmediatas | Requiere aprobación |

**Recomendación: PWA primero**

**Razones:**
1. **Costo-beneficio**: 70% de funcionalidad a 20% del costo
2. **Tu stack actual**: Laravel + Livewire = perfecto para PWA
3. **Usuarios ya usan navegadores**: menor fricción
4. **Actualizaciones instantáneas**: sin esperar stores

**Cuándo sí necesitas app nativa:**
- Uso intensivo de cámara (> 50 fotos/día)
- Necesitas Bluetooth/NFC
- Requisitos de performance extremos

**Plan sugerido:**
1. **Fase 1 (ahora)**: PWA con funcionalidad offline
2. **Fase 2 (6 meses)**: Si > 70% usuarios móviles y demandan app
3. **Tecnología futura**: Flutter (1 código, 2 plataformas)

---

## PREGUNTA 6: Velocidad y Estabilidad

**Categoría:** Performance  
**Prioridad:** Alta  
**Estado:** Respondida  
**Fecha:** 2025-12-31  
**Respondido por:** IA

### Pregunta
¿Cómo se planifican la velocidad del sistema y la estabilidad?

### Respuesta

**Estrategia de Performance para EP-Back:**

### 1. Monitoreo (Implementar YA)

**Laravel Horizon/Pulse:**
```bash
composer require laravel/pulse
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
```

**Métricas clave:**
- Tiempo de respuesta por ruta
- Consultas SQL lentas (> 100ms)
- Uso de memoria por request
- Tasa de errores

### 2. Optimizaciones Base de Datos

**Auditoría actual:**
```bash
php artisan debugbar:enable
# Revisar queries N+1
```

**Implementar:**
```php
// Eager loading obligatorio
Inspection::with(['checklist', 'tecnico', 'edificio'])
    ->whereDate('created_at', today())
    ->get();

// Índices críticos
Schema::table('inspections', function (Blueprint $table) {
    $table->index(['tecnico_id', 'created_at']);
    $table->index(['edificio_id', 'status']);
});
```

### 3. Caché Estratégico

```php
// Cache del dashboard (5 min)
$stats = Cache::remember('dashboard_stats_'.$userId, 300, function () {
    return [
        'inspections_today' => Inspection::today()->count(),
        'pending' => Inspection::pending()->count(),
    ];
});

// Cache de checklists templates
$templates = Cache::rememberForever('checklist_templates', function () {
    return ChecklistTemplate::all();
});
```

### 4. Optimización de Assets

```bash
# Vite production build
npm run build

# Lazy loading de imágenes
<img loading="lazy" src="foto.jpg">
```

### 5. Planificación Continua

**Crear monitoreo semanal:**
```php
// app/Console/Commands/PerformanceReport.php
Artisan::command('performance:report', function () {
    $slowQueries = DB::getQueryLog()->where('time', '>', 100);
    Mail::to('admin@ep-back.com')->send(new PerformanceReport($slowQueries));
});

// Programar en Kernel
$schedule->command('performance:report')->weekly();
```

**Objetivos medibles:**
- Tiempo de carga < 2 segundos (páginas)
- Tiempo de carga < 500ms (API)
- Disponibilidad > 99.5%
- Error rate < 0.1%

**Testing de carga:**
```bash
# Simular 100 usuarios concurrentes
composer require --dev laravel/dusk
php artisan dusk:stress --users=100
```

---

## 📊 RESUMEN DE IMPLEMENTACIÓN

| Pregunta | Prioridad | Esfuerzo | ROI |
|---|---|---|---|
| PDFs | Media | 1 día | Alto |
| Firmas | Alta | 2 semanas | Muy Alto |
| Blockchain/IA | Baja | 3 meses | Bajo (ahora) |
| Offline | Alta | 4 semanas | Muy Alto |
| App Móvil | Media | PWA: 3 sem | Alto |
| Performance | Alta | Continuo | Crítico |

**Roadmap sugerido:**
1. **Sprint 1**: Performance + Firmas SVG
2. **Sprint 2**: Sistema Offline PWA
3. **Sprint 3**: IA para autocompletado
4. **Sprint 4**: Evaluación app nativa
