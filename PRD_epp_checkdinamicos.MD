# Product Requirements Document (PRD)
## EP-Back - Plataforma SaaS de Gestión H&S para Consultoras y Técnicos

**Versión:** 2.0.0.0  
**Fecha:** Enero 2026  
**Autor:** Equipo EP-Back  
**Estado:** Activo

---

## 📋 Tabla de Contenidos

1. [Contexto y Visión](#1-contexto-y-visión)
2. [Restricciones Técnicas](#2-restricciones-técnicas)
3. [Requerimientos Funcionales](#3-requerimientos-funcionales)
4. [Requerimientos No Funcionales](#4-requerimientos-no-funcionales)

---

## 1. Contexto y Visión

### 1.1 Problema a Resolver

**Consultoras de Higiene y Seguridad** y **Técnicos Independientes** enfrentan desafíos para gestionar sus carteras de clientes de manera eficiente y profesional:

- **Gestión Fragmentada**: Utilizan herramientas dispares (Excel, WhatsApp, Email) para coordinar múltiples empresas, lo que dificulta la escalabilidad.
- **Falta de Identidad**: Entregan reportes genéricos o manuales que no refuerzan su marca profesional ante sus clientes.
- **Trabajo en campo sin conectividad**: Sus técnicos necesitan completar inspecciones en sitios remotos sin depender de internet.
- **Documentación manual ineficiente**: Pérdida de tiempo transcribiendo formularios de papel a digital.
- **Trazabilidad deficiente**: Dificultad para demostrar a sus clientes el cumplimiento normativo y seguimiento de hallazgos.

### 1.2 Solución Propuesta

**EP-Back** es una **Plataforma SaaS Multi-tenant (B2B)** diseñada para que **Consultoras de H&S** puedan gestionar integralmente sus operaciones y ofrecer un servicio premium a sus clientes.

El sistema funciona bajo un modelo de **"Espacios" (Tenants)**, donde cada Consultora adquiere su propio entorno privado para:

- ✅ **Centralizar Gestión**: Administrar múltiples empresas clientes y sus sedes desde un panel unificado.
- ✅ **Checklists dinámicos**: Crear sus propios estándares de inspección personalizados.
- ✅ **Equipo de Trabajo**: Asignar tareas a su staff de técnicos.
- ✅ **Trabajo en campo**: App para captura de datos, firmas y fotos in situ.
- ✅ **Marca Blanca (TBD)**: Generación de reportes profesionales.
- ✅ **Matriz de riesgos**: Evaluación estandarizada para todos sus clientes.
- ✅ **Trazabilidad**: Historial completo para auditorías.

### 1.3 Usuarios Objetivo

| Rol | Descripción | Responsabilidades | Alcance |
|-----|-------------|-------------------|---------|
| **Super Admin** | Dueño de la Plataforma (EP-Back) | - Gestión de tenants (Consultoras)<br>- Gestión de planes y suscripciones<br>- Monitoreo global del sistema<br>- Soporte técnico nivel 2 | Global (Todos los tenants) |
| **Admin Consultora** | Cliente del SaaS (Dueño del Espacio) | - Configuración de su empresa/marca<br>- Alta de sus Clientes (Empresas) y Sedes<br>- Gestión de su staff (Técnicos)<br>- Creación de checklist maestros<br>- Visualización de reportes de todos sus clientes | Tenant (Su espacio privado) |
| **Técnico** | Staff de la Consultora | - Ejecución de inspecciones<br>- Registro de capacitaciones<br>- Entrega de EPP<br> - Solo ve asignaciones de las empresas que gestiona | Datos asignados dentro del Tenant |

### 1.4 Objetivos de Negocio

1. **Digitalizar el 100% de las operaciones en campo** eliminando formularios en papel
2. **Reducir tiempo de generación de reportes en 80%** mediante automatización de PDFs
3. **Aumentar productividad de técnicos en 50%** con wizards guiados y formularios dinámicos
4. **Mejorar trazabilidad al 100%** con historial completo de actividades
5. **Facilitar auditorías** con reportes profesionales generados automáticamente

### 1.5 Alcance del Producto

#### ✅ Dentro del Alcance (V2.0.0.0)

**Módulos Implementados:**
1. **Checklists Dinámicos** - Constructor visual, asignaciones, inspecciones con firmas
2. **Gestión de Empresas y Sedes** - CRUD completo, asignación de técnicos
3. **Capacitaciones** - Presenciales y virtuales, registro de asistencia, certificados PDF
4. **Entrega de EPP** - Catálogo, importación Excel de empleados, wizard de entrega, constancias
5. **Matriz de Riesgos** - Diseño de matrices, evaluación 5×5, controles, plan anual
6. **Hallazgos e Incidentes** - Registro con fotos, seguimiento, clasificación por criticidad
7. **Constancias de Visita** - Vinculación de actividades, firmas, generación PDF
8. **Usuarios y Roles** - Sistema de permisos, asignación a sedes
9. **Categorías** - Organización jerárquica de checklists
10. **Dashboards** - KPIs personalizados por rol

**Características Técnicas:**
- TALL Stack (Tailwind, Alpine, Livewire, Laravel 12)
- 41 componentes Livewire
- 46 tablas en base de datos
- Generación automática de PDFs (DomPDF + Spatie PDF)
- Importación Excel (Maatwebsite Excel)
- Firmas digitales (signature_pad.js)
- API REST con Laravel Sanctum

#### ❌ Fuera del Alcance (Roadmap Futuro)

- **Modo offline completo** (PWA con Service Workers) - Sprint 2
- **Aplicación móvil nativa** (iOS/Android)
- **Firma digital certificada** (PKCS#7) - Sprint 1
- **Integración con ERP externos**
- **Inteligencia artificial** (autocompletado, análisis de imágenes) - Sprint 5
- **Portal para clientes empresariales** - Sprint 4
- **Blockchain para trazabilidad**

---

## 2. Restricciones Técnicas

### 2.1 Stack Tecnológico

#### Backend
- **Framework:** Laravel ^12.0
- **PHP:** 8.2+
- **Base de Datos:** 
  - SQLite (desarrollo)
  - MySQL 8.0+ / PostgreSQL 14+ (producción recomendado)
- **ORM:** Eloquent
- **Autenticación:** 
  - Laravel Sessions (Web)
  - Laravel Sanctum (API tokens)

#### Frontend (TALL Stack)
- **Livewire:** 3.7 (componentes reactivos sin JavaScript)
- **Tailwind CSS:** 3.x (utility-first CSS via CDN)
- **Alpine.js:** 3.x (interactividad mínima, incluido en Livewire)
- **Laravel Blade:** Motor de plantillas

#### Generación de Documentos
- **PDFs:** 
  - barryvdh/laravel-dompdf (v3.1) - Generación principal
  - spatie/laravel-pdf (v1.5) - Alternativa para casos complejos
- **Excel:**
  - maatwebsite/excel (v3.1) - Importación/exportación
  - phpoffice/phpspreadsheet (v1.30)
  - shuchkin/simplexlsx (v1.1) - Lectura ligera

#### Firmas Digitales
- **signature_pad.js** (NPM) - Captura de firmas en canvas
- **Almacenamiento:** Base64 en base de datos

### 2.2 Navegadores Soportados

| Navegador | Versión Mínima | Notas |
|-----------|----------------|-------|
| Chrome | 100+ | ✅ Recomendado para técnicos |
| Firefox | 95+ | ✅ Soportado |
| Safari | 15+ | ⚠️ Limitaciones en Alpine.js |
| Edge | 100+ | ✅ Soportado |
| Mobile Safari | iOS 14+ | ✅ Responsive para tablets en campo |
| Chrome Mobile | Android 10+ | ✅ Responsive para tablets en campo |

> **Nota:** El sistema está optimizado para uso en tablets (10-13") que los técnicos llevan al campo.

### 2.3 Sistemas Operativos

#### Servidor
- **Linux:** Ubuntu 22.04 LTS, Debian 11+ (recomendado)
- **Windows Server:** 2019+ (soportado)
- **Docker:** Opcional para contenedorización

#### Cliente
- **Cualquier SO** con navegador moderno
- **Diseño responsive:** Mobile-first para tablets

### 2.4 Integraciones Obligatorias

**Ninguna integración externa obligatoria en V2.0.0.0**

El sistema es completamente autónomo y no depende de servicios externos para su funcionamiento core.

#### Integraciones Opcionales/Futuras
- **Email:** SMTP para envío de constancias y notificaciones
- **API REST:** Endpoints disponibles para integraciones custom
- **Storage externo:** S3/MinIO para almacenamiento de archivos (recomendado en producción)

### 2.5 Requisitos de Infraestructura

#### Mínimos (1-3 técnicos, <10 empresas)
- **CPU:** 2 cores
- **RAM:** 4 GB
- **Almacenamiento:** 20 GB SSD
- **Ancho de banda:** 10 Mbps

#### Recomendados (5-10 técnicos, 20-50 empresas)
- **CPU:** 4 cores
- **RAM:** 8 GB
- **Almacenamiento:** 50 GB SSD
- **Ancho de banda:** 50 Mbps
- **Cache:** Redis (opcional)

#### Escalado (10+ técnicos, 50+ empresas)
- **CPU:** 8+ cores
- **RAM:** 16+ GB
- **Almacenamiento:** 100+ GB SSD (o S3 para archivos)
- **Ancho de banda:** 100+ Mbps
- **Cache:** Redis (obligatorio)
- **CDN:** Para assets estáticos

---

## 3. Requerimientos Funcionales

### 3.1 Épica: Gestión SaaS y Multitenancy

#### RF-000: Gestión de Tenants (Super Admin)
- **Prioridad:** Crítica
- **Descripción:** El Super Admin debe poder gestionar el ciclo de vida de las consultoras (Tenants)
- **Criterios de Aceptación:**
  - Crear nuevo Tenant (Nombre Consultora, Dominio/Slug, Admin Inicial)
  - Activar/Suspender acceso al Tenant
  - Dashboard global (Total Tenants, Usuarios Totales)

### 3.2 Épica: Gestión de Usuarios y Roles (Tenant)

#### RF-001: Sistema de Autenticación Dual
- **Prioridad:** Crítica
- **Descripción:** El sistema debe soportar autenticación web (sesiones) y API (tokens)
- **Criterios de Aceptación:**
  - Login web con email/password (Laravel Sessions)
  - Login API con email/password retorna token Sanctum
  - Recuperación de contraseña por email
  - Logout seguro con invalidación de sesión/token
  - Timeout de sesión: 2 horas de inactividad

#### RF-002: Sistema de Roles y Permisos
- **Prioridad:** Crítica
- **Descripción:** Control de acceso basado en roles (RBAC) adaptado a la estructura SaaS
- **Roles:**
  1. **Super Admin**: Acceso a panel de gestión SaaS. Sin acceso a datos de negocio de los tenants (salvo soporte).
  2. **Admin Consultora**: Control total sobre su espacio.
  3. **Técnico**: Rol operativo restringido.
- **Criterios de Aceptación:**
  - Middleware `role:SuperAdmin` protege rutas de gestión SaaS
  - Middleware `tenant.context` asegura que cada request opera sobre el tenant correcto
  - Admin Consultora puede crear técnicos dentro de su tenant
  - Ver matriz completa de permisos actualizada

#### RF-003: Asignación de Técnicos a Sedes
- **Prioridad:** Alta
- **Descripción:** Los administradores deben poder asignar técnicos a múltiples sedes
- **Criterios de Aceptación:**
  - Tabla pivot `sede_users` (many-to-many)
  - Un técnico puede estar asignado a múltiples sedes
  - Una sede puede tener múltiples técnicos
  - Filtrado automático de datos por sedes asignadas

---

### 3.3 Épica: Checklists Dinámicos

#### RF-004: Constructor Visual de Checklists
- **Prioridad:** Crítica
- **Descripción:** Los administradores deben poder crear checklists personalizados con secciones y preguntas
- **Tipos de Preguntas Soportados:**
  1. Texto corto
  2. Texto largo (textarea)
  3. Selección única (radio)
  4. Selección múltiple (checkbox)
  5. Archivo adjunto
  6. Fecha
  7. Número
  8. Firma digital
- **Criterios de Aceptación:**
  - Crear checklist con título, descripción, categoría y subcategoría
  - Agregar secciones para organizar preguntas
  - Agregar preguntas con tipo, opciones (JSON), orden, obligatoriedad
  - Drag & drop para reordenar preguntas
  - Duplicar checklists existentes
  - Activar/desactivar checklists
  - Guardar como borrador

#### RF-005: Sistema de Asignaciones
- **Prioridad:** Alta
- **Descripción:** Los administradores deben poder asignar checklists a técnicos específicos
- **Criterios de Aceptación:**
  - Asignar checklist + técnico + empresa + sede + fecha programada
  - Estados: `pending`, `in_progress`, `completed`
  - Notificación al técnico (opcional)
  - Vista de "Mis Asignaciones" para técnicos
  - Filtros por estado, fecha, sede

#### RF-006: Wizard de Inspección
- **Prioridad:** Crítica
- **Descripción:** Los técnicos deben poder completar inspecciones asignadas con wizard paso a paso
- **Criterios de Aceptación:**
  - Wizard guiado por secciones
  - Validación de campos obligatorios
  - Captura de firma del técnico (canvas)
  - Captura de firma del responsable de sede (canvas)
  - Opción "firma no disponible" con justificación
  - Guardar como borrador (continuar después)
  - Completar y enviar inspección
  - Generación automática de PDF al completar

#### RF-007: Historial de Inspecciones
- **Prioridad:** Alta
- **Descripción:** Visualización de todas las inspecciones completadas con filtros
- **Criterios de Aceptación:**
  - Tabla con: checklist, técnico, sede, fecha, estado
  - Filtros: fecha, técnico, sede, checklist, estado
  - Vista detallada de respuestas
  - Descarga de PDF
  - Administradores ven todas, técnicos solo las propias

---

### 3.4 Épica: Gestión de Empresas y Sedes

#### RF-008: CRUD de Empresas
- **Prioridad:** Alta
- **Descripción:** Gestión completa de empresas clientes
- **Criterios de Aceptación:**
  - Crear empresa: nombre, razón social, CUIT (único)
  - Editar empresa
  - Eliminar empresa (validar que no tenga sedes activas)
  - Listar con búsqueda y paginación
  - Solo (Tenant) administradores

#### RF-009: CRUD de Sedes
- **Prioridad:** Alta
- **Descripción:** Gestión de sedes/sucursales por empresa
- **Criterios de Aceptación:**
  - Crear sede vinculada a empresa
  - Datos: nombre, dirección, correo, teléfono, tipo, localidad
  - Hasta 3 responsables con nombre, cargo, email
  - Editar y eliminar sedes
  - Asignar técnicos a sede
  - Listar sedes por empresa

---

### 3.5 Épica: Capacitaciones

#### RF-010: Gestión de Capacitaciones
- **Prioridad:** Alta
- **Descripción:** Programación de capacitaciones presenciales y virtuales
- **Criterios de Aceptación:**
  - Crear capacitación: título, descripción, tipo (presencial/virtual), fecha, duración, instructor
  - Vincular a empresa y sede
  - Asignar técnico responsable
  - Estados: `pending`, `completed`
  - Editar y eliminar capacitaciones

#### RF-011: Registro de Asistencia
- **Prioridad:** Alta
- **Descripción:** Toma de asistencia durante capacitaciones
- **Criterios de Aceptación:**
  - Agregar asistentes: nombre, DNI, firma digital
  - Vincular empleados existentes (opcional)
  - Editar y eliminar asistentes
  - Generar certificado PDF con lista de asistentes
  - Técnicos pueden tomar asistencia en campo

---

### 3.6 Épica: Entrega de EPP

#### RF-012: Catálogo de EPP
- **Prioridad:** Media
- **Descripción:** Gestión del catálogo de elementos de protección personal
- **Criterios de Aceptación:**
  - CRUD de items EPP: nombre, descripción, tipo, vida útil (meses), código norma
  - Activar/desactivar items
  - Solo administradores

#### RF-013: Gestión de Empleados
- **Prioridad:** Alta
- **Descripción:** Registro de empleados por empresa/sede
- **Criterios de Aceptación:**
  - CRUD empleados: nombre, apellido, DNI, CUIL, puesto
  - Vincular a empresa y sede
  - Importación masiva desde Excel
  - Activar/desactivar empleados
  - Administradores y técnicos pueden gestionar

#### RF-014: Wizard de Entrega de EPP
- **Prioridad:** Alta
- **Descripción:** Proceso guiado para registrar entregas de EPP en campo
- **Criterios de Aceptación:**
  - Seleccionar empleado
  - Seleccionar items EPP con cantidad
  - Tipo de entrega: inicial, reposición, renovación
  - Captura de firma del empleado
  - Observaciones
  - Generación automática de constancia PDF
  - Historial de entregas por empleado

---

### 3.7 Épica: Matriz de Riesgos

#### RF-015: Diseño de Matrices de Riesgo
- **Prioridad:** Media
- **Descripción:** Creación de matrices personalizadas por sede
- **Criterios de Aceptación:**
  - Crear matriz: título, descripción, área, proceso
  - Vincular a empresa y sede
  - Agregar riesgos: peligro, tipo, consecuencia
  - Reordenar riesgos
  - Duplicar matrices
  - Estados: `draft`, `active`, `archived`

#### RF-016: Evaluación de Riesgos (5×5)
- **Prioridad:** Alta
- **Descripción:** Evaluación de riesgos con metodología probabilidad × impacto
- **Criterios de Aceptación:**
  - Evaluar probabilidad (1-5)
  - Evaluar impacto/severidad (1-5)
  - Cálculo automático de nivel de riesgo (P × I)
  - Clasificación: trivial, tolerable, moderado, importante, intolerable
  - Controles existentes y adicionales
  - Medidas de control con responsable y plazo
  - Dashboard de riesgos con métricas

#### RF-017: Plan Anual de Actividades
- **Prioridad:** Media
- **Descripción:** Planificación anual de actividades vinculadas a matriz de riesgos
- **Criterios de Aceptación:**
  - Dashboard calendario por sede
  - Agregar actividades por mes
  - Marcar/desmarcar cumplimiento mensual
  - Cálculo de % cumplimiento
  - Exportar PDF del plan anual
  - Solo administradores editan, técnicos visualizan

---

### 3.8 Épica: Hallazgos e Incidentes

#### RF-018: Registro de Hallazgos
- **Prioridad:** Alta
- **Descripción:** Documentación de hallazgos de seguridad detectados en campo
- **Criterios de Aceptación:**
  - Crear hallazgo: título, descripción, ubicación específica
  - Criticidad: bajo, medio, alto, crítico
  - Estado: pendiente, en_proceso, resuelto, verificado, cerrado
  - Plazo de corrección
  - Responsable de corrección
  - Vincular a riesgo (opcional)
  - Subir fotos de evidencia (múltiples)
  - Origen polimórfico (desde inspección, evaluación de riesgo, etc.)

#### RF-019: Seguimiento de Hallazgos
- **Prioridad:** Alta
- **Descripción:** Trazabilidad del ciclo de vida de hallazgos
- **Criterios de Aceptación:**
  - Agregar seguimientos con descripción y cambio de estado
  - Historial completo de seguimientos
  - Notificación de cambios (opcional)
  - Administradores gestionan todos, técnicos solo los propios
  - Generar PDF de reporte de hallazgo

#### RF-020: Registro de Incidentes
- **Prioridad:** Media
- **Descripción:** Documentación de incidentes de seguridad
- **Criterios de Aceptación:**
  - Crear incidente: título, descripción, gravedad
  - Vincular a hallazgo (opcional)
  - Fecha y hora del incidente
  - Acciones tomadas
  - Solo administradores

---

### 3.9 Épica: Constancias de Visita

#### RF-021: Generación de Constancias
- **Prioridad:** Alta
- **Descripción:** Documentación de todas las actividades realizadas en una visita técnica
- **Criterios de Aceptación:**
  - Crear constancia: fecha visita, tipo visita, observaciones
  - Vincular actividades polimórficas:
    - Inspecciones completadas
    - Capacitaciones dictadas
    - Entregas de EPP realizadas
    - Evaluaciones de riesgo
    - Hallazgos registrados
  - Captura de firma del técnico
  - Captura de firma del responsable de sede
  - Opción "firma no disponible" con justificación
  - Generación automática de PDF profesional
  - Código único de constancia

#### RF-022: Historial de Constancias
- **Prioridad:** Media
- **Descripción:** Visualización de constancias generadas
- **Criterios de Aceptación:**
  - Administradores ven todas
  - Técnicos ven solo las propias
  - Filtros: fecha, técnico, sede, empresa
  - Descarga de PDF
  - Vista detallada con actividades vinculadas

---

### 3.10 Épica: Dashboards y Reportes

#### RF-023: Dashboard Administrador
- **Prioridad:** Alta
- **Descripción:** Panel de control con KPIs globales
- **KPIs:**
  - Total de empresas y sedes
  - Total de técnicos activos
  - Inspecciones del mes (completadas vs pendientes)
  - Capacitaciones programadas
  - Hallazgos críticos abiertos
  - Cumplimiento de plan anual
- **Criterios de Aceptación:**
  - Gráficos visuales (charts)
  - Filtro por rango de fechas
  - Acceso rápido a módulos

#### RF-024: Dashboard Técnico
- **Prioridad:** Alta
- **Descripción:** Panel personalizado para técnicos
- **KPIs:**
  - Mis asignaciones pendientes
  - Inspecciones completadas este mes
  - Capacitaciones dictadas
  - Hallazgos creados
  - Constancias generadas
- **Criterios de Aceptación:**
  - Solo datos de sedes asignadas
  - Acceso rápido a "Mis Asignaciones"
  - Calendario de actividades

#### RF-025: Generación de PDFs
- **Prioridad:** Crítica
- **Descripción:** Exportación automática de documentos profesionales
- **Tipos de PDF:**
  1. Certificado de inspección
  2. Certificado de capacitación
  3. Constancia de entrega de EPP
  4. Constancia de visita
  5. Reporte de hallazgo
  6. Matriz de riesgos
  7. Plan anual
- **Criterios de Aceptación:**
  - Logo personalizable
  - Formato A4/Legal
  - Saltos de página correctos (DomPDF)
  - Firmas digitales incrustadas
  - Descarga directa desde navegador
  - Generación < 5 segundos

---

### 3.11 Épica: Categorías y Organización

#### RF-026: Gestión de Categorías
- **Prioridad:** Media
- **Descripción:** Organización jerárquica de checklists
- **Criterios de Aceptación:**
  - CRUD categorías: nombre, icono, color, orden
  - CRUD subcategorías vinculadas a categorías
  - Reordenar con drag & drop
  - Vincular checklists a categoría/subcategoría
  - Solo administradores

---

## 4. Requerimientos No Funcionales

### 4.1 Seguridad

#### RNF-001: Autenticación y Autorización
- **Descripción:** Protección robusta de acceso al sistema
- **Criterios:**
  - Contraseñas hasheadas con bcrypt (Laravel default)
  - Tokens CSRF en todos los formularios
  - Middleware de autenticación en rutas protegidas
  - Middleware de roles granular
  - Tokens Sanctum con expiración configurable
  - **Aislamiento de Tenant**: Middleware que inyecta `tenant_id` en todos los Scopes

#### RNF-001.bis: Aislamiento Lógico de Datos (Multitenancy)
- **Descripción:** Garantizar que ningún dato de un Tenant sea accesible por otro.
- **Criterios:**
  - Columna `tenant_id` en todas las tablas principales (Empresas, Sedes, Usuarios, Inspecciones, etc.)
  - Global Scope en Eloquent (`TenantScope`) aplicado automáticamente
  - Constraints de Base de Datos para integridad referencial por tenant

#### RNF-002: Protección de Datos
- **Descripción:** Seguridad de información sensible
- **Criterios:**
  - HTTPS obligatorio en producción
  - Sanitización de inputs (prevención XSS)
  - Protección contra SQL Injection (Eloquent ORM)
  - Validación de archivos subidos (tipo, tamaño)
  - Logs de auditoría para acciones críticas
  - Backup diario automático de base de datos

#### RNF-003: Aislamiento de Datos por Sede
- **Descripción:** Los técnicos solo acceden a datos de sedes asignadas interna a los tenants
- **Criterios:**
  - Scopes globales en modelos (cuando aplique)
  - Validación de pertenencia en controladores
  - Filtrado automático en queries
  - Imposibilidad de acceder a datos de otras sedes

---

### 4.2 Performance

#### RNF-004: Tiempos de Respuesta
- **Descripción:** Latencia aceptable para operaciones
- **Criterios:**
  - **Páginas web:** < 2 segundos (carga inicial)
  - **Acciones Livewire:** < 500 ms
  - **Generación de PDF:** < 5 segundos
  - **Búsquedas:** < 1 segundo (hasta 10,000 registros)
  - **Importación Excel:** < 10 segundos (hasta 500 filas)

#### RNF-005: Optimización de Queries
- **Descripción:** Prevención de problemas N+1 y queries lentas
- **Criterios:**
  - Eager loading en relaciones Eloquent (`with()`)
  - Índices en columnas frecuentemente consultadas
  - Paginación en listados (25 items por página)
  - Cache de queries repetitivas (Redis opcional)
  - Monitoreo con Laravel Debugbar (desarrollo)

#### RNF-006: Escalabilidad
- **Descripción:** Soporte de crecimiento de usuarios y datos
- **Criterios:**
  - **Técnicos concurrentes:** 10+ sin degradación
  - **Empresas:** 100+ clientes
  - **Sedes:** 500+ sedes
  - **Inspecciones:** 50,000+ registros históricos
  - **Archivos:** Almacenamiento en filesystem o S3 (no en BD)

---

### 4.3 Usabilidad

#### RNF-007: Diseño Responsive
- **Descripción:** Adaptación a tablets y dispositivos móviles
- **Criterios:**
  - Breakpoints Tailwind: Mobile (< 640px), Tablet (640-1024px), Desktop (> 1024px)
  - Optimizado para tablets 10-13" (uso principal en campo)
  - Navegación táctil optimizada
  - Formularios accesibles en pantallas pequeñas
  - Canvas de firma responsive

#### RNF-008: Experiencia de Usuario
- **Descripción:** Interfaz intuitiva y consistente
- **Criterios:**
  - Wizards paso a paso para procesos complejos
  - Mensajes de error claros y accionables
  - Feedback visual en acciones (spinners, toasts)
  - Confirmaciones en acciones destructivas
  - Ayuda contextual (tooltips)
  - Sidebar colapsable
  - Colores corporativos (naranja EP: #F69239)

#### RNF-009: Accesibilidad
- **Descripción:** Cumplimiento de estándares básicos WCAG 2.1 (nivel A)
- **Criterios:**
  - Contraste de colores adecuado
  - Navegación por teclado
  - Etiquetas descriptivas en formularios
  - Textos alternativos en imágenes

---

### 4.4 Mantenibilidad

#### RNF-010: Código Limpio
- **Descripción:** Estándares de calidad de código
- **Criterios:**
  - PSR-12 (PHP Standards)
  - Type hints y return types en métodos
  - Componentes Livewire < 500 líneas
  - Modelos con relaciones bien definidas
  - Comentarios en lógica compleja
  - Nombres descriptivos de variables y métodos

#### RNF-011: Documentación
- **Descripción:** Documentación técnica actualizada
- **Criterios:**
  - README con instrucciones de instalación
  - Documentación de arquitectura (ARQUITECTURA.md)
  - Documentación de tablas (TABLAS.MD)
  - Documentación de procedimientos (10 archivos)
  - Documentación de roles y permisos
  - Roadmaps de desarrollo

---

### 4.5 Disponibilidad

#### RNF-012: Uptime
- **Descripción:** Disponibilidad del sistema
- **Criterios:**
  - **SLA:** 99% uptime mensual (permite ~7 horas downtime/mes)
  - Ventanas de mantenimiento: Domingos 2-4 AM
  - Backups diarios automáticos
  - Plan de recuperación ante desastres (RTO < 8 horas)

#### RNF-013: Monitoreo
- **Descripción:** Observabilidad del sistema
- **Criterios:**
  - Logs estructurados (Laravel Log)
  - Alertas de errores críticos (email/Slack)
  - Monitoreo de performance (Laravel Pulse - Roadmap Sprint 1)
  - Health checks automáticos

---

### 4.6 Compatibilidad

#### RNF-014: Versionado
- **Descripción:** Compatibilidad hacia atrás
- **Criterios:**
  - Versionado semántico (MAJOR.MINOR.PATCH.BUILD)
  - Migraciones de BD reversibles (`down()` method)
  - Deprecación gradual de features (3 meses de aviso)
  - Changelog documentado

#### RNF-015: API REST
- **Descripción:** Compatibilidad con integraciones externas
- **Criterios:**
  - Endpoints RESTful
  - Autenticación Sanctum
  - Respuestas JSON estandarizadas
  - Versionado de API (v1, v2, etc.)
  - Documentación de endpoints

---

## 📊 Resumen de Prioridades

| Prioridad | Requerimientos Funcionales | Requerimientos No Funcionales |
|-----------|----------------------------|-------------------------------|
| **Crítica** | RF-000, RF-001, RF-002, RF-004, RF-006, RF-025 | RNF-001, RNF-001.bis, RNF-004 |
| **Alta** | RF-003, RF-005, RF-007, RF-008, RF-009, RF-010, RF-011, RF-013, RF-014, RF-016, RF-018, RF-019, RF-021, RF-023, RF-024 | RNF-002, RNF-007, RNF-012 |
| **Media** | RF-012, RF-015, RF-017, RF-020, RF-022, RF-026 | RNF-005, RNF-008, RNF-010 |

---

## 📅 Roadmap de Implementación

### Fase 1: Core (Completado - V2.0.0.0)
- ✅ TALL Stack implementado
- ✅ 10 módulos principales funcionales
- ✅ 41 componentes Livewire
- ✅ 46 tablas en base de datos
- ✅ Sistema de roles y permisos (Adaptar a SaaS)
- ✅ Generación de PDFs
- 🔄 Migración a estructura Multitenant (Base de datos)

### Fase 2: Fundación (Sprint 1 - 2-3 semanas)
- 🔄 Performance y estabilidad (Laravel Pulse)
- 🔄 Firmas digitales de alta calidad (SVG 3x)
- 🔄 Optimización de queries (índices, eager loading)

### Fase 3: PWA Offline (Sprint 2 - 3-4 semanas)
- 📋 Service Workers
- 📋 IndexedDB para almacenamiento local
- 📋 Sincronización automática
- 📋 Manifest PWA

### Fase 4: UX (Sprint 3 - 1-2 semanas)
- 📋 Rediseño de sidebar
- 📋 Agrupación lógica de funciones
- 📋 Sistema de favoritos

### Fase 5: Portal Clientes (Sprint 4 - 3-4 semanas)
- 📋 Nuevo rol "Usuario Empresarial"
- 📋 Dashboard para clientes
- 📋 Sistema de notificaciones

### Fase 6: Inteligencia Artificial (Sprint 5 - 4-6 semanas)
- 📋 Autocompletado de checklists
- 📋 Análisis de imágenes
- 📋 Generación de informes técnicos

---

## 📚 Referencias

- [ARQUITECTURA.md](file:///d:/EPPSaaS/ep-back/doc/Lógica_del_diseño/ARQUITECTURA.md) - Arquitectura completa del sistema
- [TABLAS.MD](file:///d:/EPPSaaS/ep-back/doc/Lógica_del_diseño/TABLAS.MD) - Esquema de base de datos (46 tablas)
- [ROLES Y SUS ALCANCES.MD](file:///d:/EPPSaaS/ep-back/doc/Procedimientos/ROLES%20Y%20SUS%20ALCANCES.MD) - Matriz de permisos
- [ROADMAP_MAESTRO.md](file:///d:/EPPSaaS/ep-back/doc/Roadmap/ROADMAP_MAESTRO.md) - Roadmap con dependencias
- [master_demo_seeder.md](file:///d:/EPPSaaS/ep-back/doc/master_demo_seeder.md) - Datos de demostración

---

**Documento aprobado por:** Equipo EP-Back  
**Última actualización:** Enero 2026  
**Próxima revisión:** Marzo 2026  
**Versión del Sistema:** V2.0.0.0