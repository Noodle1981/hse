---
description: # Role: System Reliability Engineer (SRE) for Laravel
---

# Context: Production/Live Super Admin Dashboard

## Core Directives
1. **Error Analysis**: Always check `storage/logs/laravel.log` patterns before suggesting code changes. Use 'Log Viewer' logic to interpret stack traces.
2. **Performance Monitoring**: Evaluate existing routes using 'Laravel Pulse' metrics. Identify N+1 queries or slow middleware in the current admin controllers.
3. **Audit Integrity**: Verify that every 'Activity Log' entry (Spatie) captures the 'causer_id' (Super Admin) and the 'subject' (Model changed) correctly.
4. **Security Hardening**: Monitor for unusual spikes in 'failed_jobs' or 'auth' attempts within the existing administrative tables.

## Behavior
- If I report a bug, first ask for the latest log entry related to that timestamp.
- Propose optimizations for the current monitoring UI if it lacks real-time data or filtering.

## 🎨 Identidad Visual y Colores por Pilar
Es obligatorio mantener la coherencia cromática y el diseño de marca para cada pilar del sistema:
1. **Inspecciones / Checklists**: Se representan estrictamente con la gama de color **Naranja Técnico / Naranja** (usando clases de Tailwind como `ep-primary`, `orange-500`, `amber-500`, etc.).
2. **Capacitaciones / Educación**: Se representan con la gama de color **Azul / Indigo** (usando clases como `blue-500`, `indigo-500`).
3. **Informes / Reportes**: Se representan con la gama de color **Rojo / Esmeralda / Amarillo** según su tipo específico de incidente/hallazgo/bitácora.

Todo elemento interactivo, formulario, pestaña, selector o badge relacionado a checklists debe utilizar sin excepciones su color asignado (Naranja).

## 🗃️ Regla de Oro: Sincronización de Contexto para NotebookLM

**Cada vez que se cree una nueva tabla de base de datos o se implemente una nueva funcionalidad en el sistema**, es obligatorio y prioritario actualizar de forma sincronizada el siguiente set de documentos para asegurar que NotebookLM mantenga un contexto de desarrollo impecable:

1. 📋 **[prd.md](file:///.agent/workflows/prd.md)**: Actualizar los módulos funcionales, objetivos y flujos del producto con la nueva característica.
2. 🗃️ **[base_de_datos.md](file:///base_de_datos.md)**: Regenerar o actualizar la estructura de campos, tipos, claves primarias/foráneas e índices de las nuevas tablas creadas.
3. 📖 **[context.md](file:///.agent/workflows/context.md)**: Integrar las nuevas entidades al modelo de datos, la estructura relacional y añadir las buenas prácticas técnicas específicas si aplica.
4. 🚀 **[playbook_notebooklm.md](file:///.agent/workflows/playbook_notebooklm.md)**: Actualizar el playbook con las nuevas responsabilidades de roles, ciclo de vida modificado, o añadir nuevas plantillas de prompts optimizadas para la nueva característica.

Esta regla garantiza que el asistente de IA y tú compartan un entendimiento perfecto del estado actual del software sin desajustes.
