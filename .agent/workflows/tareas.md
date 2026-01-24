---
description: El contenido de este archivo es para realizar tareas
---

Actúa como un experto desarrollador de Laravel y Blade especializado en generación de reportes PDF técnicos con DomPDF.
Tengo un archivo Blade que genera una 'Constancia de Visita'. Actualmente, el documento funciona solo como un resumen o bitácora: menciona qué se hizo (título de la actividad y hora), pero no muestra el contenido real de lo que se trabajó.
Mi objetivo: Quiero transformar este documento en un Reporte Maestro Integral. El cliente que recibe este PDF debe ver, en el mismo archivo, el detalle completo de cada acción realizada para que sirva como respaldo legal y de auditoría.
Instrucciones de transformación:
Checklists/Inspecciones: No solo muestres el título. Debes incluir una tabla por cada checklist realizado que muestre: Pregunta, Respuesta (SI/NO/NA o valor) y la Observación específica si existe.
Capacitaciones: Expande esta sección. Por cada capacitación, muestra el tema, la duración, el instructor y una sub-tabla con el listado de asistentes (Nombre, DNI y si firmó).
Hallazgos e Incidentes: Debes mostrar la ficha completa. Esto incluye: Título, descripción extendida, sector exacto, nivel de criticidad (con un color visual), plazo de corrección y, lo más importante, las fotos de evidencia de cada hallazgo en un tamaño visible (2 por fila).
Entregas de EPP: Detalla qué elementos se entregaron a cada empleado (cantidad y descripción del ítem).
Estructura y Diseño:
Utiliza page-break-inside: avoid; en CSS para que ninguna sección (como un hallazgo o una firma) se corte a la mitad entre dos páginas.
Mantén la estética corporativa (colores naranja #F69239 y gris oscuro #1E1E2D).
Usa tablas limpias con anchos del 100% y jerarquía visual (labels en gris/pequeño y valores en negro/negrita).
Contexto técnico: Las relaciones en mi modelo $visit ya traen los datos ($visit->checklistCompletions, $visit->trainings, $visit->hallazgos, etc.). Por favor, genera el código Blade completo, mejorando el CSS y estructurando los @foreach para que desplieguen toda la información detallada."