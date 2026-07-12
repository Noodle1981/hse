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
  * Auditar desvíos complejos, incidentes y planes de acción correctiva en campo, siendo el único autorizado para transicionar hallazgos a los estados de cierre definitivo (`cerrado`) o verificación final (`verificado`).
  * Consolidar visitas, capacitaciones e inspecciones en **Reportes Maestros** (`MasterReport`) y generar los informes consolidados en PDF.
  * Auditar, observar, devolver y aprobar los Informes Maestros redactados por los técnicos a su cargo, redactando observaciones de corrección (`observacion_revision`).
* **Páginas React / Inertia asociadas:** 
  * Directorio `resources/js/Pages/Gestor/*` (Dashboard, MasterReports, Inspections, Visits, Companies, Checklist administration, Annual Plan).

### 1.2. Técnico (Inspector de Campo / Operador de Terreno)
* **Objetivo:** Ejecutar las inspecciones físicas, constatar condiciones de seguridad, dictar capacitaciones y certificar su presencia y visitas en las instalaciones del cliente.
* **Responsabilidades:**
  * Iniciar y finalizar visitas en terreno (`VisitCertificate`), registrando su ingreso y egreso con verificación GPS obligatoria (Proof of Presence).
  * Completar los checklists e inspecciones asignadas u auto-asignadas (`VisitChecklistCompletion`), registrando las firmas e imágenes correspondientes.
  * Registrar hallazgos críticos de Higiene y Seguridad (desvíos) adjuntando fotos de evidencia en tiempo real, con la capacidad de cambiar su estado hasta `resuelto` (pero restringido de cerrarlos o verificarlos).
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

### 1.4. Súper Administrador (Administrador Global del Sistema)
* **Objetivo:** Administrar con control total y privilegios globales absolutos el SaaS de XamaSafe, la infraestructura de consultoras, la gestión del staff administrativo global, y la eliminación de cuentas.
* **Responsabilidades:**
  - Crear, editar, listar y eliminar consultoras de Higiene y Seguridad (`Consultora`).
  - Configurar límites de licencias de técnicos (`max_tecnicos`), gestores (`max_gestores`) y empresas (`max_empresas`) por consultora.
  - Personalizar el branding corporativo de cada consultora (logotipo y colores de acento primario, secundario y de relieve) mediante la interfaz gráfica en React.
  - Administrar el staff de operadores y técnicos de cada consultora, pudiendo cambiar su estado a `'inactivo'` para deshabilitar su acceso en tiempo real sin eliminar el registro físico (preservando la trazabilidad de inspecciones e informes pasados).
  - Gestionar el personal administrativo global (Súper Administradores y Vendedores), incluyendo la creación de nuevas cuentas, la deshabilitación de accesos mediante cambio de estado a `'inactivo'`, y el blanqueo de contraseñas de forma segura utilizando modales de confirmación React tácticos.
* **Páginas React / Inertia asociadas:**
  - Directorio `resources/js/Pages/Admin/Consultants/*` (Index, Edit, Users).
  - Directorio `resources/js/Pages/Admin/Staff/*` (Index.jsx - Gestión de Personal Global).

### 1.5. Vendedor (Soporte Comercial Global)
* **Objetivo:** Administrar y dar soporte comercial a las consultoras en el onboarding y licenciamiento, con privilegios administrativos globales restringidos.
* **Responsabilidades:**
  - Crear, editar y listar consultoras de Higiene y Seguridad (`Consultora`).
  - Configurar límites de licencias (`max_tecnicos`, `max_gestores`, `max_empresas`) y gestionar el estado de suscripción de cada consultora (`estado_suscripcion` a `activa`, `morosa` o `baja`).
  - Personalizar el branding corporativo de cada consultora (logotipo y colores corporativos).
  - **Restricciones de seguridad:** no puede eliminar consultoras, no tiene acceso a la gestión de personal de consultoras (usuarios gestores/técnicos) ni a la gestión global de staff (recibe `403 Forbidden` en el backend y los elementos se ocultan visualmente en el sidebar/vistas).
* **Páginas React / Inertia asociadas:**
  - Directorio `resources/js/Pages/Admin/Consultants/*` (Index, Edit).

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

6. **Paso 6: Consolidación y Reporte Maestro (Técnico + Gestor):**
   El Técnico inicia el Wizard de **Reportes Maestros** desde la pestaña "Métricas" en `MyAssignments.jsx`. El motor compara la data con el `AnnualPlan`, calcula el cumplimiento, y agrupa desvíos/capacitaciones.
   - **Máquina de 5 Estados (Backend-Driven):**
     El campo `accion` lo calcula el backend (`OperationController.php`) buscando el `MasterReport` por `periodo` (YYYY-MM) + `sede_id` + `user_id`, y lo transmite al frontend. El frontend `MyAssignments.jsx` solo renderiza en base a este campo:
     1. **`ejecutar`** → No existe informe para ese periodo. Botón amarillo "+ EJECUTAR".
     2. **`reanudar`** → `estado = 'en_redaccion'`, sin observación. Enlace amarillo sutil "REANUDAR" al Wizard.
     3. **`corregir`** → `estado = 'en_redaccion'` + `observacion_revision` no nulo. Enlace rojo pulsante "⚠ CORREGIR".
     4. **`pendiente`** → `estado = 'finalizado'`. Badge amarillo solo-lectura "⏳ PENDIENTE GESTOR".
     5. **`aprobado`** → `estado = 'aprobado'`. Enlace emerald "✅ VER INFORME" hacia `GET /master-reports/{id}`.
   - **Regla de Unicidad:** Un técnico solo puede tener un informe por `periodo` (YYYY-MM) y `sede`. El backend previene la creación de duplicados.
   - **El Ciclo de Revisión y Aprobación (Gestor)**:
     1. El Técnico edita el borrador (`'en_redaccion'`) y lo cierra como `'finalizado'` desde el Wizard.
     2. El informe se bloquea para el Técnico. La **Sección 6: Evaluación del Gestor** se habilita en `master-reports/{id}/edit`.
     3. El Gestor puede **Aprobar** (estado → `'aprobado'`, se limpia `observacion_revision`) o **Observar/Devolver** (se guarda observación y estado → `'en_redaccion'`).
     4. Si es devuelto, el Técnico ve una tarjeta Glassmorphic roja al inicio del Wizard con el feedback del Gestor.
     5. Si es aprobado, queda archivado en solo-lectura para todos. La `MasterReports/Index.jsx` muestra solo el botón "PDF FINAL" para el gestor y "DESCARGAR PDF APROBADO" para el técnico.

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
  * **Opciones de Capacitación Incorporadas:**
    * `duration_minutes`: Duración planificada de la charla (tiempos parametrizados: 5m, 10m, 15m, 30m, 45m, 1h, 1:30h, 2h, 3h).
    * `modality`: Tipo de capacitación ("presencial", "virtual", "hibrido").
    * `multi_trainer`: Switch booleano para programar la presencia de 1 o más capacitadores en campo.
* **Sincronización:** Cuando un técnico inicia una visita presencial en el mes de Enero, el dashboard le sugiere automáticamente realizar las tareas planificadas en el Plan Anual para `2026-01`. Si la ejecuta, el motor del Reporte Maestro la mapea como completada exitosamente, sumándola al porcentaje de eficiencia del contrato.
* **Herencia en Sesiones y Cierre Simplificado:** Al iniciar la sesión en vivo desde la visita, el sistema busca el plan programado y preconfigura automáticamente la duración y la modalidad. El cierre de sesión prescinde de firmas adicionales de instructores, valiéndose únicamente de la firma única del técnico auditor y la lectura del cronómetro en vivo para certificar la duración real.
* **Acordeón Táctico y Listados Fluidos de Alta Escala:** Para evitar la fatiga visual ante grupos masivos (30+ asistentes), la grilla de capacitaciones finalizadas del Gestor implementa un sub-acordeón con un listado limpio tipo tabla (Nombre, Puesto, DNI) en lugar de un grid pesado de firmas digitalizadas.
* **PDFs de Alta Fidelidad en Caliente:** El controlador de exportación PDF (`TrainingPDFController`) detecta si la solicitud proviene de una ejecución real (`VisitTraining`), cruzando dinámicamente las firmas e inputs capturados durante la visita presencial en lugar de leer la plantilla base.

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
  * **Capacitaciones Ad-hoc Unificadas (`MyAssignments.jsx`):** En el modal de inicio rápido, la pestaña de Capacitaciones Ad-hoc cuenta con un buscador reactivo de texto y filtros avanzados de Categoría y Subcategoría (idénticos a los de Inspecciones pero con la paleta de color Índigo Técnico). Al presionar "Iniciar", la tarjeta del checklist se despliega de forma interactiva inline (Inline Accordion) permitiendo configurar la modalidad ("In situ (Presencial)" vs. "Online (Virtual)") mediante botones segmentados con neón índigo, duración estimada y descripción de objetivos. Al presionar "Confirmar & Iniciar", se inyecta la capacitación con ese título y la modalidad seleccionada se guarda directamente en la columna `type` de la tabla `trainings` para consistencia.

* **Bóveda de Certificados Agrupada por Fecha y Sede (`History.jsx`):**
  * **Agrupación Inteligente:** En lugar de mostrar un listado plano de cada checklist individual (que satura el historial ante múltiples inspecciones), el servidor agrupa las inspecciones completadas (`DynamicInspection::completed()`) por **Fecha (`completed_date`)** y **Sede (`sede_id`)**.
  * **Fila Centralizada de Jornada:** Muestra la fecha del día, la sede y empresa asociadas, KPIs resumidos del día (checklists realizados, ratio de éxitos y porcentaje de cumplimiento normativo), y el botón para descargar la **Constancia de Visita Principal** (`VisitCertificate`) firmada por el jefe del cliente. Si la visita no se ha cerrado o firmado aún, se renderiza un botón deshabilitado `"Sin Constancia"` en tono gris con advertencia estética.
  * **Sub-Acordeón Detallado:** Al pulsar `Ver Checklists (X)`, se despliega suavemente un subpanel con animación fluida que lista cada check individual de la jornada:
    * Nombre y código de norma del checklist.
    * Hora exacta de finalización y **nombre del supervisor firmante (`supervisor_name`)**.
    * Badge semafórico del estado: **Pasó** (Verde Esmeralda) o **Falló ⚠️** (Rojo Alerta Neón pulsante).
    * Botón de descarga de PDF individual.
  * **Paginación Robustecida:** El controlador backend (`InspectionController.php`) realiza una paginación manual por grupos vía `LengthAwarePaginator`, garantizando la correcta compatibilidad de los filtros interactivos.

  - **Persistencia Ligera:** En la base de datos (`visit_certificates.instructor_signature` o `users.firma`), solo se almacena la ruta relativa (ej. `signatures/instructor_xxxx.png`). Esto previene el crecimiento desmedido de las filas de texto largo (`TEXT`/`LONGTEXT`) y mantiene las consultas optimizadas.
* **Mecanismo Operativo (Frontend):**
  - La finalización de la charla en `Session.jsx` expone un switch táctil glowing (`bg-orange-500 shadow-[0_0_15px_rgba(246,146,57,0.3)]`) para "¿Instructor Externo / Especialista?".
  - Al activarse, se despliega un panel translúcido de Glassmorphism con un input obligatorio para el nombre (`trainer_name`) y un lienzo de `SignaturePad` con trazos color naranja técnico inyectados mediante React, con filtro inverso `brightness-0 invert opacity-90` para legibilidad.
* **Mecanismo de Guardado Físico (Backend):**
  - La firma no se persiste en formato Base64 en la base de datos para evitar "bloat". El controlador decodifica la cadena, genera un archivo físico `.png` y lo almacena en `Storage::disk('public')->put('signatures/...', $data)`.
  - La base de datos (`visit_certificates.instructor_signature`) solo guarda la ruta relativa al archivo, y el campo `visit_certificates.instructor_signature_date` almacena el timestamp de la firma.
  - Si aplica, se actualiza `trainings.trainer_name`.

### 3.8. Blindaje de Fechas e Inmutabilidad (SRE Security & HUD Sync Badge)
* **Propósito:** Prevenir el fraude de horarios o alteración malintencionada de marcas de tiempo en la finalización de checklists mediante el bloqueo absoluto de entradas de tiempo desde el cliente.
* **Funcionamiento Backend:**
  - Se implementa el FormRequest `StoreChecklistCompletionRequest` para sanitizar las peticiones de completado, eliminando del payload campos de tiempo como `completed_at`, `date`, `time` o `timestamp` mediante el método `validatedWithSafeTime()`.
  - Si el request contiene cualquiera de estos campos manipulados, `InspectionController` dispara una advertencia detallada `[SRE SECURITY AUDIT]` en `laravel.log` que recopila la IP de origen, usuario/técnico, ID de visita y los valores de tiempo descartados, persistiendo estrictamente la fecha con `now()` del servidor.
* **Funcionamiento Frontend:**
  - En `Wizard.jsx`, se despliega un badge centrado y glowing en Naranja Técnico justo encima del progress bar: `"⌚ Tiempo Real (Sync Servidor)"` (con animación `animate-pulse` en el reloj), certificando visualmente la sincronización del tiempo con el servidor.

### 3.9. Política de Firma Pendiente y Flujo de Aprobación Táctica (Auditoría de Firmas)
* **Propósito:** Garantizar que los técnicos no puedan actualizar su firma oficial inmediatamente sin que un Gestor audite visualmente su validez, evitando falsificaciones o firmas inválidas.
* **Tipología de Firmas en el Ecosistema XamaSafe:**
  * **Firma Tipo A (Identidad / Perfil Permanente):**
    * Firma oficial e inmutable cargada en el perfil del usuario.
    * Convertida de Base64 a archivo físico `.png` en el storage de Laravel para evitar saturación de la base de datos.
    * Entra en estado `'pending'` (`firma_status`) y se guarda en la columna `firma_temp` y ruta física temporal.
    * Requiere la aprobación interactiva de un Gestor mediante la consola `PendingSignaturesList.jsx`.
    * **DomPDF Resolution Constraint:** Para su correcta impresión en certificados PDF, el servidor traduce la ruta relativa de la base de datos a una ruta física absoluta mediante `public_path('storage/' . $firma)`, evitando el renderizado del clásico cuadro cruzado roto de DomPDF.
  * **Firma Tipo B (Firma Transaccional / De Visita o Planilla):**
    * Persistidas directamente en formato Base64 o estructuras serializadas dentro de las tablas correspondientes (`training_attendances.signature`, `visit_certificates.responsible_signature`, etc.), garantizando ligereza e inmediatez sin necesidad de procesos de auditoría del perfil.

* **Flujo Operativo de Aprobación (Tipo A):**
  1. **Captura Temporal:** El Técnico actualiza su firma en `/perfil` mediante un canvas React. El backend decodifica el Base64 y lo almacena como archivo `.png` en un directorio temporal (`storage/app/public/signatures/temp_uid.png`), guardando la ruta en `firma_temp` y marcando `firma_status = 'pending'`. El perfil del técnico queda bloqueado con indicador "Aprobación Pendiente".
  2. **Auditoría de Gestor:** El Componente `PendingSignaturesList.jsx` se inyecta en la cabecera de la matriz de usuarios (`Users.jsx`) y lee las firmas en estado pendiente. Muestra la firma invertida (`brightness-0 invert opacity-90`) sobre fondo oscuro premium.
  3. **Resolución:**
     * **Aprobar:** Copia físicamente la firma temporal a una ruta permanente aleatoria única para mantener el historial forense inmutable, actualiza `users.firma` con esta ruta, limpia `firma_temp` y setea `firma_status = 'approved'`.
     * **Rechazar:** Elimina físicamente el archivo temporal en el storage y marca `firma_status = 'rejected'`, habilitando al técnico a reintentar y mostrando una advertencia de rechazo en su perfil.
  4. **Visor de Firmas Oficiales:** Se habilita un botón de "ojito" en cada fila del usuario en la matriz de identidades para que el Gestor previsualice con un modal de alta gama Glassmorphic la firma activa y aprobada actual.

### 3.11. Módulo Universal de Activos y Trazabilidad Dinámica (Asset Engine)
* **Propósito:** Ofrecer un inventario técnico unificado (Matafuegos, Redes de Agua, Autoelevadores, Vehículos) con metadata dinámica por rubro, expediente fotográfico por ranuras, galería de evidencias y alertas automatizadas.
* **Arquitectura:**
  - **`AssetType` (Esquema de Metadata y Fotos):** Los Gestores configuran esquemas JSON de metadatos (`metadata_schema`) y fotografías requeridas (`photo_schema`), especificando atributos (`label`, `type`, `show_in_create`, `role`, `alert_days`, `alert_threshold`, `photo_schema.*.key`, `photo_schema.*.required`). Los formularios en React (`Create.jsx` / `Edit.jsx` / `Types/Index.jsx`) dibujan los inputs de forma automatizada (*Schema-Driven*).
    - **Taxonomía de Roles de Metadata:** Cada atributo dinámico posee un rol (`role`) que regula el comportamiento de pre-llenado en el Wizard:
      1. `identificador` (Alta, ej: N° Interno) → Pre-completa en Wizard.
      2. `dato_estatico` (Trazabilidad estática, ej: Tipo de Agente) → Pre-completa en Wizard.
      3. `dato_con_regla` (Fechas/Métricas SRE, ej: Próxima Carga) → Pre-completa en Wizard.
      4. `verificacion` (Chequeo en campo, ej: manómetro en zona verde) → No se pre-completa (inicia vacío).
    - **Configuración Dinámica:** El configurador de tipos (`Types/Index.jsx`) incluye un selector de rol con badges de color. Si el rol no está especificado en datos heredados o importados de checklists, se auto-infiere en caliente en base a heurísticas del label.
  - **`Asset` (Inventario, Multi-Alta & UUIDs):** Asignación de claves primarias **UUID (v4)** (`HasUuids`) para mitigar vulnerabilidades SRE (anti-IDOR y prevención de enumeración de recursos). Soporta alta rápida multi-activo en 1 paso (`Create.jsx`) con auto-incremento inteligente de identificadores y validación reactiva anti-duplicados en frontend y backend (`distinct`, `unique:assets,identifier`).
  - **Expediente Fotográfico y Galería de Evidencias (`Show.jsx`):** Pestañas para gestión de fotos fijas base por ranura en `storage/app/public/assets/{id}/`, timeline de evidencias unificando inspecciones con fallas e incidentes (`incidentes.asset_id`), y visor Lightbox en pantalla completa.
  - **Informe PDF A4 (`asset-report.blade.php`):** Exportación ejecutiva de la ficha técnica en formato A4 estándar con marca de agua, Código QR, mosaico fotográfico e historial de inspecciones.
  - **Wizard Paso 0 (Trazabilidad Táctica):** Antes de responder el checklist, el técnico debe seleccionar en el Paso 0 el activo objeto de inspección (filtrando por nombre o QR/Patente), asociando `dynamic_inspections.asset_id`.
    - **Badge Dinámico de Cabecera:** Muestra dinámicamente el activo vinculado al vuelo durante el llenado.
    - **Advertencia de Re-inspección (Modal Ámbar):** Si el técnico intenta seleccionar un activo que ya fue auditado en la visita actual (estado `✓ Auditado`), el Wizard Paso 0 detiene la selección inmediata y despliega un modal de confirmación táctico premium en color ámbar/dorado para advertir sobre la duplicación legal del reporte.
  - **Bomba de Estado, Log de Cambios y Resolución SRE (`SyncAssetMetadata`):** Mapea respuestas del checklist marcadas en `dynamic_questions.vincular_a_metadata` directamente a la ficha del activo.
    * **Log Auditor Inmutable:** Compara el valor actual con el nuevo valor. Si hay un cambio, guarda el registro auditor inmutable en la tabla `asset_metadata_logs` (valor anterior, nuevo valor, técnico, inspección y fecha) para trazabilidad a nivel de campo.
    * Si la inspección resulta fallida (`Fail`), transiciona automáticamente el estado del activo a **"Fuera de Servicio"** (`fuera_de_servicio`).
    * **Resolución y Próxima Meta:** Si el checklist completado coincide con el checklist de resolución parametrizado para una regla SRE, actualiza la próxima meta del atributo (`proximo_val = valor_actual + frecuencia`), marca como completada la asignación técnica correctiva en `checklist_assignments` y auto-resuelve el hallazgo SRE abierto.
  - **Audit Trail Inmutable (`AssetObserver` / `AssetMetadataLog`):** Todos los cambios de estado y metadata se persisten de forma inmutable en `audit_logs` (nivel de activo completo) y `asset_metadata_logs` (nivel de campo individual).
  - **Engine de Alertas y Mantenimiento Autónomo (`CheckAssetAlerts`):** Proceso programado (`php artisan assets:check-alerts`) que evalúa vencimientos de fechas (`alert_days`) y umbrales numéricos (`alert_threshold`) en las reglas simples y en las **múltiples reglas SRE de ciclo infinito** configuradas en la ficha técnica.
    * **Pre-Alerta:** Si una métrica o fecha entra dentro del `margen_pre_alerta`, el activo pasa al estado `alerta` (amarillo).
    * **Crítico (Lockout):** Si el valor supera la frecuencia de servicio, el activo pasa a `fuera_de_servicio` (rojo). Se genera automáticamente un `Hallazgo` crítico y se **auto-asigna una orden de trabajo correctiva (`ChecklistAssignment`)** asociada al activo físico mediante la clave foránea `asset_id` con `user_id = null` (compartida por sede). Todos los técnicos asignados a la sede visualizan la tarea en su consola; el primero que inicia la ejecución en el Wizard provoca la auto-asignación dinámica a su usuario (`user_id = Auth::id()`) y transiciona el estado a `in_progress`, ocultándola de los demás técnicos.
    * **Hover Tooltip de Detalles en Index:** Al pasar el mouse sobre el estado "Alerta" del activo en la tabla general del gestor, se despliega hacia abajo (`top-full mt-2`) un tooltip oscuro premium detallando cada campo que causó la alerta (ej: "Vencimiento Carga" o "Prueba Hidráulica") con la fecha exacta y días restantes, ocultando campos técnicos redundantes.
    * **Baja Lógica y Preservación Histórica (Soft Deletes):** El borrado de activos realiza una baja lógica (`SoftDeletes`) renombrándose en la UI a "Dar de Baja". Para garantizar que el historial legal de inspecciones e incidentes pasados de un activo de baja no quede huérfano, las relaciones en `DynamicInspection` e `Incidente` cargan los activos incluyendo registros de baja (`->withTrashed()`).
  - **Bloqueo Operativo (Interlock):** 
    * Si el activo se encuentra en estado `fuera_de_servicio` por un mantenimiento vencido, el backend (`InspectionController.php`) bloquea cualquier intento de guardar o completar un checklist regular sobre él.
    * En el frontend (`Wizard.jsx`), el técnico ve denegada la selección de dicho activo mediante un banner rojo de alerta crítica en el Paso 0, a menos que esté ejecutando su checklist de resolución asignado para destrabarlo.

### 3.10. Validación Táctica de Informes Maestros y Hardening Backend (SRE Audit)
* **Propósito:** Prevenir la manipulación maliciosa de reportes consolidados (inyección de identificadores) y asegurar la autenticidad del documento sin depender de firmas manuales canvas.
* **Operación de Firmas Inmutables:**
  - Se remueve en absoluto el signature canvas manual en la sección 5 del Wizard de Redacción de Informes Maestros (`Wizard.jsx`).
  - El sistema extrae la firma digital del perfil autorizada y aprobada del técnico asignado (`report.tecnico.firma`). 
  - **HUD de Validación:** Si existe la firma de perfil aprobada, se muestra inmutable y con filtro invertido (`brightness-0 invert opacity-90`) con un glow verde táctico. Si no existe, se bloquea por completo la finalización del reporte y se provee un botón directo hacia su Perfil.
* **Mecanismos de Hardening (Seguridad SRE) y Flujo de Aprobación**:
  - **Route Model Binding (RMB) Semántico:** Las rutas web en Laravel se declaran como `{masterReport}` coincidiendo con el modelo inyectado en `MasterReportController`. Esto evita que peticiones vacías o manipuladas pasen la validación.
  - **Autorización por Políticas:** Todo endpoint (`edit`, `update`, `preview`) ejecuta `$this->authorize('view/update', $masterReport)` validando que pertenezca al autor o consultora dueña de la sede, garantizando aislamiento multi-tenancy absoluto.
  - **Sanitización por FormRequests:** Se inyectan `StoreMasterReportRequest` y `UpdateMasterReportRequest`. Los campos críticos (`estado`, `user_id`, `sede_id`) se sanitizan en backend y se descartan del payload utilizando `safeData()` y `validatedWithSafeTime()`.
  - **Acciones Transitorias y Flujo de Aprobación**:
    - Parámetros dinámicos y volátiles como `finalize` (para pasar a `'finalizado'`) y `action` (`'approve'` o `'observe'`) se consumen en la lógica del controlador para orquestar la máquina de estados, pero se purgan en `safeData()` antes de invocar `$masterReport->update()`, evitando errores SQL por columnas inexistentes.
    - La acción de aprobación (`'approve'`) actualiza el estado a `'aprobado'` y limpia la columna `observacion_revision`.
    - La acción de devolución (`'observe'`) actualiza el estado a `'en_redaccion'` y persiste la retroalimentación en `observacion_revision` para que el técnico la visualice en una tarjeta Glassmorphic roja al inicio de su Wizard en React.
* **Optimización y Eager Loading en Exportación PDF:**
  - El controlador ejecuta **Eager Loading** (`trainings.attendances`) al compilar los datos para la plantilla Blade, reduciendo a cero el problema N+1 de consultas en base de datos.
  - El PDF final lista cada charla del periodo, incorporando en la columna de registro de firmas la lista completa de asistentes reales normalizados en base de datos con su Nombre, DNI y firma transaccional real.
* **Filtro Estricto de Falsos Ad-Hoc:**
  - Las repeticiones o múltiples ejecuciones de checklists/capacitaciones programadas en el `AnnualPlan` para el periodo actual no deben listarse como "Ad-Hoc / Fuera de Plan".
  - La lógica en `aggregatePeriodData` descarta del listado Ad-Hoc cualquier actividad que coincida en código, ID de plantilla o título con lo planificado. Solo se reportan las actividades completamente espontáneas e imprevistas como "Inspecciones Técnicas No Rutinarias".

### 3.11. Gestión Global, Roles Globales y Auditoría de Accesos
* **Propósito:** Separar responsabilidades comerciales y globales de administración, y garantizar trazabilidad inalterable de los cambios en los límites de licenciamiento y estados de suscripción de los clientes.
* **Operación de Roles Globales:**
  - El sistema cuenta con dos roles a nivel global: `super_admin` (privilegios totales de infraestructura) y `vendedor` (privilegios de ventas y soporte comercial, con restricciones de eliminación y gestión de personal global).
  - Los endpoints de CRUD de Staff Global (`admin/staff/*`) y eliminación de consultoras se restringen estrictamente mediante middleware al rol `super_admin`.
* **Registro de Auditoría (Audit Log) Inmutable:**
  - **Tabla `audit_logs`:** registra el diff de límites y suscripciones modificadas en cada consultora. Guarda el `user_id` de quien modificó, `action` ('create' / 'update'), `target_type`, `target_id` y `payload` (JSON con el diff de cambios `old` y `new`).
  - **Inmutabilidad del Log:** El modelo `AuditLog` intercepta los eventos Eloquent `updating` y `deleting` para arrojar una `Exception`, bloqueando cualquier modificación o borrado de auditoría en la base de datos.
  - **Tabla `consultora_suscripcion_logs`:** registra las transiciones históricas de estados de suscripción (`estado_anterior`, `estado_nuevo`) para auditoría comercial rápida.
* **Visualización de Auditoría en Frontend:**
  - La vista `Edit.jsx` (Consultoras) presenta una pestaña de **"Auditoría"** que renderiza una línea de tiempo táctica de cambios de accesos, mostrando de forma descriptiva el diff visual (ej. *Licencias Técnicos: 10 → 15*) y el nombre del usuario global autor.
* **Pruebas de Trazabilidad:**
  - La suite de pruebas en `GlobalStaffManagementTest.php` valida el aislamiento de rutas de staff global/vendedores, la inmutabilidad de la tabla `audit_logs` y la generación automática de los logs ante inserciones/actualizaciones en consultoras.

### 3.13. Configurador de Tipos de Checklist Dinámicos (`Types/Index.jsx`)
* **Concepto Clave:** El `AssetType` es un **unificador de atributos de ficha técnica**, no un tipo físico clásico. En la UI se denomina "Tipo de Checklist Dinámico" y permite crear la ficha técnica de un activo asociando las preguntas de un checklist operativo existente.
* **Flujo Operativo:**
  1. El gestor crea un nuevo tipo ("Nuevo tipo de Checklist Dinámico").
  2. Selecciona Categoría → Subcategoría → Checklist específico en cascada.
  3. Presiona **"Configurar Atributos"**: el sistema consulta `/gestor/asset-types/import-checklist/{id}` y trae las preguntas con `vincular_a_metadata` del checklist.
  4. Los atributos importados se muestran como tarjetas de solo lectura (inmutables). Los atributos manuales son editables y eliminables.
  5. Puede agregar atributos manuales propios del tipo de activo con tipos: `text`, `number`, `date_alert` (fecha con alerta en días), `date_info` (fecha informativa).
  6. Cada atributo puede tener **Reglas SRE** (frecuencia, margen de pre-alerta, checklist resolutor).
* **Reglas de Inmutabilidad (`field._imported`):**
  - Campos con `_imported: true`: etiqueta y clave son de solo lectura, sin botón de borrado.
  - Campos sin `_imported`: completamente editables y eliminables.
  - Al re-importar un checklist diferente: los campos `_imported` anteriores se borran automáticamente y se reemplazan por los del nuevo checklist. Los campos manuales se conservan.
* **Política de Versionado (Regla de Oro):**
  - Los checklists son documentos controlados (análogo a normas ISO 9001/45001).
  - Si un checklist necesita corrección, se crea una **nueva versión** del checklist y el gestor re-importa el tipo de activo de forma deliberada.
  - El checklist original sigue funcionando en inspecciones de todas las sedes sin conflicto con el AssetType.
* **Ámbito Multi-Sede:**
  - `AssetType` es de `empresa_id` (plantilla compartida a nivel empresa).
  - `Asset` individual es de `sede_id` (el activo pertenece a una sede).
  - Dos sedes pueden compartir el mismo tipo sin conflicto. El checklist original puede seguir usándose normalmente en cualquier sede.


### 3.12. Estándares de Responsividad, Scrollbars de Marca y Destructuración Segura
* **Propósito:** Ofrecer una experiencia táctica de primer nivel (HUD) en cualquier dispositivo, garantizando que el personal técnico en faenas y portátiles medianas navegue cómodamente sin colapsos de UI.
* **Directrices Técnicas:**
  - **Breakpoint Táctil Cards (Límite < 1024px):** Para pantallas inferiores a `1024px` (`lg` en Tailwind), las tablas complejas se ocultan (`lg:block hidden`) y la información se despliega verticalmente mediante tarjetas táctiles (`lg:hidden block`).
  - **Scrollbar de Marca (`.custom-scrollbar`):** Definido globalmente en `resources/css/app.css`. Los envoltorios de tablas (`overflow-x-auto`) deben tener la clase `.custom-scrollbar` con los colores del pilar asignado:
    - *Checklists / Inspecciones / Matrices:* Naranja técnico (`rgba(246, 146, 57, 0.3)`).
    - *Educación / Capacitaciones / Hallazgos:* Índigo técnico (`rgba(99, 102, 241, 0.3)`).
    - *Reportes / Incidentes / Bitácoras:* Rojo técnico (`rgba(239, 68, 68, 0.3)`).
  - **Columna Fija Adaptable en Matrices (Annual Contract):** Las columnas fijas en tablas anchas no deben ocluir el resto de la pantalla en dispositivos móviles. Su ancho debe ser dinámico (`w-[140px] sm:w-[220px] md:w-[300px]`) con paddings de celda compactos en resoluciones bajas.
  - **Destructuración Segura de Propiedades (`auth`):** En todas las páginas y componentes React resueltos mediante Inertia, cuando se requiera validar roles de usuario o condicionales de rutas, se debe usar `usePage` para destructurar la propiedad `auth` de forma segura:
    ```javascript
    const { auth } = usePage().props;
    ```
    Esto previene errores de referencia nula en entornos de producción.
  - **Depuración de Activos Obsoletos:** Se eliminaron las capturas obsoletas `cap1.png`, `cap2.png` y `cap3.png` para mantener limpio el almacenamiento del proyecto, dado que ya no son referenciadas por la landing page en React (`Welcome.jsx`).
  - **Módulo de Activos y Trazabilidad con Identificación por Códigos QR:**
    * **Desacoplamiento Temático (Activo Genérico):** Los checklists no guardan datos de equipos fijos en su esquema relacional. Se vinculan a un `Asset` de forma dinámica, permitiendo que la misma estructura sirva a empresas de logística (camionetas) y alimentación (frigoríficos).
    * **Escaneo QR de Activos:** La cámara móvil lee el QR del activo, resuelve su ficha y tipo (`asset_type_id`), e inicia de inmediato el checklist preventivo correspondiente, bloqueando entradas manuales para evitar errores de digitación de patentes o códigos de barra.
    * **Trazabilidad Longitudinal:** Preguntas del form builder enlazadas mediante `vincular_a_metadata` actualizan el JSON `metadata` del activo (ej: kilometraje del vehículo) de forma automatizada al finalizar la inspección.
    * **Identificación y Asistencia de Personal por QR:** Los empleados presentan su credencial digital/física QR. Escaneándola, el capacitador registra asistencias instantáneas en `VisitTraining` sin tipeos manuales, inyectando nombre y DNI a `training_attendances` en 5 segundos. Permite auditorías de EPP nominales y firma inmutable de supervisores con validación GPS de cercanía.
  - **Intercepción Visual y Auto-completado por Falla Crítica en Asistente (`Wizard.jsx`):**
    * **Bloqueo Inteligente en Wizard:** Al intentar pasar de sección, el sistema detecta si alguna respuesta es considerada una no-conformidad crítica (comparando contra `failure_values` mediante `failure_logic`).
    * **Modal HUD de Intercepción:** Muestra un modal Glassmorphism táctico con dos opciones: continuar con la inspección o rechazar y finalizar.
    * **Llenado en Cascada Automatizado:** Si se selecciona "Rechazar y Finalizar", el frontend auto-completa el resto de preguntas de secciones futuras con la leyenda de auditoría `"No evaluado por falla crítica"` y salta directamente al cierre e ingreso de firmas del checklist.
  - **Flujos Contextuales en Visitas Activas (Hallazgos e Incidentes):**
    * **Consistencia de Ubicación:** Al reportar hallazgos o incidentes críticos desde una visita activa, el selector se bloquea y se transforma en una tarjeta visual inmutable ("📍 Fijo por Visita") para prevenir que el técnico ingrese información en sedes equivocadas.
    * **Redirección de Retorno:** Al guardar con éxito el formulario, el técnico es redirigido directamente a la pantalla de la visita activa en progreso (`tecnico.visits.in-progress`) para asegurar la continuidad de su jornada de trabajo.
  - **Soporte de Inputs Temporales Avanzados (Checklists):**
    * **Hora y Fecha/Hora combinadas:** Campos dinámicos con inputs nativos para `time` y `datetime-local` adaptados a mobile.
    * **Asistencia Táctil Rápida:** Botones de un toque ("🕒 Hora Actual" / "📅 Ahora") que inyectan de forma inmediata la fecha y hora locales del dispositivo del técnico sin tipeo manual.
  - **UX de Evidencia Fotográfica Acumulativa:**
    * **Carga Secuencial:** Permite subir múltiples fotos de forma secuencial sin que una nueva selección borre los archivos ya seleccionados.
    * **Remoción en Previsualización:** Cada miniatura de foto de evidencia incluye un botón rojo flotante "✕" para removerla individualmente antes de la persistencia física en el backend.

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

### 4.3. Prompt para Configuración de Tipos de Checklist Dinámicos
> **Contexto:** Estamos en `resources/js/Pages/Gestor/Assets/Types/Index.jsx`. Esta pantalla configura los "Tipos de Checklist Dinámicos" (modelo `AssetType`).
> **Regla crítica de UI:**
> - Los atributos con `field._imported === true` son de solo lectura: no se muestra input de etiqueta, ni de clave, ni botón de borrado.
> - Los atributos sin `_imported` son completamente editables.
> - Al re-importar un checklist, los atributos `_imported` anteriores se borran y se reemplazan por los del nuevo checklist. Los manuales se conservan.
> **Regla de Política (Documentos Controlados):**
> Los checklists son documentos controlados (ISO 9001/45001). Si se corrige un checklist, se crea una nueva versión y el gestor re-importa deliberadamente.
> **Regla de Ámbito:**
> `AssetType.empresa_id` (nivel empresa, compartido entre sedes). `Asset.sede_id` (nivel sede). No hay conflicto entre sedes que usen el mismo tipo.
> **Tarea:** `[Describir mejora o corrección en el configurador]`.
