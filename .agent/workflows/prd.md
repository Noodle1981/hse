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
* **HUD del Técnico (ActionSelector):** Panel modal interactivo de pantalla completa activable durante una inspección en progreso. Está dividido en dos secciones:
  1. **Tareas del Plan (Asignadas):** Muestra las tareas recomendadas y planificadas contractualmente para el mes en curso.
  2. **Acciones Libres (Ad-Hoc):** Permite iniciar tareas libres o espontáneas para emergencias o imprevistos:
     - **Checklist Ad-Hoc:** Despliega plantillas de checklist organizadas en un acordeón interactivo y colapsable clasificado por categorías de seguridad.
     - **Capacitación Express:** Formulario en línea de alta velocidad para registrar tema, descripción y duración.
     - **Reportar Incidente / Cargar Hallazgo:** Enlaces directos a formularios de alta.
* **Sesión de Inspección en Progreso:** Toda actividad técnica ocurre bajo una sesión de inspección iniciada ("Nueva Inspección"). La pantalla de progreso muestra un listado de tareas completadas de forma continua, agrupadas por plantilla e identificadas mediante **Counter Badges** de acento neón flotantes en las esquinas superiores (`absolute -top-3 -right-3`, `rounded-2xl`, tipografía `font-black`).
* **Historial de Inspecciones y Descarga:** El panel "Mis Asignaciones" funciona como un tablero de compromisos comerciales anuales. Se reemplazan los botones de ejecución directa en la tabla por un acceso de **"Ver Historial"** que redirige al historial general de inspecciones pre-filtrado por el `checklist_id` del registro, facilitando la consulta y descarga rápida de todos los PDFs firmados generados para esa tarea.

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
* **Asistencia e Identificación Completa (DNI):** El formulario de asistencia incluye Nombre, Puesto y **DNI / Identificación obligatorio**, persistiendo los asistentes y sus firmas digitales serializados en la base de datos (columna `attendees` de la tabla `visit_trainings`). Las tarjetas en la consola reflejan de forma clara el DNI y rol del participante (ej. `OPERARIO • DNI: 95827361`).
* **Bitácora y Editor de Notas Técnica en Caliente:** Permite al técnico editar y guardar en tiempo real el temario desarrollado (`description`) y observaciones de campo (`notes`) directamente sobre columnas estructuradas e independientes en la tabla de base de datos `visit_trainings` de la visita en progreso.
* **Finalización de Charla y Modal Premium:** La sesión se da por finalizada mediante un comando táctil en cabecera que despliega un modal React de diseño premium con fondos desenfocados en blur e íconos en 3D, actualizando el estado de la capacitación a `'completed'` para sincronizar de inmediato su aparición en la consola cronológica del HUD.
* **Certificación:** Generación de un PDF de capacitación con validez legal conteniendo las firmas digitalizadas.
  * **Alternancia de Roles en Capacitación:** Las firmas y flujos de firmas de capacitación detectan dinámicamente si el rol activo del operador es de técnico, redirigiéndolo limpiamente al finalizar de vuelta a su HUD de sesión en progreso.

### 4.6. Módulo de Contratos y PPD (Programa de Prevención de Daños)
* **Control de Ciclos:** Seguimiento de contratos anuales activos por empresa.
* **Alertas de Vencimiento:** Avisos visuales y de control de visitas requeridas en base al acuerdo de servicio (ej. frecuencia mensual, trimestral).

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
