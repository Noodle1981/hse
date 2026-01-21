# 📚 ÍNDICE DE DOCUMENTACIÓN - EP-BACK

> **Última actualización:** 2025-12-31  
> **Propósito:** Navegación rápida de toda la documentación del proyecto

---

## 🗂️ Estructura de Directorios

```
doc/
├── 📘 AI_FRAMEWORK.md              ← Framework universal AI-driven (replicable)
├── 📄 WORKFLOW.md                  ← Workflow específico del equipo EP-Back
├── 📝 credenciales.md              ← Credenciales y accesos del sistema
├── 📊 master_demo_seeder.md        ← Documentación del seeder de datos
│
├── 📁 Desarrollos/
│   ├── banco_de_preguntas.md      ← Knowledge base técnico (Q&A)
│   ├── nuevos_requerimientos.md   ← Inbox de nuevos features
│   └── Auditorías/
│       └── auditoriageneral.md    ← Docs de auditoría EDUGE
│
├── 📁 Lógica_del_diseño/
│   ├── COMPONENTES_LIVEWARE.MD    ← Catálogo de componentes Livewire
│   ├── ESCALABILIDAD.MD           ← Arquitectura y plan de escalabilidad
│   ├── TAREAS.MD                  ← Resumen de módulos completados
│   ├── TABLAS.MD                  ← Esquema completo de base de datos
│   ├── VISUAL.MD                  ← Sistema de diseño visual
│   ├── sistema_de_permisos.md     ← Roles y permisos
│   └── workflow_checklist.md      ← Workflow del sistema de checklists
│
├── 📁 Procedimientos/
│   ├── (10 archivos)              ← Procedimientos técnicos específicos
│   └── ...
│
└── 📁 Roadmap/                     ← 🎯 NUEVO SISTEMA DE ROADMAPS
    ├── ROADMAP_MAESTRO.md         ← Índice maestro con dependencias
    ├── roadmap_performance.md     ← Sprint 1 (Fundación)
    ├── roadmap_firmas.md          ← Sprint 1 (Fundación)
    ├── roadmap_pwa_offline.md     ← Sprint 2 (Optimización)
    ├── roadmap_sidebar.md         ← Sprint 3 (UX)
    ├── roadmap_usuarios_empresariales.md  ← Sprint 4 (UX)
    └── roadmap_ia_automaciones.md ← Sprint 5 (IA)
```

---

## 🎯 DOCUMENTOS CLAVE PARA PM

### 1️⃣ **ROADMAP_MAESTRO.md** 🔥 EMPEZAR AQUÍ

**Ubicación:** [`doc/Roadmap/ROADMAP_MAESTRO.md`](file:///d:/EPPSaaS/ep-back/doc/Roadmap/ROADMAP_MAESTRO.md)

**Qué es:**
- Mapa maestro de todos los roadmaps del proyecto
- Sistema de 5 niveles de dependencia (Fundación → Futuro)
- Vista de Sprints 1-5 organizados
- Reglas claras: "No hacer roadmap X sin roadmap Z"

**Por qué importa:**
- Visibilidad completa de lo que viene
- Dependencias explícitas (evita bloqueos)
- Estimaciones de tiempo por sprint
- Métricas de progreso trackables

**Contenido:**
```
✅ Diagrama visual de dependencias (Mermaid)
✅ Tabla de features por nivel de prioridad
✅ Sprints sugeridos del 1 al 5
✅ Reglas de dependencias
✅ Métricas de progreso
```

---

### 2️⃣ **banco_de_preguntas.md** 💡 KNOWLEDGE BASE

**Ubicación:** [`doc/Desarrollos/banco_de_preguntas.md`](file:///d:/EPPSaaS/ep-back/doc/Desarrollos/banco_de_preguntas.md)

**Qué es:**
- 6 preguntas técnicas clave respondidas con detalle
- Análisis de viabilidad de tecnologías
- Roadmaps sugeridos por área

**Preguntas cubiertas:**
1. ¿Por qué dos librerías de PDF? (DomPDF vs Spatie)
2. ¿Cómo mejorar firmas digitales?
3. ¿Blockchain, tokens e IA son viables?
4. ¿Cómo hacer sistema offline para técnicos?
5. ¿App móvil o PWA web?
6. ¿Cómo planificar performance y estabilidad?

**Por qué importa:**
- Decisiones técnicas documentadas
- ROI estimado de cada tecnología
- Priorización basada en beneficio real

---

### 3️⃣ **nuevos_requerimientos.md** 📋 INBOX DE FEATURES

**Ubicación:** [`doc/Desarrollos/nuevos_requerimientos.md`](file:///d:/EPPSaaS/ep-back/doc/Desarrollos/nuevos_requerimientos.md)

**Qué es:**
- Formato estándar para documentar nuevos features
- Se borra cuando se implementa (se genera doc final)

**Requerimientos actuales:**
1. **Rol Usuarios Empresariales** (Sprint 4)
   - Clientes pueden revisar trabajos de técnicos
   - Sistema de notificaciones bidireccional
   - Evitar sobrecarga de emails

2. **Rediseño del Sidebar** (Sprint 3)
   - Agrupación lógica de funciones
   - Más intuitivo y fácil de usar

3. **Generación de procedimientos**
   - Testing por etapas
   - Casos de uso reales (no seeders)

**Por qué importa:**
- PM documenta aquí lo que necesita
- IA analiza viabilidad técnica
- Se convierte en roadmap formal

---

### 4️⃣ **WORKFLOW.md** ⚙️ CÓMO TRABAJA EL EQUIPO

**Ubicación:** [`doc/WORKFLOW.md`](file:///d:/EPPSaaS/ep-back/doc/WORKFLOW.md)

**Qué es:**
- Workflow específico del equipo EP-Back con IA
- Roles y responsabilidades claros
- Ceremonias ágiles optimizadas

**Roles definidos:**
- **PM:** Define features, valida entregas
- **Scrum Master:** Actualiza roadmaps, gestiona sprints
- **Fullstack #1:** Features standard con asistencia IA
- **Fullstack #2:** Arquitectura, BD, deploys

**Mejoras con IA:**
- Sprint Planning: -70% tiempo (1.5h vs 4h)
- Duración Sprint: -33% (2-3 semanas vs 3-4)
- Código/dev/día: +250%

**Por qué importa:**
- PM entiende cómo funciona el equipo ahora
- Expectativas claras de tiempos
- Métricas comparativas con/sin IA

---

### 5️⃣ **AI_FRAMEWORK.md** 🤖 METODOLOGÍA REPLICABLE

**Ubicación:** [`doc/AI_FRAMEWORK.md`](file:///d:/EPPSaaS/ep-back/doc/AI_FRAMEWORK.md)

**Qué es:**
- Framework **universal** aplicable a cualquier proyecto
- No es específico de EP-Back
- Metodología completa de trabajo con IA

**Contenido:**
```
✅ Filosofía y principios
✅ Estructura de documentos universal
✅ Workflow de 4 fases (Discovery → Planning → Execution → Review)
✅ Plantillas de prompts reutilizables
✅ Métricas de éxito
✅ Checklist de implementación
```

**Por qué importa:**
- Replicable en otros proyectos del equipo
- Reduce curva de aprendizaje
- Mejores prácticas documentadas

---

## 📊 ROADMAPS INDIVIDUALES (Sprints)

### Sprint 1: Fundación (2-3 semanas) 🏗️

#### [`roadmap_performance.md`](file:///d:/EPPSaaS/ep-back/doc/Roadmap/roadmap_performance.md)
**Objetivo:** Sistema estable y rápido (prerequisito de todo)

**Fases:**
- Monitoreo (Laravel Pulse)
- Optimización BD (índices, eager loading)
- Caché estratégico
- Optimización de assets

**Criterio de éxito:**
- Tiempo de carga < 2s
- 0 queries N+1
- Disponibilidad > 99.5%

---

#### [`roadmap_firmas.md`](file:///d:/EPPSaaS/ep-back/doc/Roadmap/roadmap_firmas.md)
**Objetivo:** Firmas digitales legibles y de calidad

**Implementación inmediata:**
- SVG alta resolución (3x)
- Storage optimizado
- Incrustación en PDFs sin pérdida

**Futuro:**
- Firma digital certificada (PKCS#7)
- Validación legal

---

### Sprint 2: Optimización (3-4 semanas) ⚡

#### [`roadmap_pwa_offline.md`](file:///d:/EPPSaaS/ep-back/doc/Roadmap/roadmap_pwa_offline.md)
**Objetivo:** Técnicos trabajan sin internet en campo

**Prerequisito:** Performance al 80%

**Implementación:**
- Service Workers (caché)
- IndexedDB (almacenamiento local)
- Sincronización automática
- Manifest PWA

**Criterio de éxito:**
- 10+ inspecciones offline sin pérdida de datos

---

### Sprint 3: UX (1-2 semanas) 🎨

#### [`roadmap_sidebar.md`](file:///d:/EPPSaaS/ep-back/doc/Roadmap/roadmap_sidebar.md)
**Objetivo:** Navegación intuitiva

**Sin prerequisitos** (puede ejecutarse en paralelo)

**Features:**
- Agrupación lógica por categorías
- Grupos colapsables
- Sistema de favoritos
- Búsqueda integrada

**Mejora esperada:** 50% reducción en tiempo de búsqueda

---

### Sprint 4: UX Avanzado (3-4 semanas) 📱

#### [`roadmap_usuarios_empresariales.md`](file:///d:/EPPSaaS/ep-back/doc/Roadmap/roadmap_usuarios_empresariales.md)
**Objetivo:** Portal para clientes empresariales

**Prerequisito:** Performance + PWA (recomendado)

**Basado en:** Requerimiento #1 de `nuevos_requerimientos.md`

**Implementación:**
- Nuevo rol `usuario_empresarial`
- Pivot con empresas
- Dashboard cliente personalizado
- Sistema de vistos
- Notificaciones bidireccionales

---

### Sprint 5: Inteligencia (4-6 semanas) 🤖

#### [`roadmap_ia_automaciones.md`](file:///d:/EPPSaaS/ep-back/doc/Roadmap/roadmap_ia_automaciones.md)
**Objetivo:** IA práctica para autocompletar y analizar

**Prerequisito:** Performance + PWA + Data histórica (>100 inspecciones)

**Casos de uso:**
- Autocompletado de checklists (70% precisión)
- Análisis de imágenes (detectar anomalías)
- Generación de informes técnicos

**Presupuesto:** < $100 USD/mes

---

## 📈 MÉTRICAS Y PROGRESO

### Estado Actual (2025-12-31)

```
NIVEL 0 (Fundación):     [ ░░░░░░░░░░ ] 0%  ← Pendiente
NIVEL 1 (Optimización):  [ BLOQUEADO   ]    ← Requiere NIVEL 0 al 80%
NIVEL 2 (UX):            [ BLOQUEADO   ]    ← Requiere NIVEL 1
NIVEL 3 (IA):            [ BLOQUEADO   ]    ← Requiere NIVEL 1 + data
NIVEL 4 (Futuro):        [ BLOQUEADO   ]    ← Requiere NIVEL 2-3
```

### Sprints Planeados

| Sprint | Tema | Duración | Features | Estado |
|--------|------|----------|----------|--------|
| 1 | Fundación | 2-3 sem | Performance + Firmas | 🔴 Pendiente |
| 2 | PWA Offline | 3-4 sem | Sistema offline | ⚪ Bloqueado |
| 3 | Sidebar UX | 1-2 sem | Navegación mejorada | ⚪ Bloqueado |
| 4 | Usuarios Empresariales | 3-4 sem | Portal clientes | ⚪ Bloqueado |
| 5 | IA Fase 1 | 4-6 sem | Autocomplete + Análisis | ⚪ Bloqueado |

---

## 🎯 PRÓXIMOS PASOS (Para PM)

### Acción Inmediata

1. **Revisar ROADMAP_MAESTRO.md**
   - Validar niveles de prioridad
   - Aprobar o ajustar orden de sprints

2. **Validar Sprint 1**
   - ¿Performance + Firmas es correcto como fundación?
   - ¿2-3 semanas es aceptable?

3. **Priorizar requerimientos**
   - Confirmar que Usuarios Empresariales va en Sprint 4
   - Confirmar que Sidebar puede ir en Sprint 3 (paralelo)

### Esta Semana

1. **Sprint Planning** (aplicar nuevo workflow)
   - Pre-planning con Tech Lead + IA
   - Planning meeting reducido (1.5h)
   - Iniciar Sprint 1

2. **Comunicar al equipo**
   - Compartir `WORKFLOW.md`
   - Compartir `AI_FRAMEWORK.md`
   - Alinear expectativas

### Métricas a Trackear

Desde Sprint 1, monitorear:
- ✅ Velocity (story points/sprint)
- ✅ Time to market (días requerimiento → producción)
- ✅ Precisión de IA (% código usado sin cambios)
- ✅ Calidad (bugs post-deploy)
- ✅ Satisfacción del equipo (encuesta mensual)

---

## 💡 TIPS PARA USAR ESTA DOCUMENTACIÓN

### Como PM:

1. **Bookmark ROADMAP_MAESTRO.md**
   - Es tu fuente de verdad del proyecto
   - Actualizado semanalmente por Scrum

2. **Usa nuevos_requerimientos.md**
   - Documenta features nuevos ahí
   - IA analiza viabilidad técnica
   - Se convierte en roadmap formal

3. **Lee banco_de_preguntas.md**
   - Entiende decisiones técnicas
   - ROI de cada tecnología
   - Priorización informada

### Como Scrum Master:

1. **Actualiza roadmaps semanalmente**
   - Métricas de progreso
   - Estado de prerequisitos
   - Bloqueos

2. **Facilita retrospectivas con métricas IA**
   - Velocity con/sin IA
   - Precisión de IA
   - Acciones de mejora

### Como Developer:

1. **Consulta roadmap individual antes de empezar**
   - Entiende arquitectura propuesta
   - Prerequisitos técnicos
   - Criterios de completitud

2. **Documenta en banco_de_preguntas.md**
   - Preguntas técnicas no triviales
   - Decisiones de arquitectura
   - Learnings importantes

---

## 📞 CONTACTO Y FEEDBACK

**Responsable de documentación:** Scrum Master

**¿Encontraste algo desactualizado?**
- Levantar en daily/retrospectiva
- Abrir issue en repo
- Notificar al Scrum Master

**¿Necesitas nuevo roadmap?**
- Documentar en `nuevos_requerimientos.md`
- Consultar con Tech Lead + IA
- Sprint planning lo prioriza

---

## 🔄 CHANGELOG

| Fecha | Versión | Cambios |
|-------|---------|---------|
| 2025-12-31 | 1.0 | Estructura inicial de documentación |
| | | Sistema de roadmaps con dependencias |
| | | Framework universal AI-driven |
| | | Workflow del equipo definido |

---

> **"Documentación es código que no envejece. Mantenla actualizada y será tu mejor aliado."**
