---
description: Mejoras de textos de casos de usos
---

te voy algo bastante complejo, la idea principal es darle un contexto más serio a esto, en el sentido de que es un demo, los registros que tengo son  datos muy poco serio, quiero que cada registro y este bien seria y profesionalla informacion, digamos en todas las observaciones. ect. no se si empezar con hallazgos, incidentes, capacitaciones, luegos visitas, aclarando que no todos tienen el mismo tipo de checklist y no todos tienen hallazgos y demas, hacer todo serio concluyendo el informes, la fotos las voy a generar depende de lo que completes. ¿Debatamos por favor? hize una copia a la base de datos por las dudas

---

## 📅 TAREA PENDIENTE (Siguiente Sesión): Reparar Visualización del Contrato Anual

**Problema:**
El panel de "Contrato Anual" (`App\Livewire\Contracts\AnnualContractDashboard.php`) **no muestra las esferas en Verde (Cumplimiento)** para Capacitaciones e Informes, a pesar de que los registros existen en la base de datos (Ej: Capacitaciones en Marzo 2026).

**Causa Detectada:**
La query del panel filtra por `sede_id`. Es altamente probable que la Sede seleccionada en el selector del Dashboard no coincida exactamente con el `sede_id` de las capacitaciones cargadas, o que falte vincular la "Fila del Plan" con la actividad específica en la tabla `annual_plans`.

**Pasos para el IA en la próxima sesión:**
1. Leer `AnnualContractDashboard` y el método `calculateComplianceData`.
2. Inspeccionar la tabla `annual_plans` corriendo:
   `App\Models\AnnualPlan::where('sede_id', 1)->get()`
3. Comparar contra `App\Models\Training::where('sede_id', 1)->get()`.
4. Si las filas del Plan no tienen el `checklist_id` o el `activity_name` mapeado idéntico, corregir la inyección de datos para que el Dashboard pinte los checks verdes ✅ automáticamente. Explicar al usuario si falta crear "Planificaciones" para esa Sede.