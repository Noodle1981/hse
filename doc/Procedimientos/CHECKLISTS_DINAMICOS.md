# 📝 Manual de Usuario: Auditorías e Inspecciones (v2.2)

Este manual explica cómo construir formularios de control y realizar inspecciones técnicas en campo utilizando el motor de **Checklists Dinámicos** de **XamaSafe**.

---

## 🎯 Visión General
El sistema permite digitalizar cualquier tipo de inspección técnica, eliminando el uso de formularios en papel y centralizando los resultados en reportes automáticos con validez legal.

---

## 🎨 Identidad Visual (Pilar Inspecciones)
Todos los componentes interactivos, botones de acción, sliders, tabs y barras de progreso asociados a las auditorías y checklists utilizan estrictamente el color **Naranja Técnico / Naranja** (usando clases como `ep-primary`, `orange-500`, `amber-500`, etc.).

---

## 👤 Roles y Responsabilidades

| Rol | Acción |
|---|---|
| **Gestor (Administrador)** | Crear "Plantillas de Checklist", configurar secciones y preguntas con reglas de fallas automáticas (`failure_logic`), y asignar coberturas mensuales. |
| **Técnico de Campo** | Buscar y auto-asignarse plantillas, responder el wizard interactivo en campo, y firmar. |

---

## 🚀 Guía Paso a Paso

### 1. Construcción del Formulario y Lógica de Falla (Gestores)
1. Dirígete a **Configuración > Catálogo de Checklists**.
2. Define las secciones (`ChecklistSection`) y agrega las preguntas (`DynamicQuestion`).
3. **Reglas de Falla Automáticas:**
   - **failure_values:** Determina qué respuestas constituyen no-conformidades (ej. "No", "Malo").
   - **failure_logic:** Configura si falla si se marca cualquiera (`fail_if_selected`) o si falta alguna en multiselección (`fail_if_missing`).
   - **Rangos Numéricos:** Si es un campo numérico, determina límites `min_value` y `max_value` que disparan la falla automáticamente fuera del rango.

### 2. Ejecución y Blindaje en Caliente (Técnicos)
Durante la visita técnica presencial:
1. En **Mis Asignaciones (Misiones)**, selecciona el checklist correspondiente o pulsa `[+] Inspeccionar` para buscar con el filtro jerárquico de Categorías y Subcategorías.
2. Completa los puntos de control en el **Wizard de Inspección**.
3. **HUD Sync Badge (Seguridad SRE):** En la cabecera del wizard, verás un badge que pulsa en naranja técnico: `"⌚ Tiempo Real (Sync Servidor)"`.
4. **Blindaje e Inmutabilidad de Tiempos:** Al finalizar la inspección, el backend Laravel descarta de forma absoluta cualquier marca de tiempo enviada por el navegador (evitando fraudes horariales) y escribe el timestamp `now()` del servidor. Cualquier manipulación sospechosa genera un `Log::warning()` con la IP y datos del técnico.
5. **Counter Badges HUD:** Para inspecciones continuas y masivas de activos, verás badges flotantes en las esquinas (`absolute -top-3 -right-3`) indicando el número de inspecciones activas en caliente.

### 3. Métricas y Bóveda (Visualización)
1. Al volver al historial, la celda **"Checklists"** detalla la proporción del ratio de éxitos (`Exitosos: X/Y` en verde si pasaron, o en rojo con el icono `⚠️` si falló algún control) y el porcentaje de cumplimiento normativo destacado con sombra de texto glow (`glow-text`).

---

## 💡 Tips de Eficiencia
- **Filtros en Cascada:** El buscador te permite filtrar instantáneamente por texto, categoría y subcategoría.
- **Scrollbar Naranja:** Las tablas e historiales de visitas emplean un scrollbar naranja translúcido (`.custom-scrollbar`) que te advierte la capacidad de deslizamiento horizontal sin arruinar la interfaz.

---

## 📝 Resolución de Problemas
- **¿Qué pasa si modifico la fecha en mi celular?** El sistema lo detectará como un intento de bypass de horario, lo ignorará forzando la hora del servidor y registrará el evento en el log de auditoría SRE.
- **Una inspección no-conformante no se ve roja:** Si se detectó una no-conformidad, la fila de la inspección lucirá un badge de fallo en rojo neón con el icono de advertencia.

---
*Manual actualizado para XamaSafe v2.2 | Terminal Táctica Design*
