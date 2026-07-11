---
description: Documento de Requisitos de Producto (PRD) para XamaSafe
---

# 📋 Documento de Requisitos de Producto (PRD) — XamaSafe

## 1. Control del Documento
* **Nombre del Producto:** XamaSafe
* **Versión del PRD:** v2.4 (Stack React + Inertia.js)
* **Estado:** Activo / En Desarrollo
* **Última Actualización:** 10 Julio 2026
* **Tecnología Core:** Laravel 12 + Inertia.js (React 19) + Tailwind CSS v4 + SQLite/MySQL + Ziggy JS

---

## 2. Visión General del Producto
XamaSafe es un sistema SaaS integral de Higiene y Seguridad (HyS), Normas de Calidad (ISO, BPA) y Auditorías Ambientales. Está diseñado para simplificar la creación, gestión y ejecución de formularios dinámicos y checklists en terreno por parte de técnicos de campo, consolidando toda la información en una consola de mando táctica para administradores y consultores.

### 🌟 El Factor Diferencial: Tactical Premium Dark (HUD)
XamaSafe destaca por una interfaz inmersiva y de alto impacto visual. Emplea una estética **HUD Futurista / Dashboard Táctico** con tonos oscuros profundos (`#11111D`), bordes con efectos de cristalizado (*glassmorphism*), y colores de acento neón brillantes (Naranja, Verde Neón y Violeta) que transmiten precisión y tecnología.

---

## 3. Personas y Roles de Usuario

| Rol de Usuario | Descripción | Objetivos Clave |
|----------------|-------------|-----------------|
| **Súper Administrador (`super_admin`)** | Posee privilegios globales absolutos del sistema. | Dar de alta/baja consultoras, configurar límites máximos de licencias, y gestionar el personal global (súper admins y vendedores). |
| **Vendedor (`vendedor`)** | Rol global de ventas y soporte comercial. | Administrar límites de licencias, altas y estado de suscripción de consultoras, sin acceso a borrado o gestión de personal. |
| **Gestor (Administrador / Consultor)** | Administra el sistema de una consultora específica. | Configurar checklists, supervisar técnicos en tiempo real, gestionar clientes/sedes, emitir reportes anuales de contratos y descargar PDFs consolidados. |
| **Técnico (Inspector de Terreno)** | Opera en campo utilizando dispositivos móviles o tablets. | Registrar visitas, certificar presencia mediante coordenadas GPS, completar checklists interactivos con firmas digitales y capturar hallazgos con registro fotográfico. |
| **Cliente Corporativo (Lector)** | Visualiza e inspecciona el estado de sus sedes. | Consultar las constancias de visitas, ver estadísticas de cumplimiento y descargar certificados oficiales de seguridad y capacitaciones. |

---

## 4. Módulos y Requisitos Funcionales

### 4.1. Core Engine: Checklists Dinámicos e Inspecciones
* **Generación de Plantillas:** Permite a los Gestores definir plantillas de checklists con categorías y tipos de preguntas variados (texto, numérico, opción múltiple, booleano, adjunto de fotos, fecha, hora (`time`), y fecha/hora combinadas (`datetime`)).
* **Asistente de Llenado con Soporte Temporal Avanzado:** En la ejecución del checklist, el técnico dispone de campos con inputs nativos y elegantes para los tipos **Hora (`time`)** y **Fecha/Hora (`datetime`)**. Incluye botones de llenado rápido táctico ("🕒 Hora Actual" y "📅 Ahora") que capturan la hora y fecha locales del dispositivo con un solo toque.
* **Intercepción Visual por Falla Crítica (SRE Shield):** Para garantizar el cumplimiento de normativas de seguridad estrictas, el Wizard evalúa en caliente si alguna respuesta de la sección actual se encuentra en los `failure_values` configurados al presionar "Siguiente Sección". Ante un desvío crítico detectado:
  - Se despliega un modal HUD estilo Glassmorphism con un indicador de advertencia rojo animado y dos caminos.
  - **Continuar Inspección:** Cierra el modal y permite continuar evaluando los puntos restantes de la auditoría.
  - **Rechazar y Finalizar:** Dispara un flujo de auto-llenado automático que recorre las preguntas sin responder de secciones posteriores y las completa con la leyenda de auditoría `"No evaluado por falla crítica"`, saltando inmediatamente al paso final de Firmas y observaciones para un cierre inmediato.
* **Consola de Técnico Unificada en 3 Pilares (`MyAssignments.jsx`):** Rediseño completo de la interfaz de tareas en un panel dinámico estructurado en tres áreas de acción:
  1. **Misiones (Inspecciones):** Gestión de checklists preventivos y regulatorios. Muestra un KPI de checklists asignados vs completados, tasa de éxito y cumplimiento normativo.
  2. **Educación (Capacitaciones):** Charlas express y capacitaciones con registro de firmas e identificación de asistentes (DNI).
  3. **Métricas (Informes):** Redacción de reportes mensuales y control del avance del contrato.
* **HUD del Técnico (ActionSelector):** Panel modal interactivo de pantalla completa activable durante una inspección en progreso. Permite iniciar ejecuciones libres o basadas en el plan mensual:
  - **Buscador Escalable y Filtro Jerárquico:** El técnico puede realizar búsquedas por texto (nombre/código de norma) y aplicar filtros dinámicos en cascada de Categorías y Subcategorías para seleccionar rápidamente plantillas.
  - **Ejecuciones Ad-Hoc:** Inicia al vuelo una visita y asignación autogenerada cuando se ejecutan checklists o charlas fuera de la planificación ordinaria.
* **Sesión de Inspección en Progreso:** Toda actividad técnica ocurre bajo una sesión de inspección iniciada ("Nueva Inspección"). La pantalla de progreso muestra un listado de tareas completadas de forma continua, agrupadas por plantilla e identificadas mediante **Counter Badges** de acento neón flotantes en las esquinas superiores (`absolute -top-3 -right-3`, `rounded-2xl`, tipografía `font-black`).
* **Blindaje e Inmutabilidad de Tiempos (SRE Security):**
  - **Inmutabilidad en Servidor:** Las peticiones de finalización de inspección descartan de forma absoluta cualquier marca de tiempo (`completed_at`, `date`, `time`, `timestamp`) enviada por el cliente, forzando estrictamente el uso del timestamp `now()` en el servidor Laravel.
  - **Auditoría Activa SRE:** Se audita en tiempo real cualquier intento de bypass de fecha desde el cliente, inyectando un `Log::warning()` detallado en `laravel.log` indicando sospecha de fraude de horarios y capturando la IP de origen, ID/nombre del técnico, ID de la visita y el payload descartado.
  - **HUD Sync Badge:** Muestra visualmente arriba de la barra de progreso en el asistente de llenado (`Wizard.jsx`) un badge con la tipografía de acento neón en Naranja Técnico: `"⌚ Tiempo Real (Sync Servidor)"` para transmitir tranquilidad visual y auditoría automática al técnico de campo.
* **Métricas y KPIs de Alta Fidelidad en Tablas:** Se reemplaza el estado lineal simple por la columna **"Checklists"** (en la pestaña de Inspecciones) que muestra en tiempo real la proporción de checklists completados. Si existen completados, añade el ratio de **"Exitosos: X/Y"** (en verde esmeralda si pasaron todos, o en rojo de seguridad con el icono `⚠️` si se detectó alguna falla) y el porcentaje de cumplimiento normativo destacado mediante sombras de texto de alta gama (`glow-text`).
* **Historial de Inspecciones y Descarga (Bóveda de Certificados):** El panel "Mis Asignaciones" proporciona un botón de **"Ver Historial"** que redirige a la consola de historial filtrada por el checklist correspondiente.

### 4.2. Bóveda de Certificados: Historial de Auditoría Agrupado por Fecha y Sede
* **Consolidación Diaria por Servidor (`InspectionController.php`):** Las inspecciones del técnico se agrupan en el controlador por la fecha de término (`DATE(completed_at)`) y la ubicación (`sede_id`), evitando la saturación del listado al auditar múltiples activos (como 15 vehículos) el mismo día.
* **Constancia de Visita Principal:** Cada fila principal agrupada representa una jornada de trabajo y provee el enlace directo para descargar el PDF de la **Constancia de Visita** (`VisitCertificate`) firmada digitalmente por el jefe responsable del cliente. Si la visita no se ha firmado o cerrado aún, se muestra un botón en gris deshabilitado `"Sin Constancia"` como advertencia visual estética.
* **Sub-Acordeón de Inspecciones Individuales:** Cada fila agrupada incorpora un botón interactivo `Ver Checklists (X)`. Al pulsarlo, desliza un subpanel detallado que lista las inspecciones de ese día mostrando:
  - Título y código regulatorio del checklist.
  - Hora de finalización y **nombre de quien firmó la inspección (`supervisor_name`)**.
  - Badge semafórico de resultado: **Pasó** (Verde Esmeralda) o **Falló ⚠️** (Rojo Alerta Neón si se detectó una no-conformidad en el check).
  - Un botón de descarga directa para el PDF individual de ese check específico.

### 4.2. Módulo de Campo: Visitas con Validación GPS (Proof of Presence) y Auditoría Horaria
* **Geolocalización Inmediata:** Captura obligatoria de latitud/longitud del dispositivo móvil del técnico mediante la API nativa de geolocalización al realizar:
  1. **Check-in (Entrada):** Apertura de la visita, registrando `checkin_lat` y `checkin_lng`.
  2. **Check-out (Salida):** Finalización de la visita, registrando `checkout_lat`, `checkout_lng` y el indicador `gps_verified`.
* **Cálculo de Distancia (Fórmula de Haversine):** Compara el pin del técnico con las coordenadas registradas de la Sede en la base de datos aplicando la fórmula matemática de Haversine contra el `radio_tolerancia_metros` (default 500m) configurado por sede.
* **Auditoría de Tiempos (Campo vs Gabinete):**
  * **Horas de Campo:** Calculadas automáticamente a partir de la diferencia entre el inicio (`start_time`) y fin (`end_time`) de la visita presencial.
  * **Horas de Gabinete:** Permite la carga estimativa de horas de oficina dedicadas a la confección y análisis técnico del informe (`horas_gabinete_estimadas`).
* **Modalidad Roster Minero 24/7:** Configuración especial para sedes en zonas de operación minera continua. Al activarse, se omiten las alertas ordinarias de días y horarios no laborales, validando de forma automática cualquier ingreso del técnico (incluyendo domingos y feriados).
* **Control Inteligente de Jornada:** En sedes estándar, el sistema audita en tiempo real que los ingresos ocurran en los días (`dias_operativos`) y horarios (`hora_apertura` / `hora_cierre`) parametrizados, disparando advertencias visuales de sobrejornada en el dashboard táctico ante excesos.

### 4.3. Motor de Autenticación y Autorización Robustecida (Fix Definitivo de Roles)
* **Parsing Universal de Roles (Modelo `User`):** La verificación de accesos soporta múltiples formatos de definición para una flexibilidad y confiabilidad absoluta en producción:
  - Strings simples (ej. `'Técnico'`, `'gestor'`).
  - Cadenas delimitadas por comas (ej. `'Técnico,tecnico,gestor'`).
  - Cadenas separadas por barras o pipes (ej. `'Técnico|tecnico|gestor'`).
  - Arrays y estructuras anidadas (ej. `['gestor', 'tecnico,Socio']`).
* **Simplificación y Delegación de Middleware (`CheckRole`):** Reducción de la lógica de acoplamiento en el middleware de ruta. Toda solicitud de roles de las rutas de Laravel se delega directamente a `User::hasRole()`, garantizando consistencia arquitectónica bidireccional (Rutas de Middleware vs Políticas de Controladores).

### 4.4. Módulo de Hallazgos e Incidentes
* **Reporte Rápido y Sede Contextual:** Durante una visita activa, el técnico puede levantar un "Hallazgo" o "Incidente" al vuelo. Si se reporta dentro del contexto de una visita activa (`visit_id` presente):
  - El selector manual de Sede se bloquea y se reemplaza por un componente visual de solo lectura de gama premium ("📍 Fijo por Visita") que previene errores humanos al asociar el desvío.
  - Al completar la creación, el sistema redirige automáticamente al técnico de regreso a la consola de su visita activa en progreso (`tecnico.visits.in-progress`).
* **UX Optimizado para Captura de Evidencias (Hallazgos):**
  - **Eliminación de la Fecha de Detección:** Se omitió el campo visual de fecha de detección del formulario del técnico. El backend se encarga de inyectar automáticamente el timestamp actual (`now()`) para reducir la carga cognitiva en campo.
  - **Carga de Fotos Acumulativa y Preselección:** El input de evidencia fotográfica acumula fotos secuencialmente (una por una). Cada miniatura previsualizada incorpora un botón rojo "✕" para permitir la remoción de imágenes antes del guardado.
  - **Legibilidad:** Todas las etiquetas (labels) de los formularios se estructuraron en bloques independientes verticales en lugar de estar pegadas a los inputs para máxima legibilidad móvil.
* **Incidentes Críticos y Ajuste Estético:**
  - **Campos Numéricos Compactos:** Los campos numéricos "Personas Afectadas" y "Días Perdidos Estimados" se redujeron a un ancho estético centrado (`w-32`) para evitar distorsiones visuales en tablets y dispositivos de campo.
* **Gestión de Estados para Técnicos:** Se habilitó el método `update` en el backend y el modal de modificación de estado en la UI de detalle para que los Técnicos puedan actualizar de forma ágil el avance del hallazgo reportado.
* **Plan de Acción:** Los gestores y clientes pueden visualizar los hallazgos abiertos, definir planes de acción correctiva y marcar la resolución del problema con una foto de "después".

### 4.5. Módulo de Formación (Capacitaciones)
* **Registro Técnico:** Planificación y ejecución de capacitaciones grupales o individuales en campo.
* **Firma Digital Integradora:** Registro directo en pantalla de los asistentes y capacitadores utilizando un panel digital (`signature_pad` integrado en React).
* **Asistencia e Identificación Completa (DNI):** El formulario de asistencia incluye Nombre, Puesto y **DNI / Identificación obligatorio**, persistiendo los asistentes y sus firmas digitales en filas normalizadas en la base de datos (tabla `training_attendances` vinculada a `visit_trainings`). Las tarjetas en la consola reflejan de forma clara el DNI y rol del participante (ej. `OPERARIO • DNI: 95827361`).
* **Bitácora y Editor de Notas Técnica en Caliente:** Permite al técnico editar y guardar en tiempo real el temario desarrollado (`description`) y observaciones de campo (`notes`) directamente sobre columnas estructuradas e independientes en la tabla de base de datos `visit_trainings` de la visita en progreso.
* **Finalización de Charla y Modal Premium:** La sesión se da por finalizada mediante un comando táctil en cabecera que despliega un modal React de diseño premium con fondos desenfocados en blur e íconos en 3D, actualizando el estado de la capacitación a `'completed'` para sincronizar de inmediato su aparición en la consola cronológica del HUD.
* **Planificación de Capacitación Anual Avanzada:** Permite al Gestor registrar y editar al momento de crear o editar las actividades en el Plan Anual (`AnnualPlan`):
  - **Duración Estimada:** Tiempos fijos parametrizados (5m, 10m, 15m, 30m, 45m, 1h, 1:30h, 2h, 3h).
  - **Modalidad:** Opciones de "Presencial", "Virtual" e "Híbrido".
  - **Multi-Instructor:** Switch para activar el soporte de co-instructores adicionales.
* **Herencia y Automatización en Sesión en Vivo:** Al iniciar una capacitación desde la visita activa, el sistema hereda automáticamente la duración y la modalidad planificadas del Plan Anual. Al finalizar, la duración real se calcula mediante el cronómetro interactivo, y el cierre se realiza con la firma digital exclusiva del Técnico firmante (eliminando firmas de co-instructores redundantes).
* **Consola Histórica y Acordeón Táctico (Gestor):** La grilla de historial de capacitaciones de la consola (`Index.jsx`) implementa un sub-acordeón interactivo inline de alto rendimiento. En lugar de grids de firmas que saturan la pantalla, el listado de asistentes se renderiza en una tabla minimalista de alta escalabilidad (Nombre, Función, DNI) idónea para grupos numerosos de hasta 30+ personas.
* **Certificación en PDF Inteligente:** El generador de PDFs (`TrainingPDFController`) realiza una detección cruzada en caliente: si el ID solicitado corresponde a una ejecución real (`VisitTraining`), mapea de forma dinámica las firmas, asistencia final, observaciones y duración reales de la sesión, eliminando las leyendas temporales del Plan Anual y asegurando total consistencia legal.
  * **Alternancia de Roles en Capacitación:** Las firmas y flujos de firmas de capacitación detectan dinámicamente si el rol activo del operador es de técnico, redirigiéndolo limpiamente al finalizar de vuelta a su HUD de sesión en progreso.
* **Capacitación Ad-hoc / Libre Unificada (`MyAssignments.jsx`):**
  - Se consolida el inicio rápido de capacitaciones eliminando la división confusa entre Charla de 5 Minutos y Checklist Estándar. Toda capacitación ad-hoc se asocia obligatoriamente a un checklist/protocolo técnico para garantizar trazabilidad.
  - Cuenta con buscador reactivo de texto y filtros en cascada de Categoría y Subcategoría (idénticos a los de Inspecciones pero con la paleta de color Índigo Técnico).
  - Al presionar "Iniciar", la tarjeta del checklist seleccionado se expande verticalmente (Inline Accordion) con transiciones suaves, permitiendo configurar los datos de la sesión antes de comenzar:
    1. **Modalidad de Capacitación:** Segmented button control táctil ("In situ (Presencial)" y "Online (Virtual)") con glows índigos de neón.
    2. **Duración Estimada:** Selector rápido (5, 10, 15, 30, 45, 60 minutos).
    3. **Descripción / Objetivos:** Cuadro de texto para notas tácticas.
    4. **Confirmar & Iniciar:** Botón final para inyectar la capacitación de forma inmediata.
  - **Almacenamiento de Modalidad:** La modalidad se guarda directamente en la columna `type` de la tabla `trainings` (como `'presencial'` o `'virtual'`) logrando consistencia con el diseño de base de datos y su correcta visualización en el reporte PDF.

### 4.6. Módulo Universal de Activos y Trazabilidad Dinámica
* **Consola de Mando y Bóveda de Activos (`AssetController`):**
  - Gestión integral de inventario técnico (Matafuegos, Redes de Agua, Autoelevadores, Vehículos, Tableros Eléctricos) organizada por Empresa, Sede y Tipo de Activo.
  - Asignación de identificadores unívocos **UUID (v4)** en base de datos para prevenir vulnerabilidades IDOR y enumeración de recursos.
  - KPIs en tiempo real (Operativos 🟢, En Alerta 🟡, Fuera de Servicio 🔴) y filtrado predictivo multi-tenancy.
* **Alta Dinámica Multi-Activo con Validación Anti-Duplicados (`Create.jsx`):**
  - Carga ágil en lote de múltiples activos en un solo paso con auto-incremento inteligente de identificadores (ej. `MF001` -> `MF002` -> `MF003`).
  - Validación de duplicados en tiempo real (frontend con bordes de alerta e impedimento de envío) y en backend (`distinct` y `unique:assets,identifier`).
* **Schema-Driven Metadata y Roles de Pre-llenado (`AssetType` / Tipos de Checklist Dinámicos):**
  - Los Gestores definen en `asset_types.metadata_schema` campos dinámicos JSON (`type`, `label`, `show_in_create`, `role`, `alert_days`, `alert_threshold`).
  - **Roles de Atributo:** Cada campo de metadata se asocia a un rol para determinar su comportamiento de pre-llenado en el Wizard:
    1. `identificador` (Identificador único de alta, ej: N° Interno) → Pre-llenado: SÍ.
    2. `dato_estatico` (Dato técnico permanente, ej: Tipo de Agente) → Pre-llenado: SÍ.
    3. `dato_con_regla` (Métricas que disparan reglas/fechas SRE, ej: Próxima Carga) → Pre-llenado: SÍ.
    4. `verificacion` (Preguntas evaluadas en campo, ej: manómetro en zona verde) → Pre-llenado: NO (inicia vacío).
  - Flag `show_in_create` permite solicitar solo los campos de identificación críticos durante el alta rápida.
  - **Ámbito de Empresa (No de Sede):** El `AssetType` pertenece a la empresa (`empresa_id`), siendo una plantilla compartida globalmente por todas las sedes de esa empresa. Los activos individuales (`Asset`) son los que se asocian a una `sede_id` específica. Esto garantiza que un tipo (ej: "Protección Contra Incendios") sea reutilizable por todas las sedes sin conflictos.
* **Configurador de Expediente Fotográfico por Categoría (`photo_schema`):**
  - Parametrización en `asset_types.photo_schema` de las fotos base requeridas por categoría (ej. 4 fotos para Camionetas, 2 para Extintores).
* **Consola de Ficha Técnica, Timeline Multimedia y Evidencias (`Show.jsx`):**
  - Expediente fotográfico con carga/actualización directa por ranura en `storage/app/public/assets/{id}/`.
  - Galería de evidencias de inspecciones, hallazgos y siniestros reportados por los técnicos con visor Lightbox modal en pantalla completa.
* **Exportación a PDF en Formato A4 Estándar (`asset-report.blade.php`):**
  - Generación de informes ejecutivos A4 con marca de agua, Código QR, mosaico de fotos e historial de inspecciones.
* **Integración con el Wizard de Inspección (Paso 0 - Trazabilidad):**
  - El técnico debe seleccionar el **Activo Objeto de Inspección** mediante un buscador predictivo o lectura de código QR/Patente antes de responder el checklist.
  - Persistencia del vínculo `dynamic_inspections.asset_id`.
  - **Badge Dinámico de Cabecera:** Muestra un badge de vinculación dinámico en el Wizard según el activo seleccionado.
  - **Advertencia de Re-inspección (Modal Ámbar):** Si el técnico intenta seleccionar un activo que ya fue auditado en la visita actual (estado `✓ Auditado`), el sistema detiene la selección inmediata y muestra un modal de confirmación táctico premium en color ámbar/dorado advirtiendo sobre la duplicación legal del reporte.
* **Sincronización Automática, Log de Cambios y Resolución SRE (`SyncAssetMetadata`):**
  - Mapeo de respuestas del checklist a los atributos del activo (`dynamic_questions.vincular_a_metadata`).
  - **Trazabilidad y Log Auditor Inmutable (`AssetMetadataLog`):** Al finalizar una inspección, el sistema compara el valor previo del metadato con el nuevo valor enviado en las respuestas. Si hay un cambio, se guarda un registro inalterable en `asset_metadata_logs` (valor anterior, nuevo valor, técnico, inspección y fecha). La ficha de activo en la consola incorpora la pestaña `"🕐 Historial de Metadata"` para consultar este registro auditor.
  - Ante una inspección con resultado `Fail`, la bomba de estado transiciona automáticamente el activo a **"Fuera de Servicio"** (`fuera_de_servicio`).
  - **Resolución SRE de Ciclo Infinito:** Si la inspección corresponde al checklist de resolución configurado para una regla SRE, al finalizarse se recalcula automáticamente la próxima meta del atributo (`proximo_val = valor_actual + frecuencia`), se marca la asignación correctiva (`ChecklistAssignment`) como completada y se auto-resuelve el hallazgo crítico abierto.
  - Audit log inmutable de cambios en `audit_logs` vía `AssetObserver`.
* **Motor de Mantenimiento Autónomo y Alertas SRE (`CheckAssetAlerts`):**
  - Comando `php artisan assets:check-alerts` programado en el scheduler.
  - Evalúa vencimientos de fechas (`alert_days`) y umbrales numéricos (`alert_threshold`) en las reglas tradicionales y en las **múltiples reglas SRE de ciclo infinito** configuradas en la ficha técnica.
  - **Pre-Alerta (Warning):** Si una métrica o fecha entra dentro del `margen_pre_alerta`, el activo pasa al estado `alerta` (amarillo).
  - **Crítico (Lockout):** Si el valor supera la frecuencia de servicio de cualquier regla SRE, el activo pasa a `fuera_de_servicio` (rojo). Se genera automáticamente un `Hallazgo` crítico y se **auto-asigna una orden de trabajo correctiva (`ChecklistAssignment`)** vinculando al activo físico con el checklist de resolución idóneo. Para permitir la operación colaborativa, estas órdenes de trabajo se generan con `user_id = null` (compartidas a nivel de sede). Cualquier técnico con acceso a la sede las visualizará como tareas pendientes en su consola y, al momento de iniciar su ejecución en el Wizard, el sistema realiza la auto-asignación dinámica al `user_id` del técnico ejecutante y cambia el estado a `in_progress` para prevenir colisiones.
  - **Hover Tooltip de Detalles en Index:** Al pasar el mouse sobre el estado "Alerta" del activo en la tabla general, se despliega hacia abajo (`top-full mt-2`) un tooltip oscuro premium detallando cada campo que causó la alerta (ej: "Vencimiento Carga" o "Prueba Hidráulica") con la fecha exacta y días restantes, ocultando campos técnicos redundantes.
  - **Baja Lógica y Preservación Histórica (Soft Deletes):** El borrado de activos realiza una baja lógica (`SoftDeletes`). El botón en el listado se denomina "Dar de Baja". Para garantizar que el historial legal de inspecciones e incidentes pasados de un activo de baja no quede huérfano, las relaciones de modelos cargan los activos incluyendo registros eliminados (`->withTrashed()`).
* **Bloqueo Operativo en Terreno (Interlock):**
  - **Validación de Bloqueo (Backend):** Si un activo se encuentra `fuera_de_servicio`, el backend en `InspectionController.php` bloquea cualquier intento de guardar o completar inspecciones comunes en dicho activo, lanzando un error de validación a menos que el checklist ejecutado sea su checklist resolutor predefinido.
  - **Bloqueo en Interfaz (Frontend):** En el asistente de inspecciones (`Wizard.jsx`), si el técnico selecciona un activo inhabilitado (`fuera_de_servicio`), se deniega el acceso y se despliega un banner de error rojo crítico: *"ACCESO DENEGADO: El equipo se encuentra Fuera de Servicio por Mantenimiento Vencido"*.

### 4.7. Control y Gestión de Firmas Digitales y Aprobación Segura
* **Tipología de Firmas del Sistema:**
  * **Firma Tipo A (Firma de Perfil / Identidad Permanente / Instructor Especialista):**
    * **Definición:** Es la firma oficial y permanente que identifica a un Técnico, Usuario o Instructor Especialista en el sistema.
    * **Flujo de Captura:** Se realiza desde la pantalla de Perfil o el canvas de finalización usando un canvas de dibujo React. El frontend transmite la firma temporalmente como un string Base64 URL (`data:image/png;base64,...`).
    * **Almacenamiento Físico (Optimización Storage vs Base64):** El backend (e.g. `TrainingController` o `ProfileController`) intercepta el string Base64, lo decodifica de forma segura y lo guarda físicamente como un archivo `.png` en el disco público de la aplicación (`Storage::disk('public')->put('signatures/filename.png', $data)`).
    * **Persistencia Ligera en Base de Datos:** En las tablas (`users.firma`, `visit_certificates.instructor_signature`), solo se almacena la ruta relativa (ej. `signatures/instructor_xxxx.png`). Esto previene el crecimiento desmedido de las filas de texto largo (`TEXT`/`LONGTEXT`) y mantiene las consultas optimizadas.
    * **Auditoría Obligatoria (Técnicos):** Para técnicos, no se aplica inmediatamente. Pasa a estado pendiente (`firma_status = 'pending'`) y se guarda temporalmente en `firma_temp`.
  * **Firma Tipo B (Firma Transaccional / De Visita o Planilla):**
    * **Definición:** Firma única, temporal y dinámica capturada en caliente para certificar un evento específico (asistencia a capacitación, firma de supervisor de visita).
    * **Almacenamiento:** Mapeadas como Base64 serializadas o strings codificados asociados de forma directa a campos específicos de la base de datos (como la firma del cliente al cerrar una constancia, o las firmas de los operarios en la tabla `training_attendances`).

* **Política de Firma Pendiente (Auditoría de Identidad):**
  * Cuando un Técnico actualiza su firma digital desde su panel de Perfil (`/perfil`), esta entra en estado de aprobación pendiente (`firma_status = 'pending'`) y la firma propuesta se almacena en la ruta temporal `firma_temp`.
  * **Auditoría por Gestor (Panel Táctico):** Los Gestores visualizan las firmas pendientes en el listado maestro de identidades (`Users.jsx`). El componente `PendingSignaturesList.jsx` destaca las firmas pendientes con contrastes oscuros inmersivos y les permite tomar una acción interactiva:
    * **Aprobar:** Copia físicamente el archivo de la firma temporal a la ruta permanente final, cambia el estado a `approved`, actualiza la firma activa en `users.firma` y limpia `firma_temp`.
    * **Rechazar:** Elimina físicamente el archivo temporal en el storage y marca `firma_status = 'rejected'` para notificar visualmente al técnico y permitirle reintentar.
  * **Visor de Firmas Oficiales:** Se integra un modal visor Glassmorphic en el listado de usuarios que permite a los Gestores visualizar de forma inmediata las firmas aprobadas vigentes de cada técnico a través de un botón interactivo de inspección visual ("ojito").

### 4.8. Consolidación de Informes Maestros y Hardening Arquitectónico (SRE Audit)
* **Flujo de Trabajo Intacto e Ininterrumpido:** Se respeta al 100% el flujo de inicio desde *Mis Asignaciones* (pestaña Informes) mediante el botón interactivo de "Ejecutar", heredando limpiamente los parámetros contextuales de `sede_id` y `periodo` hacia el Wizard de Redacción.
* **Erradicación Absoluta de Firmas Manuales (Canvas):** No se permite en absoluto el trazo manual en pantalla dentro del Informe Maestro. El sistema adopta como fuente de verdad única e inmutable la **Firma Tipo A (Rúbrica de Perfil)** cargada en el perfil del técnico certificante (`report.tecnico.firma`).
* **Máquina de 5 Estados del Informe Maestro (Backend-Driven):**
  El campo `accion` es calculado **exclusivamente en el backend** (`OperationController.php`) y transmitido al frontend. El frontend `MyAssignments.jsx` solo renderiza basándose en este campo. La lógica es:
  1. **`ejecutar`** — No existe ningún `MasterReport` para esta sede en el periodo seleccionado. Se muestra el botón amarillo "+ EJECUTAR".
  2. **`reanudar`** — Existe un informe con `estado = 'en_redaccion'` sin observaciones de revisión. Se muestra el enlace amarillo sutil "REANUDAR" al Wizard de edición.
  3. **`corregir`** — Existe un informe con `estado = 'en_redaccion'` Y `observacion_revision` no nulo (devuelto por el gestor). Se muestra el enlace rojo pulsante "⚠ CORREGIR" con sombra de alerta neón.
  4. **`pendiente`** — El informe está en `estado = 'finalizado'` (enviado al gestor). Se muestra un badge amarillo sutil de solo lectura "⏳ PENDIENTE GESTOR" sin ningún botón de acción.
  5. **`aprobado`** — El informe tiene `estado = 'aprobado'`. Se muestra el enlace emerald "✅ VER INFORME" hacia `GET /master-reports/{id}`.
  **Regla de Unicidad:** Solo puede existir un informe por periodo (`YYYY-MM`) por sede+técnico. El backend busca por el campo `periodo` (no por `created_at`) para detectar el informe existente.
* **Consola de Redacción Wizard y Validación Táctica:**
  - **Firma Autorizada Vinculada:** Si el técnico posee su firma de perfil activa y aprobada, el Wizard renderiza una tarjeta de validación de solo lectura con estilo Glassmorphic Ultra-Premium DARK, destacada con un glow verde de seguridad (`bg-emerald-500/10 border-emerald-500/30`), la rúbrica con filtro inverso de contraste (`brightness-0 invert opacity-90`) y un badge animado (`animate-ping`) "Sello Verificado SRE".
  - **Bloqueo Inteligente:** Si no hay firma registrada en el perfil del redactor, el sistema bloquea de forma absoluta el envío y finalización del informe maestro, desplegando una alerta roja Glassmorphic e inyectando un botón directo al Perfil para la carga del recurso.
* **Blindaje Backend y Route Model Binding (RMB):**
  - **Identificadores Semánticos:** Las rutas web en `routes/web.php` y los controladores se migran de identificadores genéricos `{id}` a Route Model Binding estricto (`{masterReport}`).
  - **Autorización por Políticas:** El controlador `MasterReportController` ejecuta la verificación `$this->authorize('view', $masterReport)` en los métodos `show`, `edit` y `update`.
  - **FormRequests Robustos:** La creación y actualización se encapsulan en `StoreMasterReportRequest` y `UpdateMasterReportRequest`. El campo `estado` está blindado contra inyección desde el cliente.
  - **Inmutes de Finalización:** El campo transitorio `finalize` se consume únicamente en la capa del controlador y se excluye de `safeData()`.
* **Exportación de PDF de Alta Fidelidad y Rendimiento N+1:**
  - **Eager Loading de Asistencia:** Al compilar los datos en `aggregatePeriodData()`, el sistema carga previamente las relaciones (`trainings.attendances`), eliminando el problema N+1.
  - **Listado Detallado de Charla:** El PDF expande la columna de asistencia listando Nombre, DNI y firma de cada asistente.
* **Bóveda de Informes (Gestor Index):**
  - El botón "SOLICITAR INFORME MAESTRO" fue eliminado del header. Los informes se crean exclusivamente desde el plan mensual del técnico.
  - La paleta de estados en el Index usa: Amarillo = PENDIENTE (finalizado), Emerald = APROBADO, Amarillo Sutil = EN REDACCIÓN, Amarillo Brillante = SOLICITADO.
  - Un técnico con informe `aprobado` solo ve "DESCARGAR PDF APROBADO". Un gestor con informe `aprobado` solo ve "PDF FINAL". Ninguno puede editar un informe ya aprobado.
* **Flujo Seguro de Revisión y Aprobación de Gestores**:
  - **Botonera de Acción Flotante Global**: Barra táctica inteligente que evalúa rol y estado para renderizar:
    - *Aprobado*: Badge inmutable verde esmeralda "INFORME APROBADO Y REGISTRADO".
    - *Gestor + Finalizado*: Botones "APROBAR INFORME MAESTRO" y "OBSERVAR & PEDIR REVISIÓN".
    - *Técnico + Redactando*: Botones "GUARDAR BORRADOR" y "GENERAR INFORME FINAL".
    - *Otros (Lectura)*: Badge pulsante gris/zinc "MODO LECTURA ACTIVO".

### 4.9. Módulo de Súper Administración y Ventas (Gestión Global, Roles y Auditoría de Accesos)
* **Separación de Responsabilidades y Roles Globales**:
  - **Súper Administrador (`super_admin`)**: posee privilegios globales totales. Exclusivo para eliminar consultoras, administrar personal de consultoras y gestionar el staff global (crear, editar, blanquear contraseñas y desactivar súper administradores y vendedores). Su sidebar muestra "Dashboard Global", "Consultoras" y "Personal Global".
  - **Vendedor (`vendedor`)**: rol global orientado a ventas y soporte. Comparte el dashboard de consultoras, permitiendo dar de alta, configurar límites (gestores, técnicos, empresas) y cambiar el estado de suscripción de las consultoras. **Restringido**: no tiene acceso al borrado de consultoras, a la administración de usuarios internos de clientes, ni a la gestión global de staff. Su sidebar muestra "Dashboard Global" y "Consultoras".
* **Gestión de Consultoras Máster (CRUD):** Creación y edición de las entidades consultoras que agrupan a técnicos y empresas, permitiendo definir su CUIT, nombre, email, teléfono, dirección, estado de suscripción y límites máximos de licencias (técnicos, gestores, empresas).
* **Personalización de Branding y Marca:** Control de la identidad visual de cada consultora a través del formulario de edición. Permite cargar logotipos a almacenamiento persistente y seleccionar colores corporativos (Primario, Secundario, Acento) con color pickers nativos.
* **Control de Operadores y Staff (Activación/Suspensión):** Listado y gestión del staff de cada consultora (restringido a súper administradores). La baja de un operador consiste en su deshabilitación lógica (`estado = 'inactivo'`) para salvaguardar la trazabilidad de inspecciones e histórico transaccional, bloqueando su inicio de sesión de inmediato pero impidiendo la eliminación del registro físico.
* **Gestión de Personal Global:** Panel exclusivo de Súper Administradores para dar de alta, modificar y suspender a otros administradores y vendedores globales. Al igual que con los operadores de consultoras, se unifica la lógica para que la eliminación tradicional se sustituya por un cambio de estado a `'inactivo'`, previniendo la pérdida de datos históricos y logs de auditoría en el sistema.
* **Registro de Auditoría Inmutable (Audit Log)**:
  - Sistema de registro automático e inalterable que captura qué usuario (Súper Admin o Vendedor) realizó cambios de límites o suscripción en cada consultora y el diff exacto de campos. El modelo tiene bloqueados los métodos de modificación y eliminación para máxima fiabilidad. Muestra una línea de tiempo detallada con las diferencias en los campos modificados en la vista de edición.
* **Validación de Jerarquía en Operadores:** Control estricto a nivel de backend en la gestión de personal de consultoras máster. Se valida de forma segura que el operador editado o deshabilitado pertenezca a la consultora inyectada en la ruta, denegando el acceso (`403 Forbidden`) en caso de discrepancias o alteraciones del request.

### 4.10. Módulo Universal de Activos y Trazabilidad Dinámica con Códigos QR
* **Inventariado y Ficha de Activos (`Asset`)**: Abstracción universal para registrar equipamiento físico (`assets`) clasificado por tipos (`asset_types`) y asociado a una Sede. Admite atributos fijos (nombre, identificador único/patente, estado) y dinámicos guardados en formato JSON (`metadata`) para soportar múltiples industrias sin alterar el esquema físico de base de datos.
* **Trazabilidad en Inspección**: Vinculación opcional de cualquier checklist dinámico a un activo específico (`dynamic_inspections.asset_id`). Las preguntas del checklist pueden marcarse para actualizar automáticamente campos específicos en la metadata del activo al finalizar la inspección (ej: actualizar el kilometraje o el último conductor).
* **Identificación por Códigos QR (Objetos y Equipos)**: Lectura nativa de códigos QR para activos. El escaneo redirige a la URL única del activo, inicia automáticamente el checklist adecuado pre-asociado y bloquea entradas manuales para erradicar errores de digitación de patentes o códigos.
* **Identificación y Asistencia de Personal por QR**: Cada empleado dispone de una credencial QR que permite:
  - **Registro de asistencia instantáneo** en capacitaciones escaneando la credencial desde la cámara del técnico.
  - **Auditoría de EPP (Equipos de Protección Personal)** asociada directamente a la persona.
  - **Firma presencial rápida de supervisor** presentando su credencial QR frente al móvil del técnico, integrando validación GPS de cercanía.

### 4.11. Configurador de Tipos de Checklist Dinámicos (`Types/Index.jsx`)
* **Concepto:** El `AssetType` se renombra conceptualmente como **"Tipo de Checklist Dinámico"** en la interfaz de usuario. No es un tipo de activo físico en sentido clásico, sino un **unificador de atributos de ficha técnica** asociado a un checklist operativo existente.
* **Asociación de Checklist (Importación de Atributos):**
  - El gestor selecciona Categoría → Subcategoría → Checklist específico.
  - Al presionar **"Configurar Atributos"**, el sistema importa las preguntas marcadas con `vincular_a_metadata` del checklist seleccionado como atributos de la ficha técnica del activo.
  - **Reemplazo Exclusivo (No Acumulativo):** Si se cambia el checklist asociado y se reimportan atributos, el sistema limpia automáticamente los atributos importados del checklist anterior, conservando únicamente los campos creados manualmente por el gestor. Esto evita la acumulación de conjuntos de preguntas de múltiples checklists.
* **Inmutabilidad de Atributos de Checklist:**
  - Los atributos importados desde un checklist (`field._imported = true`) son de **solo lectura** en el configurador. El gestor no puede modificar su etiqueta, clave interna ni eliminarlos individualmente.
  - Tampoco se muestra el botón de borrado (✕) para estos atributos, protegiendo la trazabilidad histórica de PDFs e informes generados con esa estructura.
  - **Regla de Oro:** Los checklists son documentos controlados (equivalente a ISO 9001/45001). Si se requiere corregir un checklist, se debe crear una nueva versión del mismo y re-importar el tipo de activo de forma deliberada.
* **Atributos Manuales:**
  - El gestor puede agregar atributos propios del tipo de activo (independientes del checklist), definiendo etiqueta, clave interna (`name`), tipo de dato y reglas SRE.
  - Estos atributos sí son editables y pueden eliminarse.
* **Tipos de Atributos Soportados:** `text` (Texto), `number` (Número), `date` con `alert_days` (Fecha Vencimiento con Alerta), `date` sin alerta (Fecha Informativa).
* **Ámbito:** El `AssetType` pertenece a la `empresa_id`, siendo una plantilla compartida por todas las sedes de esa empresa. Los activos individuales (`Asset`) son los que se asocian a una `sede_id` específica. El checklist original puede seguir usándose normalmente en inspecciones de cualquier sede sin conflictos.
* **Selector de Tipo con Esquema Dark:** El `<select>` de tipo de dato utiliza `[color-scheme:dark]` y fondos sólidos en las opciones (`bg-[#16162a]`) para respetar la estética premium oscura en todos los navegadores.

---

## 5. Requisitos de Experiencia de Usuario y Diseño (Aesthetics)
Para garantizar la consistencia en el stack **React + Inertia + Tailwind CSS v4**, se deben respetar las siguientes directrices estéticas:

### 5.1. Directrices Visuales y Core HUD
1. **Inmersión Oscura:** El lienzo principal debe usar un fondo `#11111D` sólido con tarjetas y contenedores en `#1E1E2D` (con gradientes internos sutiles).
2. **Micro-interacciones en Botones (Tactical Pill):** Los botones deben contar con efectos hover dinámicos, shimmer degradado y escala reducida en clic (`active:scale-95`).
3. **Firma Digital Clara:** Las firmas digitalizadas por el técnico deben renderizarse con contraste invertido (`brightness-0 invert opacity-90`) en la interfaz oscura para simular un HUD técnico sin perder legibilidad al imprimir reportes.
4. **Cargas Transparentes (Loading States):** Spinner técnicos con animación de "escaneo de datos" HUD infinita al procesar documentos o cargar recursos en segundo plano.

### 5.2. 🎨 Identidad Visual y Colores por Pilar
Es obligatorio mantener la coherencia cromática y el diseño de marca para cada pilar del sistema:
1. **Inspecciones / Checklists:** Se representan estrictamente con la gama de color **Naranja Técnico / Naranja** (usando clases de Tailwind como `ep-primary`, `orange-500`, `amber-500`, etc.).
2. **Capacitaciones / Educación:** Se representan con la gama de color **Azul / Indigo** (usando clases como `blue-500`, `indigo-500`).
3. **Informes / Reportes:** Se representan con la gama de color **Rojo / Esmeralda / Amarillo** según su tipo específico de incidente/hallazgo/bitácora.

Todo elemento interactivo, formulario, pestaña, selector, badge o scrollbar relacionado a checklists debe utilizar sin excepciones su color asignado (Naranja).

### 5.3. 📱 Pautas de Responsividad y Navegación Adaptativa
Para evitar que las tablas anchas comprometan la navegación en notebooks convencionales y dispositivos móviles:
1. **Márgenes del Contenedor:** Ajustar de forma responsiva (`p-4 sm:p-6 lg:p-8 xl:p-12`) para optimizar el espacio útil en laptops.
2. **Paddings de Celdas y Ancho de Tabla:** Rediseñar rellenos de celdas a valores compactos (`px-4 lg:px-6 xl:px-8 py-6 lg:py-8`) para reducir la huella horizontal de la tabla y ajustarse a laptops comunes sin desbordar.
3. **Transición a Tarjetas Táctiles (Breakpoint < 1024px):** Todo listado tabular complejo debe cambiar a una vista de tarjetas verticales autoexplicativas e interactivas al bajar del breakpoint de `1024px` (`lg` en Tailwind). Las tablas anchas quedan ocultas (`lg:block hidden`) y las tarjetas toman el foco (`lg:hidden block`).
4. **Scrollbars de Marca Estilizados (`.custom-scrollbar`):** El contenedor horizontal de tablas desbordables (`overflow-x-auto`) debe llevar la clase `.custom-scrollbar` con estilos específicos que representen su pilar correspondiente:
   - *Inspecciones / Checklists / Matriz:* Scrollbar Naranja XamaSafe (`rgba(246, 146, 57, 0.3)`).
   - *Capacitaciones / Educación:* Scrollbar Índigo (`rgba(99, 102, 241, 0.3)`).
   - *Incidentes / Emergencias:* Scrollbar Rojo (`rgba(239, 68, 68, 0.3)`).
5. **Columna Fija Adaptable en Matrices (Annual Contract):** En vistas matriciales complejas, la primera columna fija (nombres de actividad) debe adaptar su tamaño de forma dinámica (`w-[140px] sm:w-[220px] md:w-[300px]`) y reducir sus paddings en móviles para permitir espacio suficiente a las columnas deslizables de los meses.

---

## 6. Requisitos Técnicos y Arquitectura

* **Monolito Híbrido:** Desarrollo ágil con Laravel en el backend e Inertia.js como puente para servir una SPA rápida basada en React, sin la sobrecarga de una API REST separada.
* **Seguridad / Multi-Tenancy:** Aislamiento estricto de los datos. Un usuario corporativo solo puede visualizar información relacionada a sus propias Empresas y Sedes. Las políticas de Laravel (`Policies`) auditan cada petición de Inertia.
* **Motor de PDF:** Laravel-DomPDF. Los archivos PDF generados se adaptan para impresión física, cambiando automáticamente la paleta oscura HUD a una paleta clara optimizada para tinta, manteniendo el branding y la visualización de las firmas.

---

*Documento creado: 20 Mayo 2026*  
*Última actualización: 7 Julio 2026 — v2.3: Módulo Tipos de Checklist Dinámicos, política de versionado de checklists, UI configurador schema-driven*  
*Propiedad del Proyecto XamaSafe*  
*Bajo especificaciones del Stack Inertia-React en la rama `feature-inertia-react`*  
