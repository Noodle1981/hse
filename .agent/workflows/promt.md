---
description: Lógica de Estados en Grilla de Informes Maestros
---

Contexto: Estamos en la rama feature-inertia-react del proyecto XamaSafe (Laravel 12 + React 19 + Tailwind v4). Actuando como System Reliability Engineer (SRE) y Frontend Developer. Ubicación: Componente MyAssignments.jsx (o la tabla correspondiente en la pestaña tab=reports del técnico).
Objetivo: Implementar un renderizado condicional de botones/enlaces en la columna de acción de la tabla de informes mensuales, mapeando 5 estados visuales a la máquina de estados de la tabla master_reports. Se debe asegurar que solo se gestione un informe por mes (periodo).
Lógica de Renderizado Condicional a Implementar (Iterando sobre los planes del mes):
Supongamos que la variable report contiene el informe maestro del mes actual para esa sede (si report es nulo, significa que aún no se ha creado).
Estado "Ejecutar" (Creación):
Condición: !report (No existe informe para este periodo).
UI: Botón "Tactical Pill" [+] EJECUTAR. Color base, resplandor primario (bg-ep-primary). Dispara la creación del informe (garantizando 1 solo por mes).
Estado "Reanudar" (Borrador Técnico):
Condición: report.estado === 'en_redaccion' && !report.observacion_revision.
UI: Botón "Tactical Pill" REANUDAR. Color gris/índigo sutil (bg-indigo-500/20 text-indigo-300). Enlaza al Wizard de edición.
Estado "En Revisión" (Devuelto por Gestor):
Condición: report.estado === 'en_redaccion' && report.observacion_revision !== null.
UI: Botón o Badge con Glow Rojo Alerta (bg-red-500/20 text-red-400 border border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.3)] animate-pulse). Texto: ⚠️ CORREGIR (REVISIÓN). Enlaza al Wizard para que el técnico lea las correcciones.
Estado "Pendiente" (En Evaluación del Gestor):
Condición: report.estado === 'finalizado'.
UI: Badge de solo lectura estilo Glassmorphism. Texto: ⏳ PENDIENTE GESTOR. Color amarillo/naranja sutil. Sin enlace de edición.
Estado "Aprobado" (Cierre Final):
Condición: report.estado === 'aprobado'.
UI: Enlace HTML (<a> o <Link>) apuntando a /master-reports/${report.id} (o la ruta global /master-reports según corresponda). Debe tener diseño de éxito Premium Dark: text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/10 transition-all duration-300 rounded-xl px-4 py-2. Texto: VER INFORME / PDF.
Directrices de Diseño (Premium Dark HUD):
Asegúrate de que el selector o contenedor de esta tabla (bg-[#1a1a2e]/40 backdrop-blur-3xl rounded-[3rem]...) mantenga su integridad.
Evita recargas completas de página; usa el componente <Link> de Inertia.js para las navegaciones internas y router.post() para la acción de "Ejecutar".

--------------------------------------------------------------------------------
Con esto, te aseguras de que el Técnico nunca pueda crear un "segundo" informe para junio, y que el ciclo de revisiones de idas y vueltas entre él y el Gestor quede perfectamente orquestado mediante colores que le dicen exactamente qué tiene que hacer.