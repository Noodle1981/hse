---
description: Motor de Mantenimiento Autónomo, Asignación Automática y Bloqueo Operativo (Interlock)
---

Contexto del Sistema: Estamos desarrollando XamaSafe (Laravel 12 + React 19 + Inertia.js + Tailwind v4). Arquitectura basada en un "Módulo Universal de Activos" donde la tabla assets guarda su información en un campo JSON metadata según el metadata_schema definido en asset_types.
Objetivo: Implementar un motor de alertas de mantenimiento múltiple que, al dispararse, asigne automáticamente un checklist de reparación al técnico, y mientras no se resuelva, bloquee operativamente el uso del activo en los checklists diarios.
Por favor, implementa los siguientes 4 pasos en orden:
PASO 1: Frontend - Form Builder del AssetType (Múltiples Reglas y Nexo de Resolución) En el componente que construye el esquema del activo (resources/js/Pages/Gestor/AssetTypes/Create.jsx o Edit.jsx), modifica la lógica para campos numéricos y de fecha:
Agrega la capacidad de crear un arreglo de maintenance_rules.
Cada regla debe tener: nombre (ej. "Cambio de Aceite"), frecuencia (ej. 15000), margen_pre_alerta (ej. 1000), y NUEVO: un menú desplegable llamado checklist_resolucion_id que liste los dynamic_checklists activos.
PASO 2: Backend - Motor de Alertas Automáticas (CheckAssetAlerts Job) Modifica el comando app/Console/Commands/CheckAssetAlerts.php:
Al iterar los activos, lee sus maintenance_rules.
Pre-Alerta: Si la métrica actual alcanza la regla menos el margen_pre_alerta, cambia status a 'alerta'.
Estado Crítico: Si supera la métrica, cambia status a 'fuera_de_servicio'.
Si entra en estado crítico y no existe un hallazgo previo para esta regla, haz dos cosas:
Crea el registro en hallazgos (Mantenimiento Requerido).
NUEVO: Crea un registro en checklist_assignments asignando el checklist_resolucion_id (leído de la regla) a la sede_id del activo, con status = 'pending', para que le aparezca al técnico en su consola.
PASO 3: Backend & Frontend - Bloqueo Operativo en Terreno (El Candado SRE) Modifica InspectionController@wizard y el Paso 0 de resources/js/Pages/Tecnico/Inspections/Wizard.jsx:
Cuando el técnico seleccione un activo en el Paso 0 (por buscador o QR), verifica su status.
Si status === 'fuera_de_servicio', el backend debe rechazar la selección y el frontend debe desplegar una alerta táctica roja: "ACCESO DENEGADO: El equipo se encuentra Fuera de Servicio por Mantenimiento Vencido".
Excepción: El bloqueo se ignora ÚNICAMENTE si el dynamic_checklist_id que el técnico está intentando ejecutar coincide con el checklist_resolucion_id asignado para arreglar ese activo.
PASO 4: Backend - Resolución Automática y Liberación (SyncAssetMetadata) Modifica el listener app/Listeners/SyncAssetMetadata.php:
Si el checklist completado actualiza una métrica en estado crítico y resetea el ciclo (ej. ingresa el nuevo kilometraje de service):
Actualiza el valor en el JSON metadata del activo y calcula el próximo vencimiento.
Busca en la tabla hallazgos el ticket abierto para este activo y cámbialo a 'resuelto'.
NUEVO: Busca la asignación pendiente en checklist_assignments y márcala como completada.
Devuelve el status de la tabla assets a 'operativo' (liberando así el candado del Paso 3).