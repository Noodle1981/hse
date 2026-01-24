---
description: Arquitectura y Desarrollo del Módulo "Informe Mensual Maestro"
---

Contexto del Sistema:
Estoy desarrollando una plataforma de gestión de Seguridad e Higiene (HyS) en Laravel. Ya cuento con un sistema de 'Constancias de Visita' técnica. Ahora necesito crear el Módulo de Informes Mensuales, que funciona como un consolidado de alto nivel para el cliente.
Objetivo del Módulo:
Permitir que un Gestor solicite informes y que los Técnicos los generen, consolidando datos históricos de visitas, matrices de riesgo y cumplimiento de planes anuales.
Instrucciones Detalladas:
1. Lógica de Negocio y Flujo de Trabajo (Workflow):
Rol Gestor: Debe tener un dashboard para visualizar todos los informes generados por la empresa. Debe poder "Solicitar Informe" a un técnico específico, lo cual creará una "Tarea" en el panel del técnico.
Rol Técnico: Debe recibir solicitudes de informes como tareas pendientes. También debe poder generar informes de forma proactiva. Debe tener un historial personal de informes emitidos.
Estado del Informe: Definir estados como: Pendiente, En Redacción, Enviado al Cliente y Archivado.
2. Estructura del Generador de Informe (UI/UX):
El formulario de creación debe ser modular y permitir:
Carga de Datos Generales: Título, periodo (mes/año), objetivo del informe.
Importación Dinámica: Una función para seleccionar 'Constancias de Visita' anteriores del mismo cliente y "traer" sus observaciones relevantes al informe actual.
Sección de Capacitación: Un checkbox para incluir o no el resumen de capacitaciones del mes.
Matriz de Riesgo: Una sección específica donde se visualice la Matriz de Riesgo actual de la sede, incluyendo una zona de "Observaciones de la Matriz" para que el técnico explique cambios en los niveles de riesgo.
Plan de Contrato Anual: Un componente de "Seguimiento de Plan" que muestre el listado de tareas pactadas anualmente vs. el porcentaje de cumplimiento real a la fecha del informe.
3. El Documento PDF (Resultado Final):
El PDF debe seguir la misma línea estética que la 'Constancia de Visita' (limpio, corporativo, colores naranja #F69239 y gris oscuro #1E1E2D), pero con secciones extendidas:
Portada Profesional: Logo grande, nombre de la empresa cliente, nombre del técnico y periodo.
Cuerpo del Informe:
Resumen ejecutivo de gestión.
Tabla comparativa de observaciones (Anteriores vs. Actuales).
Visualización de la Matriz de Riesgo: Usar una tabla de calor o colores para representar la probabilidad e impacto.
Gráfico/Tabla de Cumplimiento: Mostrar el estatus del Plan Anual (Tareas realizadas, pendientes y programadas).
Firmas: Espacio para firma del técnico y sello de la consultora.
4. Requerimientos Técnicos:**
Base de Datos: Diseñar las relaciones necesarias (Un Informe pertenece a una Sede, puede tener muchas Observaciones Importadas, se vincula a un Plan Anual).
Frontend: Uso de componentes Blade o Livewire para la selección dinámica de visitas anteriores.
Backend: Lógica para calcular el cumplimiento del Plan Anual comparando las tareas del contrato contra las actividades registradas en las constancias del mes.
Por favor, genera:
Una propuesta de las tablas de migración de base de datos necesarias.
La estructura del controlador para gestionar las solicitudes (Tareas).
El diseño visual (HTML/CSS) del componente de la Matriz de Riesgo y el Plan Anual para el PDF."información detallada."