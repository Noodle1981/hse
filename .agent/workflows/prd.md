---
description: Documento de Requisitos de Producto (PRD) para XamaSafe
---

# 📋 Documento de Requisitos de Producto (PRD) — XamaSafe

## 1. Control del Documento
* **Nombre del Producto:** XamaSafe
* **Versión del PRD:** v2.2 (Stack React + Inertia.js)
* **Estado:** Activo / En Desarrollo
* **Última Actualización:** 4 Junio 2026
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
* **Máquina de 5 Estados del Informe Maestro (Backend-Driven):**
  El campo `accion` es calculado **exclusivamente en el backend** (`OperationController.php`) y transmitido al frontend. El frontend `MyAssignments.jsx` solo renderiza basándose en este campo. La lógica es:
  1. **`ejecutar`** — No existe ningún `MasterReport` para esta sede en el periodo seleccionado. Se muestra el botón naranja "+ EJECUTAR".
  2. **`reanudar`** — Existe un informe con `estado = 'en_redaccion'` sin observaciones de revisión. Se muestra el enlace índigo "REANUDAR" al Wizard de edición.
  3. **`corregir`** — Existe un informe con `estado = 'en_redaccion'` Y `observacion_revision` no nulo (devuelto por el gestor). Se muestra el enlace rojo pulsante "⚠ CORREGIR" con sombra de alerta neón.
  4. **`pendiente`** — El informe está en `estado = 'finalizado'` (enviado al gestor). Se muestra un badge ámbar de solo lectura "⏳ PENDIENTE GESTOR" sin ningún botón de acción.
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
  - La paleta de estados en el Index usa: Ámbar = PENDIENTE (finalizado), Emerald = APROBADO, Naranja = EN REDACCIÓN, Amarillo = SOLICITADO.
  - Un técnico con informe `aprobado` solo ve "DESCARGAR PDF APROBADO". Un gestor con informe `aprobado` solo ve "PDF FINAL". Ninguno puede editar un informe ya aprobado.
* **Flujo Seguro de Revisión y Aprobación de Gestores**:
  - **Botonera de Acción Flotante Global**: Barra táctica inteligente que evalúa rol y estado para renderizar:
    - *Aprobado*: Badge inmutable verde esmeralda "INFORME APROBADO Y REGISTRADO".
    - *Gestor + Finalizado*: Botones "APROBAR INFORME MAESTRO" y "OBSERVAR & PEDIR REVISIÓN".
    - *Técnico + Redactando*: Botones "GUARDAR BORRADOR" y "GENERAR INFORME FINAL".
    - *Otros (Lectura)*: Badge pulsante índigo "MODO LECTURA ACTIVO".

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
