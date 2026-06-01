---
description: Documento de Requisitos de Producto (PRD) para XamaSafe
---

# 📋 Documento de Requisitos de Producto (PRD) — XamaSafe

## 1. Control del Documento
* **Nombre del Producto:** XamaSafe
* **Versión del PRD:** v2.1 (Stack React + Inertia.js)
* **Estado:** Activo / En Desarrollo
* **Última Actualización:** 20 Mayo 2026
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
| **Gestor (Administrador / Consultor)** | Administra el sistema desde su laptop/escritorio. | Configurar checklists, supervisar el rendimiento de los técnicos en tiempo real, gestionar clientes/sedes, emitir reportes anuales de contratos y descargar PDFs consolidados. |
| **Técnico (Inspector de Terreno)** | Opera en campo utilizando dispositivos móviles o tablets. | Registrar visitas, certificar presencia mediante coordenadas GPS, completar checklists interactivos con firmas digitales y capturar hallazgos con registro fotográfico. |
| **Cliente Corporativo (Lector)** | Visualiza e inspecciona el estado de sus sedes. | Consultar las constancias de visitas, ver estadísticas de cumplimiento y descargar certificados oficiales de seguridad y capacitaciones. |

---

## 4. Módulos y Requisitos Funcionales

### 4.1. Core Engine: Checklists Dinámicos e Inspecciones
* **Generación de Plantillas:** Permite a los Gestores definir plantillas de checklists con categorías y tipos de preguntas variados (texto, numérico, opción múltiple, booleano, adjunto de fotos).
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
* **Reporte Rápido:** Durante una inspección, el técnico puede levantar un "Hallazgo" adjuntando una descripción del incidente, criticidad (Baja, Media, Alta, Crítica) y evidencia fotográfica.
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

### 4.7. Control y Gestión de Firmas Digitales y Aprobación Segura
* **Tipología de Firmas del Sistema:**
  * **Firma Tipo A (Firma de Perfil / Identidad Permanente):**
    * **Definición:** Es la firma oficial y permanente que identifica a un Técnico o Usuario en el sistema.
    * **Flujo de Captura:** Se realiza desde la pantalla de Perfil (`/perfil`) usando un canvas de dibujo React.
    * **Almacenamiento Físico:** Al subirla, el backend decodifica la cadena Base64, genera un archivo físico `.png` y lo almacena en `storage/app/public/signatures/` con un nombre único aleatorio para preservar el historial legal. En la base de datos se guarda la ruta relativa al archivo (no el Base64) en el campo `users.firma`.
    * **Auditoría Obligatoria:** No se aplica inmediatamente. Pasa a estado pendiente (`firma_status = 'pending'`) y se guarda temporalmente en `firma_temp`.
  * **Firma Tipo B (Firma Transaccional / De Visita o Planilla):**
    * **Definición:** Firma única, temporal y dinámica capturada en caliente para certificar un evento específico (asistencia a capacitación, cierre de checklist, o constancia de visita).
    * **Almacenamiento:** Mapeadas como Base64 serializadas o strings codificados en caliente asociados de forma directa a registros de la base de datos (como la firma del cliente al cerrar una constancia, o las firmas de los operarios en la tabla `training_attendances`).

* **Política de Firma Pendiente (Auditoría de Identidad):**
  * Cuando un Técnico actualiza su firma digital desde su panel de Perfil (`/perfil`), esta entra en estado de aprobación pendiente (`firma_status = 'pending'`) y la firma propuesta se almacena en la ruta temporal `firma_temp`.
  * **Auditoría por Gestor (Panel Táctico):** Los Gestores visualizan las firmas pendientes en el listado maestro de identidades (`Users.jsx`). El componente `PendingSignaturesList.jsx` destaca las firmas pendientes con contrastes oscuros inmersivos y les permite tomar una acción interactiva:
    * **Aprobar:** Copia físicamente el archivo de la firma temporal a la ruta permanente final, cambia el estado a `approved`, actualiza la firma activa en `users.firma` y limpia `firma_temp`.
    * **Rechazar:** Elimina físicamente el archivo temporal en el storage y marca `firma_status = 'rejected'` para notificar visualmente al técnico y permitirle reintentar.
  * **Visor de Firmas Oficiales:** Se integra un modal visor Glassmorphic en el listado de usuarios que permite a los Gestores visualizar de forma inmediata las firmas aprobadas vigentes de cada técnico a través de un botón interactivo de inspección visual ("ojito").

### 4.8. Consolidación de Informes Maestros y Hardening Arquitectónico (SRE Audit)
* **Flujo de Trabajo Intacto e Ininterrumpido:** Se respeta al 100% el flujo de inicio desde *Mis Asignaciones* (pestaña Informes) mediante el botón interactivo de "Ejecutar", heredando limpiamente los parámetros contextuales de `sede_id` y `periodo` hacia el Wizard de Redacción.
* **Erradicación Absoluta de Firmas Manuales (Canvas):** No se permite en absoluto el trazo manual en pantalla dentro del Informe Maestro. El sistema adopta como fuente de verdad única e inmutable la **Firma Tipo A (Rúbrica de Perfil)** cargada en el perfil del técnico certificante (`report.tecnico.firma`). 
* **Consola de Redacción Wizard y Validación Táctica:**
  - **Firma Autorizada Vinculada:** Si el técnico posee su firma de perfil activa y aprobada, el Wizard renderiza una tarjeta de validación de solo lectura con estilo Glassmorphic Ultra-Premium DARK, destacada con un glow verde de seguridad (`bg-emerald-500/10 border-emerald-500/30`), la rúbrica con filtro inverso de contraste (`brightness-0 invert opacity-90`) y un badge animado (`animate-ping`) "Sello Verificado SRE".
  - **Bloqueo Inteligente:** Si no hay firma registrada en el perfil del redactor, el sistema bloquea de forma absoluta el envío y finalización del informe maestro, desplegando una alerta roja Glassmorphic e inyectando un botón directo al Perfil para la carga del recurso.
* **Blindaje Backend y Route Model Binding (RMB):**
  - **Identificadores Semánticos:** Las rutas web en `routes/web.php` y los controladores se migran de identificadores genéricos `{id}` a Route Model Binding estricto (`{masterReport}`). Laravel resuelve la existencia y validez de la instancia del informe en base de datos al resolver la ruta.
  - **Autorización por Políticas:** El controlador `MasterReportController` ejecuta la verificación en caliente `$this->authorize('update', $masterReport)` al inicio de la edición y actualización para prevenir manipulación cruzada de reportes.
  - **FormRequests Robustos:** La creación y actualización se encapsulan en `StoreMasterReportRequest` y `UpdateMasterReportRequest` respectivamente. El método `safeData()` y la sanitización en backend bloquean y descartan de forma absoluta cualquier intento de inyección en campos de auditoría (`estado`, `user_id`, `sede_id`, `solicitado_por`).
  - **Inmutes de Finalización:** El campo temporal/transitorio de acción `finalize` se procesa y consume únicamente en la capa del controlador para modificar el `estado` a `'finalizado'`, excluyéndose limpiamente de los datos inyectados al modelo mediante `safeData()` para evitar errores de persistencia en columnas no existentes.
* **Exportación de PDF de Alta Fidelidad y Rendimiento N+1:**
  - **Eager Loading de Asistencia:** Al compilar los datos en `aggregatePeriodData()`, el sistema carga previamente las relaciones (`trainings.attendances`), eliminando la sobrecarga y latencia del problema de consultas N+1 en bases de datos masivas.
  - **Listado Detallado de Charla:** El PDF del reporte maestro expande de forma detallada la columna de asistencia de cada capacitación de ese mes, listando el Nombre completo, DNI y la firma transaccional individual en Base64 o path físico de cada asistente.
  - **Corrección de Columnas:** Se corrige el campo de la base de datos para incidentes de `$inc->tipo_evento` a su nombre de columna correcto y nativo `$inc->tipo`.
* **Clasificación Estricta de Actividades Ad-Hoc (Fuera de Plan):** 
  - **Evitar Falsos Ad-Hoc:** Si un checklist o capacitación se completa varias veces dentro de un periodo de plan, la primera ejecución satisface la meta planificada. Las ejecuciones adicionales (repeticiones) se identifican por plantilla (`dynamic_checklist_id` o `registry_code`) o tema/título, y **se excluyen estrictamente del listado de actividades Ad-Hoc / Fuera del Plan**.
  - **Verdadero Ad-Hoc:** La sección de *Inspecciones Técnicas No Rutinarias* (Ad-Hoc) del reporte solo lista aquellas actividades que fueron ejecutadas de forma totalmente libre e improvisada, es decir, que **no tienen ninguna relación ni coincidencia en absoluto** con lo programado en el `AnnualPlan` de la Sede.
* **Flujo Seguro de Revisión y Aprobación de Gestores**:
  - **Máquina de Estados de Auditoría**: El ciclo de vida de los reportes se gestiona a través de transiciones de estados seguras controladas por roles:
    - *Técnico*: Completa el borrador y lo firma utilizando su rúbrica inmutable del perfil, cambiando el estado a `'finalizado'`.
    - *Gestor*: Una vez en `'finalizado'`, el Wizard del Técnico se bloquea por completo (`readOnly`), y el Gestor toma el control. El Gestor puede escribir una nota de auditoría (`observacion_revision`) y ejecutar una de dos acciones:
      - **Aprobar**: Cambia el estado a `'aprobado'` y limpia automáticamente cualquier nota de revisión del modelo. El reporte queda permanentemente archivado y de solo lectura.
      - **Observar / Devolver**: Guarda el comentario del Gestor y revierte el estado a `'en_redaccion'`, desbloqueando los campos y notificando al técnico.
  - **Bandeja de Entrada en Wizard (Técnico)**: Al entrar a un reporte observado, el Técnico visualiza una prominente tarjeta Glassmorphic roja con la observación de auditoría del Gestor justo al inicio del Wizard.
  - **Sección 6 — Evaluación del Gestor**: Módulo inyectado en el pie de página de redacción, visible únicamente para Gestores/Administradores y solo cuando el reporte se encuentra en estado `'finalizado'`.
  - **Botonera de Acción Flotante Global**: Barra táctica inteligente que evalúa en caliente el rol de usuario y estado del informe para renderizar triggers contextualmente correctos:
    - *Aprobado*: Badge inmutable verde esmeralda con glow (`INFORME APROBADO Y REGISTRADO`).
    - *Gestor + Finalizado*: Botones de `APROBAR INFORME MAESTRO` y `OBSERVAR & PEDIR REVISIÓN`.
    - *Técnico + Redactando*: Botones de `GUARDAR BORRADOR` y `GENERAR INFORME FINAL`.
    - *Otros Estados (Lectura)*: Badge pulsante azul/índigo con leyenda de `MODO LECTURA ACTIVO`.

---

## 5. Requisitos de Experiencia de Usuario y Diseño (Aesthetics)
Para garantizar la consistencia en el stack **React + Inertia + Tailwind CSS v4**, se deben respetar las siguientes directrices estéticas:

1. **Inmersión Oscura:** El lienzo principal debe usar un fondo `#11111D` sólido con tarjetas y contenedores en `#1E1E2D` (con gradientes internos sutiles).
2. **Micro-interacciones en Botones (Tactical Pill):** Los botones deben contar con efectos hover dinámicos, shimmer degradado y escala reducida en clic (`active:scale-95`).
3. **Firma Digital Clara:** Las firmas digitalizadas por el técnico deben renderizarse con contraste invertido (`brightness-0 invert opacity-90`) en la interfaz oscura para simular un HUD técnico sin perder legibilidad al imprimir reportes.
4. **Cargas Transparentes (Loading States):** Spinner técnicos con animación de "escaneo de datos" HUD infinita al procesar documentos o cargar recursos en segundo plano.

---

## 6. Requisitos Técnicos y Arquitectura

* **Monolito Híbrido:** Desarrollo ágil con Laravel en el backend e Inertia.js como puente para servir una SPA rápida basada en React, sin la sobrecarga de una API REST separada.
* **Seguridad / Multi-Tenancy:** Aislamiento estricto de los datos. Un usuario corporativo solo puede visualizar información relacionada a sus propias Empresas y Sedes. Las políticas de Laravel (`Policies`) auditan cada petición de Inertia.
* **Motor de PDF:** Laravel-DomPDF. Los archivos PDF generados se adaptan para impresión física, cambiando automáticamente la paleta oscura HUD a una paleta clara optimizada para tinta, manteniendo el branding y la visualización de las firmas.

---

*Documento creado: 20 Mayo 2026*  
*Propiedad del Proyecto XamaSafe*  
*Bajo especificaciones del Stack Inertia-React en la rama `feature-inertia-react`*  
