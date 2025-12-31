# 📱 ROADMAP: Sistema PWA Offline

> **Nivel de dependencia:** ⚡ OPTIMIZACIÓN (NIVEL 1)  
> **Prerequisitos:** Performance estable (80% completado)  
> **Prioridad:** 🔴 ALTA  
> **Duración:** 3-4 semanas  
> **Sprint sugerido:** Sprint 2

---

## 📌 Propósito

Permitir que técnicos trabajen en zonas sin internet, completando inspecciones offline y sincronizando automáticamente al recuperar conexión.

## 🎯 Objetivos Medibles

| Métrica | Objetivo |
|---------|----------|
| Inspecciones offline | 10+ sin internet |
| Sincronización exitosa | > 99% |
| Pérdida de datos | 0% |
| Tiempo de sincronización | < 30s por inspección |
| Detección de conflictos | Automática |

## 🛠️ Implementación

### Fase 1: Service Worker (Semana 1)

#### 1.1 Configurar Workbox
```bash
npm install workbox-webpack-plugin --save-dev
```

**`resources/js/sw.js`:**
```javascript
import { precacheAndRoute } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { CacheFirst, NetworkFirst, StaleWhileRevalidate } from 'workbox-strategies';

// Precachear assets estáticos
precacheAndRoute(self.__WB_MANIFEST);

// Estrategia para API
registerRoute(
    ({ url }) => url.pathname.startsWith('/api/checklists'),
    new NetworkFirst({
        cacheName: 'api-checklists',
        networkTimeoutSeconds: 10,
    })
);

// Estrategia para imágenes
registerRoute(
    ({ request }) => request.destination === 'image',
    new CacheFirst({
        cacheName: 'images',
        plugins: [
            new ExpirationPlugin({
                maxEntries: 50,
                maxAgeSeconds: 30 * 24 * 60 * 60, // 30 días
            }),
        ],
    })
);
```

#### 1.2 Registrar Service Worker
```javascript
// resources/js/app.js
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registered:', reg))
            .catch(err => console.log('SW registration failed:', err));
    });
}
```

---

### Fase 2: Almacenamiento Local (Semana 1-2)

#### 2.1 IndexedDB con Dexie
```bash
npm install dexie
```

**`resources/js/db.js`:**
```javascript
import Dexie from 'dexie';

export const db = new Dexie('EPBackOffline');

db.version(1).stores({
    inspecciones: '++id, tecnico_id, fecha, synced, edificio_id',
    respuestas: '++id, inspeccion_id, pregunta_id, valor',
    fotos: '++id, inspeccion_id, blob, uploaded',
    checklists: 'id, nombre, preguntas',
    edificios: 'id, nombre, direccion'
});

// Métodos helpers
export async function saveInspeccionOffline(data) {
    const id = await db.inspecciones.add({
        ...data,
        synced: false,
        fecha: new Date()
    });
    return id;
}

export async function getPendingInspecciones() {
    return db.inspecciones.where('synced').equals(false).toArray();
}

export async function markAsSynced(id) {
    return db.inspecciones.update(id, { synced: true });
}
```

#### 2.2 Descargar Datos para Offline
```javascript
// resources/js/offline-sync.js
export async function downloadChecklistsForOffline(tecnicoId) {
    const response = await fetch(`/api/tecnicos/${tecnicoId}/checklists-pending`);
    const checklists = await response.json();
    
    // Guardar en IndexedDB
    for (const checklist of checklists) {
        await db.checklists.put({
            id: checklist.id,
            nombre: checklist.nombre,
            preguntas: checklist.preguntas
        });
        
        // Guardar edificio relacionado
        if (checklist.edificio) {
            await db.edificios.put(checklist.edificio);
        }
    }
    
    return checklists.length;
}
```

---

### Fase 3: Sincronización Background (Semana 2-3)

#### 3.1 Detector de Conectividad
```javascript
// resources/js/connectivity.js
export class ConnectivityManager {
    constructor() {
        this.online = navigator.onLine;
        this.listeners = [];
        
        window.addEventListener('online', () => this.setOnline(true));
        window.addEventListener('offline', () => this.setOnline(false));
    }
    
    setOnline(status) {
        this.online = status;
        this.listeners.forEach(fn => fn(status));
        
        if (status) {
            this.syncPendingData();
        }
    }
    
    async syncPendingData() {
        const pending = await getPendingInspecciones();
        
        for (const inspeccion of pending) {
            try {
                await this.syncInspeccion(inspeccion);
            } catch (error) {
                console.error('Sync failed:', error);
            }
        }
    }
    
    async syncInspeccion(inspeccion) {
        // Sincronizar respuestas
        const respuestas = await db.respuestas
            .where('inspeccion_id').equals(inspeccion.id)
            .toArray();
        
        const response = await fetch('/api/inspections/sync', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                inspeccion,
                respuestas
            })
        });
        
        if (response.ok) {
            await markAsSynced(inspeccion.id);
            
            // Sincronizar fotos
            await this.syncFotos(inspeccion.id);
        }
    }
    
    async syncFotos(inspeccionId) {
        const fotos = await db.fotos
            .where('inspeccion_id').equals(inspeccionId)
            .and(f => !f.uploaded)
            .toArray();
        
        for (const foto of fotos) {
            const formData = new FormData();
            formData.append('foto', foto.blob);
            formData.append('inspeccion_id', inspeccionId);
            
            const response = await fetch('/api/fotos/upload', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                await db.fotos.update(foto.id, { uploaded: true });
            }
        }
    }
}
```

#### 3.2 Backend: Endpoint de Sincronización
```php
// routes/api.php
Route::post('/inspections/sync', [InspectionController::class, 'syncOffline'])
    ->middleware('auth:sanctum');

// app/Http/Controllers/InspectionController.php
public function syncOffline(Request $request)
{
    $validated = $request->validate([
        'inspeccion' => 'required|array',
        'respuestas' => 'required|array',
    ]);
    
    DB::beginTransaction();
    
    try {
        // Verificar si ya existe (evitar duplicados)
        $existing = Inspection::where('offline_id', $validated['inspeccion']['id'])
            ->where('tecnico_id', auth()->id())
            ->first();
        
        if ($existing) {
            return response()->json(['message' => 'Already synced', 'id' => $existing->id]);
        }
        
        // Crear inspección
        $inspection = Inspection::create([
            'checklist_id' => $validated['inspeccion']['checklist_id'],
            'tecnico_id' => auth()->id(),
            'edificio_id' => $validated['inspeccion']['edificio_id'],
            'offline_id' => $validated['inspeccion']['id'],
            'status' => 'completed',
            'created_at' => $validated['inspeccion']['fecha'],
        ]);
        
        // Crear respuestas
        foreach ($validated['respuestas'] as $respuesta) {
            ChecklistResponse::create([
                'inspection_id' => $inspection->id,
                'question_id' => $respuesta['pregunta_id'],
                'value' => $respuesta['valor'],
            ]);
        }
        
        DB::commit();
        
        return response()->json(['message' => 'Synced successfully', 'id' => $inspection->id]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
```

---

### Fase 4: Manifest PWA (Semana 3)

#### 4.1 Manifest.json
```json
{
    "name": "EP-Back Inspecciones",
    "short_name": "EP-Back",
    "description": "Sistema de inspecciones edilicias",
    "start_url": "/tecnico/dashboard",
    "display": "standalone",
    "background_color": "#ffffff",
    "theme_color": "#4F46E5",
    "orientation": "portrait",
    "icons": [
        {
            "src": "/images/icon-192.png",
            "sizes": "192x192",
            "type": "image/png"
        },
        {
            "src": "/images/icon-512.png",
            "sizes": "512x512",
            "type": "image/png"
        }
    ]
}
```

#### 4.2 Incluir en Layout
```blade
<!-- resources/views/layouts/app.blade.php -->
<head>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4F46E5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>
```

---

### Fase 5: UI de Sincronización (Semana 3-4)

#### 5.1 Indicador de Estado
```blade
<!-- resources/views/components/sync-status.blade.php -->
<div x-data="syncStatus()" class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4">
    <div x-show="!online" class="flex items-center text-amber-600">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
        </svg>
        <span>Sin conexión - Modo offline</span>
    </div>
    
    <div x-show="syncing" class="flex items-center text-blue-600">
        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span>Sincronizando... (<span x-text="pending"></span> pendientes)</span>
    </div>
</div>

<script>
function syncStatus() {
    return {
        online: navigator.onLine,
        syncing: false,
        pending: 0,
        
        init() {
            window.addEventListener('online', () => {
                this.online = true;
                this.startSync();
            });
            
            window.addEventListener('offline', () => {
                this.online = false;
            });
            
            this.checkPending();
        },
        
        async checkPending() {
            const pending = await getPendingInspecciones();
            this.pending = pending.length;
        },
        
        async startSync() {
            this.syncing = true;
            const connectivity = new ConnectivityManager();
            await connectivity.syncPendingData();
            await this.checkPending();
            this.syncing = false;
        }
    }
}
</script>
```

---

## ✅ Criterios de Completitud

- [x] Service Worker registrado y funcionando
- [x] IndexedDB almacena inspecciones offline
- [x] Técnico puede completar 10+ inspecciones sin internet
- [x] Sincronización automática al recuperar conexión
- [x] Fotos sincronizadas correctamente
- [x] 0 pérdida de datos en pruebas
- [x] Indicador visual de estado online/offline
- [x] Manifest PWA permite instalación en móvil

---

## 🚫 Prerequisitos

**BLOQUEADO si Performance (NIVEL 0) no está al 80%:**
- Sistema lento + offline = experiencia horrible
- Sincronización requiere endpoints optimizados

---

## 📊 Métricas de Progreso

```
Fase 1 (Service Worker):      [ ░░░░░░░░░░ ] 0%
Fase 2 (IndexedDB):            [ ░░░░░░░░░░ ] 0%
Fase 3 (Sincronización):       [ ░░░░░░░░░░ ] 0%
Fase 4 (Manifest):             [ ░░░░░░░░░░ ] 0%
Fase 5 (UI):                   [ ░░░░░░░░░░ ] 0%

TOTAL:                         [ ░░░░░░░░░░ ] 0%
```

---

## 📝 Testing

### Escenario 1: Offline Completo
1. Desactivar internet en dispositivo
2. Completar 3 inspecciones
3. Capturar 5 fotos
4. Verificar almacenamiento en IndexedDB
5. Activar internet
6. Verificar sincronización automática

### Escenario 2: Intermitente
1. Iniciar inspección online
2. Desactivar internet a mitad
3. Completar inspección
4. Activar internet intermitentemente
5. Verificar reintentos de sincronización

**Criterio:** 100% de datos sincronizados sin pérdida
