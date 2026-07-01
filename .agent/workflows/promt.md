---
description:  La Solución SRE Recomendada: Opción B + C (Prevención desde la Raíz)
---

El principio máximo del SRE que aplicamos en XamaSafe es eliminar el error humano desde la raíz. Si las preguntas sueltas rompen el flujo, el Gestor no debería poder crearlas.
Sin embargo, para no complicar la experiencia de usuario (UX) del Gestor al crear checklists rápidos, te propongo este Enfoque Alternativo (Opción C):
1. Auto-Creación de Sección Base (Backend): No obligues al Gestor a crear una sección manualmente si tiene prisa. Modifica el controlador de creación del checklist para que, al momento de hacer el POST de un nuevo DynamicChecklist, el sistema cree automáticamente por detrás una primera ChecklistSection llamada "Inspección General" o "Datos Relevantes".
2. Restricción Estricta en el Constructor (Opción B en el Frontend): En Builder.jsx, elimina por completo el botón global de "Añadir Pregunta Suelta". Los botones de "Añadir Pregunta" solo deben existir dentro de la tarjeta de cada Sección. Así, la UI guía al usuario obligándolo a inyectar la pregunta en una sección existente.
3. Hardening del FormRequest (Backend): En Laravel, actualiza el Request de guardado de preguntas para que el campo section_id pase a ser estrictamente obligatorio a nivel de validación, independientemente de que tu base de datos actual permita nulos en la tabla dynamic_questions
.
'section_id' => 'required|exists:checklist_sections,id',
💡 Beneficios de este enfoque para XamaSafe:
El Técnico (Wizard.jsx): Mantiene una experiencia táctica impecable y fluida, navegando de forma estructurada paso a paso sin secciones "fantasma".
El Gestor (Builder.jsx): Sigue pudiendo crear checklists rápidamente gracias a que el sistema le auto-genera la sección "Inspección General" al iniciar.
El Motor de Datos: El controlador InspectionController@wizard ya no necesita esa sub-consulta extraña para buscar preguntas donde whereNull('section_id'), haciendo la consulta de base de datos más rápida y limpia mediante un simple with('sections.questions')
.