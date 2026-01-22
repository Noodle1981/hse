# Documento de Requisitos del Producto (PRD) - Sistema de Gestión XamaSafe

## 1. Introducción
XamaSafe es una plataforma integral SaaS para la gestión de servicios de Seguridad e Higiene Laboral. El sistema permite a consultoras y profesionales gestionar visitas técnicas, auditorías, capacitaciones y el cumplimiento normativo de múltiples empresas y sedes.

Este documento define los requisitos para la **Generación de Informes**, **Constancias de Visita**, **Matrices de Riesgo** y **Planificación de Contratos**.

---

## 2. Visión General del Flujo de Trabajo
El núcleo operativo del sistema gira en torno a la "Visita Técnica".
1.  **Planificación**: Se define un contrato anual con el cliente que estipula la frecuencia de visitas y capacitaciones.
2.  **Ejecución**: El técnico realiza la visita e interactúa con la App para:
    *   Ejecutar Checklists (Dinámicos).
    *   Dictar Capacitaciones (con registro de asistencia).
    *   Registrar Hallazgos (Desviaciones) e Incidencias.
    *   Entregar Elementos de Protección Personal (EPP).
3.  **Documentación**: Al finalizar, se genera automáticamente una **Constancia de Visita** (Informe).
4.  **Trazabilidad**: Los hallazgos se vinculan con la **Matriz de Riesgos** de la empresa para asegurar que se gestionen los peligros identificados.
5.  **Seguimiento**: La visita consumada actualiza el progreso del **Contrato Anual**.

---

## 3. Módulos y Requisitos Funcionales

### 3.1. Generación de Informes y Constancias de Visita

El sistema debe generar automáticamente un informe detallado en formato PDF al finalizar una visita. Este documento sirve como constancia legal y operativa del servicio prestado.

**Componentes del Informe:**
*   **Encabezado:** Datos de la Consultora, Cliente, Sede, Técnico, Fecha y Hora (Inicio/Fin).
*   **Resumen Ejecutivo:** Breve descripción de las actividades realizadas.
*   **Detalle de Actividades:**
    *   **Checklists Ejecutados:** Listado de checklists completados, con sus resultados (Cumple/No Cumple/No Aplica) y observaciones.
    *   **Capacitaciones Dictadas:** Título de la capacitación, duración, temas tratados y listado de asistentes (con firma digital si aplica).
    *   **Entrega de EPP:** Registro de empleados que recibieron equipamiento, detallando los ítems.
*   **Gestión de Desviaciones:**
    *   **Hallazgos:** Lista de condiciones inseguras o actos inseguros detectados. Debe incluir fotos (evidencia) y nivel de riesgo.
    *   **Incidencias:** Reporte de accidentes o eventos significativos ocurridos durante la visita o reportados por el cliente.
*   **Firmas:** Espacio para firma digital del Técnico y del Responsable de la Sede.

**Requisitos Técnicos:**
*   Generación asíncrona de PDF.
*   Envío automático por correo electrónico al contacto de la empresa tras la firma.
*   Almacenamiento histórico de informes.

### 3.2. Matrices de Riesgos y Trazabilidad

Este módulo permite gestionar la identificación de peligros y evaluación de riesgos por puesto de trabajo o área, integrándose con la operatividad diaria para mantener la evaluación viva ("Live Document").

**Estructura de Datos:**
*   **Actividad / Puesto:** (Ej. Soldadura, Trabajo en Altura).
*   **Peligro:** Fuente de daño potencial.
*   **Riesgo:** Probabilidad x Severidad = Nivel de Riesgo (Bajo, Medio, Alto).
*   **Medidas de Control:** Acciones requeridas para mitigar el riesgo.

**Requisitos de Trazabilidad:**
*   **Vinculación de Hallazgos:** Al registrar un "Hallazgo" durante una visita, el técnico debe poder vincularlo a un riesgo específico de la Matriz de Riesgos de esa sede.
    *   *Ejemplo:* Si se detecta "Falta de arnés", se vincula al Riesgo "Caída a distinto nivel" en la Matriz.
    *   Esto permite generar estadísticas de qué riesgos se materializan con más frecuencia.
*   **Actualización Dinámica:** La recurrencia de hallazgos sobre un mismo riesgo debe alertar para revisar la evaluación de riesgo (re-evaluación).

### 3.3. Planificación de Contratos Anuales

Módulo para la gestión comercial y operativa de los servicios comprometidos con cada cliente a lo largo del año.

**Funcionalidades:**
*   **Definición del Contrato:**
    *   Fecha Inicio / Fin.
    *   Cantidad de Visitas Mensuales/Anuales contratadas.
    *   Cantidad de Capacitaciones Anuales contratadas.
    *   Horas profesionales asignadas.
*   **Cronograma de Servicio:**
    *   Calendario visual donde se planifican las visitas futuras.
    *   Asignación de técnicos a las visitas planificadas.
*   **Seguimiento de Ejecución (KPIs):**
    *   **Cumplimiento de Visitas:** (Visitas Ejecutadas / Visitas Planificadas) %.
    *   **Cumplimiento de Capacitación:** (Capacitaciones Dadas / Comprometidas) %.
    *   Al finalizar una visita (generación de Constancia), el sistema debe descontar/marcar como "Ejecutada" la actividad en el plan anual automáticamente.
*   **Alertas:** Notificaciones automáticas cuando un contrato está por debajo del cumplimiento esperado o próximo a vencer.

---

## 4. Requisitos No Funcionales
*   **Multitenancy:** Estricta separación de datos entre diferentes clientes (Empresas) y tenant de la consultora.
*   **Offline First (Deseable para App Técnico):** Capacidad de realizar la visita (checklist, hallazgos) sin internet y sincronizar al recuperar conexión.
*   **Seguridad:** Encriptación de datos sensibles y firmas.
*   **Escalabilidad:** Soporte para gran volumen de fotos en informes.

## 5. Glosario
*   **Hallazgo:** Desviación notada durante una inspección que requiere acción correctiva.
*   **Incidencia:** Evento no planificado que resultó o pudo resultar en daño.
*   **Constancia:** Documento final que certifica la intervención del profesional.
