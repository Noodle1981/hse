---
description: Módulo Universal de Activos y Trazabilidad Dinámica
---

# Módulo Universal de Activos y Trazabilidad Dinámica (Asset-Centric Inspections)

Este documento define la especificación técnica y de arquitectura para la implementación del **Módulo Universal de Activos** en XamaSafe. Permite dotar de trazabilidad longitudinal a las inspecciones de distintos rubros sin alterar físicamente la base de datos para cada cliente.

---

## 1. Desafío Arquitectónico: Flexibilidad vs. Trazabilidad SRE

* **El Problema:** Almacenar las respuestas de inspecciones en JSON o en texto plano en la tabla de respuestas es excelente para la flexibilidad, pero inviable para la trazabilidad. Buscar patentes, analizar fallas recurrentes de un vehículo o graficar el aumento de kilometraje recorriendo miles de documentos JSON colapsaría el rendimiento de la base de datos en producción.
* **La Solución:** Desacoplar la estructura del cuestionario y asociar el checklist a un **Activo Genérico (Asset)**. El checklist actualiza de forma automática las métricas clave de dicho activo al finalizarse mediante un motor de actualización en el backend (Laravel Observer).

---

## 2. Diseño de Base de Datos (Esquema Relacional + EAV)

Para dar soporte a múltiples industrias (Logística, Minería, Gastronomía, Seguridad Industrial), implementamos la abstracción de **Activo** e incorporamos los campos dinámicos utilizando campos tipo JSON y relaciones indexadas.

```mermaid
erDiagram
    sedes ||--o{ assets : tiene
    asset_types ||--o{ assets : clasifica
    assets ||--o{ dynamic_inspections : "es inspeccionado en"
    dynamic_questions ||--o{ dynamic_answers : "tiene"
    dynamic_inspections ||--o{ dynamic_answers : "contiene"
```

### Tabla: `asset_types` (Tipos de Activos)
Define las categorías de equipamiento que los clientes manejan.
* `id` (INTEGER, PK)
* `name` (VARCHAR) -> *Ej: "Vehículo Minero", "Autoelevador", "Frigorífico", "Matafuegos"*
* `category_id` (INTEGER, FK) -> *Vínculo con el pilar de categorías de XamaSafe*
* `metadata_schema` (JSON) -> *Esquema dinámico de atributos y alertas (`show_in_create`, `alert_days`)*
* `photo_schema` (JSON) -> *Configuración de fotos requeridas por ranura (ej. 4 para camionetas, 2 para extintores)*
* `created_at` / `updated_at` (DATETIME)

### Tabla: `assets` (Ficha del Activo)
Registra el activo físico real en la sede correspondiente.
* `id` (CHAR(36) UUID, PK) -> *Clave primaria UUID (v4) anti-IDOR*
* `empresa_id` (INTEGER, FK) -> *Garantiza aislamiento estricto (Multitenancy)*
* `sede_id` (INTEGER, FK)
* `asset_type_id` (INTEGER, FK)
* `name` (VARCHAR) -> *Ej: "Camioneta Hilux Toyota", "Frigorífico Principal"*
* `identifier` (VARCHAR) -> *Patente, Nro. de Chasis, Código de Barras o QR (Único por cliente)*
* `status` (VARCHAR) -> *'operativo', 'alerta', 'fuera_de_servicio'*
* `metadata` (JSON) -> *Datos dinámicos de estado: `{"kilometraje": 125000, "ultimo_chofer": "Juan Perez"}`*
* `photos` (JSON) -> *Expediente fotográfico base: `{"foto_frontal": "/storage/assets/xxx.webp"}`*
* `created_at` / `updated_at` / `deleted_at` (DATETIME)

### Tabla: `dynamic_inspections` (Modificación)
* Se añade la columna `asset_id` (INTEGER, FK, nullable).
* Permite que cualquier checklist se vincule opcionalmente a un activo de la sede.

### Tabla: `dynamic_questions` (Modificación)
* Se añade la columna `vincular_a_metadata` (VARCHAR, nullable).
* Almacena el nombre de la variable que esta pregunta actualizará en la ficha del activo (ej: `'kilometraje'`).

---

## 3. Flujo de Ejecución y Automatización

El sistema automatiza la trazabilidad a través de 3 componentes clave:

```
[Gestor: Diseña Checklist] ──> Configura "vincular_a_metadata" = "kilometraje"
                                      │
[Técnico: Realiza Control] ──> Asocia Checklist a "Camioneta AB123CD"
                                      │
[Backend: Laravel Observer] ──> Intercepta el envío y actualiza el JSON de "assets.metadata"
```

### Paso 1: Configuración en el Form Builder (Gestor)
En la interfaz del constructor de plantillas, al crear o editar una pregunta de tipo entrada (número o texto), el Gestor dispone del selector:
* **"Vincular respuesta a métrica del Activo"**: Al activarlo, ingresa el identificador de la métrica (ej. `kilometraje`).

### Paso 2: Ejecución del Checklist (Técnico)
Al iniciar la inspección, la app (React HUD) requiere que el técnico seleccione el activo de la lista de la sede (o escanee su código QR/Patente). Esto asocia la inspección con `asset_id`.

### Paso 3: Sincronización Automática (Backend Laravel)
Un Event Listener en Laravel intercepta la finalización de la inspección para actualizar el activo en caliente:

```php
namespace App\Listeners;

use App\Events\InspectionCompleted;

class SyncAssetMetadata
{
    public function handle(InspectionCompleted $event)
    {
        $inspection = $event->inspection;
        $asset = $inspection->asset;

        if (!$asset) {
            return; // No aplica trazabilidad si no se vinculó un activo
        }

        $metadata = $asset->metadata ?? [];
        $hasChanges = false;

        foreach ($inspection->answers as $answer) {
            $question = $answer->question;

            // Si la pregunta está enlazada a una métrica del activo, actualizamos
            if (!empty($question->vincular_a_metadata)) {
                $metricKey = $question->vincular_a_metadata;
                $metadata[$metricKey] = $answer->value;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $asset->metadata = $metadata;
            $asset->save();
        }
    }
}
```

---

## 4. Consultas de Rendimiento SRE (Trazabilidad Eficiente)

Para generar el historial cronológico y los gráficos de tendencia de un activo (evitando búsquedas pesadas en campos JSON generales), realizamos consultas relacionales indexadas:

```sql
-- Buscar el historial de kilometraje de un activo específico de forma ultra rápida
SELECT 
    ins.created_at AS fecha_control,
    ans.value AS valor_kilometraje
FROM dynamic_answers ans
JOIN dynamic_inspections ins ON ans.inspection_id = ins.id
JOIN dynamic_questions q ON ans.question_id = q.id
WHERE ins.asset_id = ? 
  AND q.vincular_a_metadata = 'kilometraje'
ORDER BY ins.created_at ASC;
```

### Índices Críticos Recomendados:
1. `dynamic_inspections_asset_id_index` en la tabla `dynamic_inspections` (FK).
2. `dynamic_questions_vincular_a_metadata_index` en `dynamic_questions`.
3. Índice compuesto `(inspection_id, question_id)` en `dynamic_answers` para acelerar los joins.

---

## 5. Valor del Negocio y Segmentación Multi-Rubro

* **Aislamiento Multi-Rubro:** Al abstraer la entidad como "Activos" y utilizar especificaciones dinámicas (`metadata`), la misma plataforma sirve para que una empresa de transporte gestione "Camionetas", y una de alimentación controle "Cámaras de Frío", compartiendo el mismo código base.
* **Control de Mantenimientos:** Permite implementar lógica que compare la metadata actual (ej: kilometraje reportado) contra umbrales de mantenimiento predefinidos, gatillando alertas automáticas de servicio o revisiones mecánicas antes de que ocurra un fallo.

---

## 6. Factibilidad de Identificación por Códigos QR (Objetos y Personal)

Para acelerar la captura de datos en terreno y asegurar la veracidad de la información, se propone la integración de lectura nativa de códigos QR.

### 6.1 Identificación de Objetos y Equipos (Activos Físicos)
* **El Método:** Cada activo físico (camionetas, autoelevadores, salas de compresores, matafuegos) dispondrá de una etiqueta de alta resistencia con un código QR único.
* **Flujo de Escaneo (Cámara Móvil):**
  1. El técnico abre la app móvil y pulsa "Escanear QR del Activo".
  2. Al escanear la etiqueta, el sistema resuelve la URL codificada (ej: `https://xamasafe.com/scan/asset/{uuid}`) en la base de datos local o remota.
  3. De forma automática, se levanta el checklist correspondiente al tipo de activo (`asset_type_id`) y se asocia la inspección a `asset_id`.
  4. Se bloquean los inputs manuales de identificación para evitar digitaciones erróneas de patentes, números de chasis o series.

### 6.2 Identificación y Auditoría de Personal
* **El Método:** Cada operario, conductor o técnico contará con una credencial digital (dentro de la app) o física con su código QR de identificación de usuario/empleado (`user_id`).
* **Casos de Uso Operativos:**
  1. **Registro de Capacitaciones:** El capacitador o supervisor puede iniciar una capacitación (`VisitTraining`) y registrar la asistencia simplemente escaneando la credencial QR de cada empleado. Esto inyecta de forma masiva los nombres y DNIs en la base de datos.
  2. **Auditoría de EPP (Inspecciones al Personal):** Al auditar el estado de arneses, cascos o botas de un operario, el inspector escanea el QR de la credencial del empleado. La inspección queda vinculada directamente a la persona, permitiendo analizar tendencias de faltas de seguridad por individuo.
  3. **Firma Rápida en Terreno:** Sustituye la firma táctil clásica por una validación QR de "Presencia del Supervisor", donde el supervisor de obra presenta su QR frente al celular del técnico para firmar de forma inmutable y con validación geográfica por GPS.

### 6.3 Ventajas Clave para el Cliente
* **Cero Errores Manuales:** Se eliminan por completo los registros falsos o mal redactados (patentes mal escritas, DNIs con números de más).
* **Velocidad de Operación:** Reducción del tiempo de inicio de un checklist o toma de asistencia de 2 minutos a menos de 5 segundos.
* **Seguridad SRE (Prueba de Presencia):** Se garantiza que el técnico estuvo físicamente frente al objeto o a la persona auditada, brindando un estándar de control superior para el cliente corporativo.

