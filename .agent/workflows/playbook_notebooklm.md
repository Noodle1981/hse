---
description: Guía maestra y playbook operativo de XamaSafe para alimentar NotebookLM y optimizar el flujo de trabajo con IA
---

# 🚀 PLAYBOOK MAESTRO: Flujos, Roles y Arquitectura Operativa (XamaSafe)

Este documento ha sido diseñado específicamente para ser alimentado a **NotebookLM** y otros modelos de lenguaje. Describe en detalle el ciclo de vida operativo, los roles de usuario, la arquitectura de datos, y proporciona plantillas de prompts optimizadas para ahorrar tokens y maximizar la precisión en el desarrollo.

---

## 1. 👥 MODELO DE ROLES: Capacidad, Responsabilidades y Límites

El sistema opera bajo un esquema estricto de multi-tenancy y control de accesos basado en tres perfiles clave:

```
                  ┌──────────────────────────────┐
                  │      ADMIN / GESTOR          │
                  │   Consola de Mando / PDFs    │
                  └──────────────┬───────────────┘
                                 │ Controla a
                  ┌──────────────▼───────────────┐
                  │         TÉCNICO              │
                  │   Móvil / Captura en Campo   │
                  └──────────────┬───────────────┘
                                 │ Inspecciona a
                  ┌──────────────▼───────────────┐
                  │      CLIENTE CORPORATIVO     │
                  │    Auditoría / Dashboard     │
                  └──────────────────────────────┘
```

### 1.1. Gestor (Administrador / Consultor Técnico de Higiene y Seguridad)
* **Objetivo:** Diseñar el plan estratégico de seguridad y auditorías, y consolidar la información en informes consolidados profesionales para el cliente final.
* **Responsabilidades:**
  * Crear y dar de alta Empresas (`Empresa`) y Sedes físicas (`Sede`).
  * Diseñar y estructurar plantillas de checklists dinámicos (`DynamicChecklist`, `ChecklistSection`, `DynamicQuestion`).
  * Asignar checklists a sedes y técnicos específicos para su ejecución en fechas límites (`ChecklistAssignment`).
  * Configurar los planes de contratación anual (`AnnualPlan`) para definir qué tareas deben cumplirse cada mes.
  * Auditar desvíos complejos, incidentes y planes de acción correctiva en campo.
  * Consolidar visitas, capacitaciones e inspecciones en **Reportes Maestros** (`MasterReport`) y generar los informes consolidados en PDF.
* **Páginas React / Inertia asociadas:** 
  * Directorio `resources/js/Pages/Gestor/*` (Dashboard, MasterReports, Inspections, Visits, Companies, Checklist administration, Annual Plan).

### 1.2. Técnico (Inspector de Campo / Operador de Terreno)
* **Objetivo:** Ejecutar las inspecciones físicas, constatar condiciones de seguridad, dictar capacitaciones y certificar su presencia y visitas en las instalaciones del cliente.
* **Responsabilidades:**
  * Iniciar y finalizar visitas en terreno (`VisitCertificate`), registrando su ingreso y egreso con verificación GPS obligatoria (Proof of Presence).
  * Completar los checklists e inspecciones asignadas u auto-asignadas (`VisitChecklistCompletion`), registrando las firmas e imágenes correspondientes.
  * Registrar hallazgos críticos de Higiene y Seguridad (desvíos) adjuntando fotos de evidencia en tiempo real.
  * Registrar incidentes de seguridad que requieran atención inmediata.
  * Impartir capacitaciones en planta, agregando participantes y tomando la firma electrónica en pantalla mediante el panel táctil (`signature_pad` integrado en React).
* **Páginas React / Inertia asociadas:**
  * Directorio `resources/js/Pages/Tecnico/*` (MyAssignments, Visits/InProgress, Visits/History).

### 1.3. Cliente Corporativo (Lector / Supervisor de Sede)
* **Objetivo:** Supervisar el nivel de cumplimiento de higiene y seguridad de sus plantas y coordinar la resolución de los hallazgos reportados.
* **Responsabilidades:**
  * Consultar el estado operativo y KPIs de sus sedes en tiempo real.
  * Acceder al histórico de constancias de visitas firmadas por los técnicos de campo.
  * Visualizar hallazgos abiertos y registrar evidencias de resolución (planes de acción correctivos).
  * Monitorear el progreso y la asistencia de su personal a las capacitaciones.
* **Límite Técnico:** Restringido bajo lógica de multi-tenancy. Solo visualiza información relacionada a sus propias Sedes y Empresas (definido mediante políticas de Laravel).

---

## 2. 🔄 EL CICLO DE VIDA OPERATIVO: Flujo de Principio a Fin

El flujo operativo comercial y técnico de XamaSafe sigue un ciclo continuo e integral:

```
  ┌──────────────┐      ┌─────────────────┐      ┌──────────────────┐
  │ 1. Alta de   │ ───> │  2. Diseño de   │ ───> │ 3. Planificación │
  │  Maestros    │      │    Checklists   │      │    Anual (PPD)   │
  └──────────────┘      └─────────────────┘      └────────┬─────────┘
                                                          │
  ┌──────────────┐      ┌─────────────────┐               │
  │ 6. Reporte   │ <─── │   5. Cierre y   │ <─────────────┘
  │    Maestro   │      │   Firma Visita  │ (Inicio de Visita GPS en Campo)
  └──────────────┘      └─────────────────┘
```

1. **Paso 1: Configuración de Maestros (Gestor):**
   Se da de alta una **Empresa** y sus **Sedes** físicas. Cada sede se configura obligatoriamente con su latitud y longitud geográfica, y un radio de tolerancia en metros (usualmente 500m) para las verificaciones de seguridad GPS.

2. **Paso 2: Diseño de Checklists Dinámicos (Gestor):**
   El Gestor crea una plantilla de control (`DynamicChecklist`) con un código normativo único (`registry_code`). Añade secciones y preguntas dinámicas, definiendo límites numéricos o valores que disparan una condición de falla de forma automática.

3. **Paso 3: Planificación Comercial (El Plan de Contrato Anual):**
   El Gestor crea el `AnnualPlan` de la sede, marcando qué checklists y capacitaciones deben dictarse obligatoriamente mes a mes (ej: Inspección de Tanques en Enero, Febrero y Julio; Capacitación en Espacios Confinados en Enero).

4. **Paso 4: El Despliegue en Campo (Técnico):**
   El Técnico ingresa a la aplicación desde su dispositivo móvil. Al llegar a la planta del cliente, presiona **Iniciar Visita**. El sistema solicita permisos de geolocalización, calcula la distancia mediante la fórmula de Haversine y abre una visita activa (`VisitCertificate` en estado `in_progress`).

5. **Paso 5: Auditoría y Ejecución en Planta (Técnico):**
   * **Inspecciones:** El Técnico completa en la interfaz del HUD Táctico los checklists que corresponden al mes (sugeridos de forma automática por el Plan Anual) o se auto-asigna checklists ad-hoc.
   * **Hallazgos:** Si encuentra una falla (ej: corrosión en un tanque), registra el hallazgo, define la criticidad y sube una fotografía.
   * **Capacitaciones:** Se dicta la capacitación a los empleados presentes. Se registran sus datos y firman en la pantalla de la tablet del técnico.
   * **Cierre de Visita (Check-out):** Al terminar la jornada en planta, el técnico pulsa "Finalizar Visita". El responsable de la Sede física del cliente firma en pantalla, se calcula la distancia GPS de salida, y la visita cambia a estado `signed`, enviándose de forma automática por email en formato PDF.

6. **Paso 6: Consolidación y Reporte Maestro (Gestor):**
   A fin de mes (o trimestre), el Gestor ingresa al Wizard de **Reportes Maestros**. El sistema consolida automáticamente toda la actividad ejecutada en el período. El motor de cálculos compara lo completado contra el Plan Anual, calcula el porcentaje de cumplimiento y lista los hallazgos y desvíos detectados. El Gestor redacta las conclusiones, firma, y el sistema genera el informe consolidado formal de Higiene y Seguridad del cliente en un PDF de alta definición optimizado para impresión física.

---

## 3. 🛠️ CÓMO SE ARMAN Y CONFIGURAN LAS ENTIDADES CLAVE

### 3.1. Cómo se crea un nuevo Checklist Dinámico y sus Reglas de Falla
Un checklist dinámico sigue la jerarquía relacional de base de datos:
* **`DynamicChecklist`:** Cabecera (Título, descripción, código de registro normativo).
* **`ChecklistSection`:** Subdivisiones lógicas del formulario (ej: "Estado Estructural", "Válvulas").
* **`DynamicQuestion`:** Cada pregunta individual. 
  * Admite tipos: `text`, `textarea`, `select`, `radio`, `checkbox`, `number`, `date`, `signature`.
  * **Reglas de Falla:**
    * * failure_values:* Colección JSON de respuestas que constituyen una no-conformidad (ej: `["Sí"]` o `["Malo", "Regular"]`).
    * * failure_logic:* 
      * `fail_if_selected`: Falla si el técnico marca cualquiera de los valores de falla.
      * `fail_if_missing`: Utilizado en multiselección (checkboxes). Falla si la respuesta carece de los elementos obligatorios.
    * * Rangos Numéricos:* Si el tipo es `number`, se configuran campos `min_value` y `max_value`. Si el técnico digita un valor fuera de este rango, el sistema calcula de inmediato que existe una falla.

### 3.2. Cómo se configura el Plan de Contrato Anual (`AnnualPlan`)
* **Finalidad:** Garantizar y demostrar la cobertura comercial y legal del servicio de higiene y seguridad acordado con el cliente.
* **Funcionamiento:** En lugar de crear citas o tareas fijas e inamovibles, se asocian de forma flexible plantillas de checklists (`dynamic_checklist_id`) o temas de capacitación a una Sede para meses específicos del año.
* **Estructura del Plan:**
  * `sede_id`: Sede sobre la que aplica la cobertura.
  * `category`: Categorías `Inspección` (Checklists), `Capacitación` o `Informe`.
  * `scheduled_months`: Array JSON con los períodos planificados en formato Año-Mes (ej: `["2026-01", "2026-02", "2026-07"]`).
* **Sincronización:** Cuando un técnico inicia una visita presencial en el mes de Enero, el dashboard le sugiere automáticamente realizar las tareas planificadas en el Plan Anual para `2026-01`. Si la ejecuta, el motor del Reporte Maestro la mapea como completada exitosamente, sumándola al porcentaje de eficiencia del contrato.

### 3.3. Cómo se Audita la Jornada Laboral y el Roster Minero 24/7
* **Finalidad:** Controlar el tiempo presencial (Horas de Campo) vs. de oficina (Horas de Gabinete) y auditar desvíos horarios para cumplir con la ERL.
* **Operación Roster 24/7 (Rubro Minero):**
  * Para operaciones continuas de alta montaña o faenas mineras, se habilita `es_roster_24_7 = true`.
  * La lógica de validación del `TimeTrackingService` aprueba automáticamente cualquier check-in sin importar días o rangos de horas, ya que se trabaja bajo modalidad continua incluyendo feriados y domingos.
* **Operación Ordinaria Standard:**
  * Si `es_roster_24_7 = false`, el sistema cruza la marca del check-in (`start_time`) verificando que el día de la semana pertenezca a `dias_operativos` (almacenado como array JSON) y que el horario se encuentre entre `hora_apertura` y `hora_cierre`.
  * **Cálculo de Sobrejornada:** El Dashboard calcula dinámicamente si las horas efectivas de campo superan la duración parametrizada para la Sede en ese día, disparando alertas tácticas interactivas parpadeantes ante sobretiempos.
* **Preparación Proof of Presence (GPS):**
  * La base relacional de `visit_certificates` y `sedes` incluye campos de geolocalización (`checkin_lat`, `checkin_lng`, `checkout_lat`, `checkout_lng` y `radio_tolerancia_metros`) listos para cruzarse en el backend aplicando la **Fórmula matemática de Haversine** para calcular distancias terrestres exactas en metros.

### 3.4. La Consola de Mando de Técnico por Pilares y la Bóveda de Certificados Agrupada
* **Consola Dinámica en 3 Pilares (`MyAssignments.jsx`):**
  * Supera la interfaz plana de tareas organizando las actividades en tres pestañas: **Misiones (Inspecciones)**, **Educación (Capacitaciones)** y **Métricas (Informes)**.
  * Los widgets de KPIs en la cabecera mutan en tiempo real para reflejar métricas del pilar seleccionado.
  * Dispone de botones de acción contextuales unificados (`[+] Inspeccionar`, `[+] Iniciar Capacitación`, `[+] Ejecutar Informe`) ubicados estratégicamente antes de la grilla de tareas para abrir de inmediato modales inteligentes de inicio de sesión planificada y libre (ad-hoc).
  * Reemplaza el campo de "Estado" lineal por la celda avanzada **"Checklists"** (en la pestaña de Misiones) que detalla de forma interactiva el ratio de éxitos (`Exitosos: X/Y` con advertencias visuales `⚠️` y color de alerta roja si hay no-conformidades en los controles) y el porcentaje de cumplimiento normativo resaltado con sombras de neón (`glow-text`).
  * Incorpora un buscador por texto escalable y filtros de Categoría y Subcategoría en cascada para checklists libres.

* **Bóveda de Certificados Agrupada por Fecha y Sede (`History.jsx`):**
  * **Agrupación Inteligente:** En lugar de mostrar un listado plano de cada checklist individual (que satura el historial ante múltiples inspecciones), el servidor agrupa las inspecciones completadas (`DynamicInspection::completed()`) por **Fecha (`completed_date`)** y **Sede (`sede_id`)**.
  * **Fila Centralizada de Jornada:** Muestra la fecha del día, la sede y empresa asociadas, KPIs resumidos del día (checklists realizados, ratio de éxitos y porcentaje de cumplimiento normativo), y el botón para descargar la **Constancia de Visita Principal** (`VisitCertificate`) firmada por el jefe del cliente. Si la visita no se ha cerrado o firmado aún, se renderiza un botón deshabilitado `"Sin Constancia"` en tono gris con advertencia estética.
  * **Sub-Acordeón Detallado:** Al pulsar `Ver Checklists (X)`, se despliega suavemente un subpanel con animación fluida que lista cada check individual de la jornada:
    * Nombre y código de norma del checklist.
    * Hora exacta de finalización y **nombre del supervisor firmante (`supervisor_name`)**.
    * Badge semafórico del estado: **Pasó** (Verde Esmeralda) o **Falló ⚠️** (Rojo Alerta Neón pulsante).
    * Botón de descarga de PDF individual.
  * **Paginación Robusta:** El controlador backend (`InspectionController.php`) realiza una paginación manual por grupos vía `LengthAwarePaginator`, garantizando la correcta compatibilidad de los filtros interactivos.

### 3.5. Sistema de Roles Ultra-Robusto (Fix Definitivo)
* **Propósito:** Prevenir errores recurrentes de autorización (403 Acceso Denegado) provocados por disparidades en la especificación de nombres de roles (plurales, acentos, separadores por coma o barra) entre rutas, políticas y vistas.
* **Funcionamiento Unificado:**
  * Toda la lógica de parseo reside en `User::parseRoles(string|array $roles)` de forma recursiva.
  * Resuelve automáticamente strings planos (`'Técnico'`), delimitados por comas (`'tecnico,gestor'`), delimitados por pipes (`'tecnico|gestor'`), arrays y arrays anidados (`['gestor', 'tecnico,Socio']`).
  * Normaliza de forma bidireccional los nombres legados (`Administrador`, `socio`, `Técnico`, `tecnicos`) a sus equivalentes lógicos internos (`gestor` o `tecnico`), haciendo que el motor sea a prueba de fallos en producción.
  * El middleware `CheckRole` se simplifica delegando toda la matriz de variádicos directamente al modelo `User::hasRole()`.

### 3.6. Enlace de Modelos en Rutas (Route Model Binding)
* **Propósito:** Evitar la inyección accidental de instancias de modelo vacías (con `id: null`) al frontend, lo que causa fallas críticas en los generadores de rutas de Javascript (como Ziggy JS).
* **Operación:**
  * Laravel realiza la inyección automática cruzando el nombre del comodín de la ruta con la variable tipada en el controlador.
  * **Regla Estricta:** Si el método en el controlador tipa un modelo (ej. `public function show(DynamicInspection $inspection)`), el comodín de la ruta en `routes/web.php` **debe llamarse exactamente `{inspection}`** y no `{id}`.
  * Cambiar de `{id}` a `{inspection}` o `{finding}` garantiza que la base de datos se consulte en tiempo de resolución de la ruta, retornando datos 100% consistentes y previniendo crashes en la renderización React.

---

## 4. 💡 PLAYBOOK DE PROMPTS: Optimización para Desarrollar con IA (NotebookLM)

Utiliza estas estructuras de prompts cuando trabajes en NotebookLM o con asistentes integrados en el IDE para optimizar el consumo de tokens y asegurar la calidad del código:

### 4.1. Prompt para Refactorización de Componentes React (Inertia + Tailwind v4)
> **Contexto:** Estoy desarrollando en la rama `feature-inertia-react` en un stack Laravel 12 + Inertia (React 19) + Tailwind CSS v4.
> **Objetivo:** Refactorizar/crear la interfaz de `[Especificar Vista, ej: Tecnico/Visits/InProgress.jsx]`.
> **Directrices de Diseño (HUD Premium Dark):**
> - Utiliza el color de fondo raíz `#11111D` y paneles en `#1E1E2D` (`bg-ep-dark`).
> - Los botones deben usar la estructura "Tactical Pill" con efectos hover dinámicos y brillo suave (`shadow-lg shadow-ep-primary/20`).
> - Emplea variables de CSS variables custom (`--color-ep-primary` que corresponde al color de marca `#F69239`).
> - Integra transiciones y micro-animaciones HUD suaves (`transition-all duration-300 hover:scale-105`).
> - Asegúrate de que las firmas digitalizadas utilicen el filtro de inversión de color (`brightness-0 invert opacity-90`) para una inmersión completa en la visual oscura sin dañar la impresión de informes.
> - Evita la redundancia de código y modulariza las secciones en subcomponentes funcionales limpios.
> **Tarea:** Genera el componente React para `[Describir Funcionalidad]`.

### 4.2. Prompt para Lógica del Backend (Laravel 12 Controllers & Services)
> **Contexto:** Estamos en el backend de XamaSafe en Laravel 12 con PHP 8.2+.
> **Objetivo:** Crear/Modificar lógica de negocio relacionada con `[Especificar, ej: la Fórmula de Haversine para geolocalización o validación de checklists]`.
> **Directrices del Código Backend:**
> - Aplica estrictamente Type Hints en parámetros y Return Types en todos los métodos.
> - Separa las validaciones complejas de peticiones utilizando clases de validación dedicadas (`FormRequests`).
> - Previene problemas de rendimiento cargando previamente las relaciones requeridas (`Eager Loading` / `with()`) para mitigar consultas N+1.
> - Retorna respuestas nativas utilizando el adaptador de Inertia: `return Inertia::render('Path/To/Component', $data);`.
> - Preserva el aislamiento de inquilinos (multi-tenancy) verificando la autorización con políticas (`this->authorize()`).
> **Tarea:** Escribe el código PHP limpio y modular para `[Describir tarea técnica]`.
