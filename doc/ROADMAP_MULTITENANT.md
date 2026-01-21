# 🚀 Roadmap: Sistema Multi-Tenant con Branding Personalizable

> **Objetivo**: Transformar EP-Back en una plataforma SaaS multi-tenant donde cada consultora puede tener su propia identidad visual y configuración personalizada.

---

## 📊 Visión General

### Problema Actual
- Sistema diseñado para un solo cliente
- Branding hardcodeado (logos, colores, nombres)
- Escalabilidad limitada para múltiples consultoras

### Solución Propuesta
- Sistema multi-tenant con aislamiento de datos
- Configuración de branding por tenant (logo, colores, nombre)
- Panel de administración para Super Admin
- White-label completo para cada cliente

### Beneficios
- ✅ Escalabilidad: N clientes con una sola base de código
- ✅ Comercialización: Producto SaaS vendible
- ✅ Mantenimiento: Actualizaciones centralizadas
- ✅ Personalización: Cada cliente con su identidad

---

## 🎯 Fases de Implementación

### **FASE 1: Fundamentos de Multi-Tenancy** 🏗️
**Objetivo**: Establecer la infraestructura base de tenants

#### 1.1 Base de Datos
- [ ] Crear migración `create_tenants_table`
  ```sql
  - id (PK)
  - name (nombre de la consultora)
  - slug (identificador único, ej: "consultora-abc")
  - subdomain (opcional, para subdominios)
  - is_active (boolean)
  - trial_ends_at (nullable, para trials)
  - created_at, updated_at
  ```

- [ ] Crear migración `add_tenant_id_to_users`
  ```sql
  - tenant_id (FK a tenants, nullable inicialmente)
  ```

- [ ] Crear migración para otras tablas principales
  ```sql
  Agregar tenant_id a:
  - sales
  - budgets
  - purchase_details
  - hour_details
  - board_details
  - automation_projects
  - checklists
  - categories
  - roles (si aplica)
  ```

#### 1.2 Modelos y Relaciones
- [ ] Crear modelo `Tenant` con relaciones
- [ ] Agregar trait `BelongsToTenant` para modelos
- [ ] Actualizar modelos existentes con relación `tenant()`
- [ ] Configurar soft deletes en `Tenant`

#### 1.3 Seeders
- [ ] Crear `TenantSeeder` con tenant por defecto
- [ ] Actualizar `UserSeeder` para asignar tenant
- [ ] Crear datos de prueba para múltiples tenants

**Estimación**: 2-3 días
**Prioridad**: 🔴 Alta

---

### **FASE 2: Middleware y Contexto de Tenant** 🔒
**Objetivo**: Detectar y aplicar el tenant activo en cada request

#### 2.1 Middleware
- [ ] Crear `IdentifyTenant` middleware
  - Detectar tenant por:
    - Subdominio (ej: `consultora1.ep-back.com`)
    - Sesión del usuario logueado
    - Header personalizado (para API)
  - Setear tenant en contexto global
  - Abortar si tenant inactivo

- [ ] Crear `EnsureTenantScope` middleware
  - Aplicar global scope automáticamente
  - Prevenir acceso cross-tenant

#### 2.2 Global Scopes
- [ ] Crear `TenantScope` para filtrado automático
- [ ] Aplicar scope a todos los modelos con `BelongsToTenant`
- [ ] Métodos para bypass (solo Super Admin)

#### 2.3 Helpers y Facades
- [ ] Crear helper `tenant()` para obtener tenant actual
- [ ] Crear helper `isSuperAdmin()` 
- [ ] Facade `Tenant::current()`, `Tenant::id()`

**Estimación**: 2-3 días
**Prioridad**: 🔴 Alta

---

### **FASE 3: Configuración de Branding** 🎨
**Objetivo**: Permitir personalización visual por tenant

#### 3.1 Base de Datos
- [ ] Crear migración `create_tenant_settings_table`
  ```sql
  - id (PK)
  - tenant_id (FK)
  - logo_path (nullable)
  - logo_small_path (nullable, para navbar)
  - favicon_path (nullable)
  - primary_color (default: #3B82F6)
  - secondary_color (default: #10B981)
  - accent_color (nullable)
  - company_name (nullable, override del tenant.name)
  - email_from (nullable)
  - email_from_name (nullable)
  - custom_css (text, nullable)
  - settings (JSON, para configs adicionales)
  ```

#### 3.2 Almacenamiento de Archivos
- [ ] Configurar disk `tenants` en `config/filesystems.php`
  ```php
  'tenants' => [
      'driver' => 'local',
      'root' => storage_path('app/tenants'),
  ]
  ```

- [ ] Estructura de directorios:
  ```
  storage/app/tenants/{tenant_id}/
    ├── logos/
    ├── images/
    ├── documents/
    └── exports/
  ```

#### 3.3 Modelos y Servicios
- [ ] Crear modelo `TenantSetting` con relación a `Tenant`
- [ ] Crear servicio `BrandingService` para:
  - Obtener configuración de branding
  - Cachear configuración (Redis/File)
  - Invalidar cache al actualizar
- [ ] Crear servicio `TenantStorageService` para manejo de archivos

**Estimación**: 2 días
**Prioridad**: 🟡 Media

---

### **FASE 4: Panel de Administración de Tenants** ⚙️
**Objetivo**: UI para Super Admin gestionar tenants

#### 4.1 Rutas y Controladores
- [ ] Crear rutas en `routes/web.php` (grupo `superadmin`)
  ```
  /superadmin/tenants (index)
  /superadmin/tenants/create
  /superadmin/tenants/{id}/edit
  /superadmin/tenants/{id}/settings (branding)
  ```

- [ ] Crear `SuperAdmin\TenantController`
  - CRUD completo de tenants
  - Activar/desactivar tenants
  - Ver estadísticas por tenant

- [ ] Crear `SuperAdmin\TenantSettingsController`
  - Actualizar branding
  - Upload de logos
  - Preview de cambios

#### 4.2 Vistas Livewire
- [ ] Crear componente `TenantList` (tabla con filtros)
- [ ] Crear componente `TenantForm` (crear/editar)
- [ ] Crear componente `TenantBrandingForm`
  - Upload de logo con preview
  - Color pickers para colores
  - Preview en tiempo real
- [ ] Crear componente `TenantStats` (dashboard)

#### 4.3 Validaciones y Políticas
- [ ] Crear `TenantPolicy` (solo Super Admin)
- [ ] Validar slug único
- [ ] Validar formatos de imagen
- [ ] Validar códigos de color

**Estimación**: 3-4 días
**Prioridad**: 🟡 Media

---

### **FASE 5: Aplicación de Branding Dinámico** 🖌️
**Objetivo**: Inyectar branding del tenant en todas las vistas

#### 5.1 View Composers
- [ ] Crear `TenantBrandingComposer`
  - Inyectar variables en todas las vistas:
    - `$tenantLogo`
    - `$tenantName`
    - `$tenantColors`
    - `$tenantSettings`

- [ ] Registrar composer en `AppServiceProvider`

#### 5.2 Actualización de Layouts
- [ ] Actualizar `layouts/app.blade.php`
  - Logo dinámico en navbar
  - Favicon dinámico
  - Inyectar CSS de colores personalizados
  - Título dinámico

- [ ] Actualizar `layouts/guest.blade.php`
  - Logo en login/register
  - Colores de marca

#### 5.3 CSS Dinámico
- [ ] Crear ruta `/tenant-styles.css` (generada dinámicamente)
- [ ] Generar CSS con variables CSS:
  ```css
  :root {
    --tenant-primary: {{ $primaryColor }};
    --tenant-secondary: {{ $secondaryColor }};
  }
  ```
- [ ] Actualizar componentes Tailwind para usar colores dinámicos

#### 5.4 Emails
- [ ] Actualizar templates de email con branding
- [ ] Logo del tenant en headers
- [ ] Colores personalizados
- [ ] Firma personalizada

**Estimación**: 3 días
**Prioridad**: 🟡 Media

---

### **FASE 6: Migración de Datos Existentes** 📦
**Objetivo**: Migrar datos actuales al sistema multi-tenant

#### 6.1 Estrategia de Migración
- [ ] Crear tenant "default" o "Xamanen" (tenant principal)
- [ ] Asignar `tenant_id` a todos los registros existentes
- [ ] Verificar integridad de datos

#### 6.2 Scripts de Migración
- [ ] Crear comando Artisan `tenant:migrate-existing-data`
  - Asignar tenant_id a users
  - Asignar tenant_id a sales, budgets, etc.
  - Generar reporte de migración

- [ ] Crear comando `tenant:verify-data-integrity`
  - Verificar que no haya registros huérfanos
  - Validar relaciones

#### 6.3 Rollback Plan
- [ ] Documentar pasos de rollback
- [ ] Backup de base de datos antes de migración
- [ ] Script de reversión si es necesario

**Estimación**: 1-2 días
**Prioridad**: 🔴 Alta (antes de producción)

---

### **FASE 7: Aislamiento y Seguridad** 🔐
**Objetivo**: Garantizar que los tenants no puedan acceder a datos de otros

#### 7.1 Testing de Aislamiento
- [ ] Crear tests de aislamiento de datos
  - Usuario de Tenant A no puede ver datos de Tenant B
  - Queries automáticamente filtradas por tenant
  - APIs respetan tenant scope

#### 7.2 Auditoría
- [ ] Logs de acceso cross-tenant (si ocurre)
- [ ] Alertas de seguridad
- [ ] Integrar con sistema de auditoría existente

#### 7.3 Validaciones Adicionales
- [ ] Middleware para prevenir tenant switching
- [ ] Validar tenant_id en formularios
- [ ] Sanitizar inputs de tenant

**Estimación**: 2 días
**Prioridad**: 🔴 Alta

---

### **FASE 8: Onboarding de Nuevos Tenants** 🎉
**Objetivo**: Proceso automatizado para dar de alta nuevos clientes

#### 8.1 Wizard de Onboarding
- [ ] Crear wizard multi-step para Super Admin
  - Paso 1: Datos del tenant (nombre, slug, contacto)
  - Paso 2: Configuración de branding
  - Paso 3: Crear usuario Admin del tenant
  - Paso 4: Configuración inicial (módulos activos)
  - Paso 5: Confirmación y activación

#### 8.2 Automatización
- [ ] Crear comando `tenant:create` (CLI)
- [ ] Generar credenciales iniciales
- [ ] Enviar email de bienvenida
- [ ] Crear datos de ejemplo (opcional)

#### 8.3 Documentación
- [ ] Guía de onboarding para Super Admin
- [ ] Guía de configuración para Admin de Tenant
- [ ] Video tutorial (opcional)

**Estimación**: 2-3 días
**Prioridad**: 🟢 Baja (post-MVP)

---

### **FASE 9: Optimización y Performance** ⚡
**Objetivo**: Asegurar que el sistema escale eficientemente

#### 9.1 Caching
- [ ] Cachear configuración de tenant (Redis)
- [ ] Invalidación inteligente de cache
- [ ] Cache de queries frecuentes por tenant

#### 9.2 Índices de Base de Datos
- [ ] Crear índices compuestos `(tenant_id, id)`
- [ ] Analizar queries lentas
- [ ] Optimizar joins con tenant_id

#### 9.3 Lazy Loading
- [ ] Eager loading de relaciones tenant
- [ ] Optimizar N+1 queries

**Estimación**: 2 días
**Prioridad**: 🟡 Media

---

### **FASE 10: Funcionalidades Avanzadas** 🚀
**Objetivo**: Features adicionales para diferenciación

#### 10.1 Módulos Opcionales por Tenant
- [ ] Sistema de activación/desactivación de módulos
  - Sales, Budgets, Hours, Boards, etc.
  - Configuración en `tenant_settings.modules` (JSON)
- [ ] Ocultar menús de módulos desactivados
- [ ] Validar acceso a rutas de módulos

#### 10.2 Subdominios Personalizados
- [ ] Configurar wildcard DNS
- [ ] Middleware para detectar tenant por subdominio
- [ ] Documentación de configuración de servidor

#### 10.3 Reportes por Tenant
- [ ] Dashboard de Super Admin con métricas:
  - Usuarios activos por tenant
  - Uso de almacenamiento
  - Actividad mensual
  - Módulos más usados

#### 10.4 Exportación de Datos
- [ ] Permitir a tenants exportar sus datos
- [ ] Formato JSON/Excel
- [ ] Cumplimiento GDPR

**Estimación**: 4-5 días
**Prioridad**: 🟢 Baja (futuro)

---

## 📋 Checklist de Pre-Producción

Antes de lanzar el sistema multi-tenant en producción:

- [ ] ✅ Todas las migraciones ejecutadas y probadas
- [ ] ✅ Datos existentes migrados correctamente
- [ ] ✅ Tests de aislamiento pasando al 100%
- [ ] ✅ Backup de base de datos realizado
- [ ] ✅ Documentación actualizada
- [ ] ✅ Al menos 2 tenants de prueba configurados
- [ ] ✅ Performance testing con múltiples tenants
- [ ] ✅ Plan de rollback documentado
- [ ] ✅ Capacitación a Super Admin completada

---

## 🛠️ Stack Tecnológico

### Backend
- **Laravel 10+**: Framework principal
- **MySQL**: Base de datos
- **Redis**: Cache de configuración de tenants

### Frontend
- **Livewire 3**: Componentes reactivos
- **Tailwind CSS**: Estilos con variables dinámicas
- **Alpine.js**: Interactividad

### Almacenamiento
- **Local Storage**: Logos y archivos por tenant
- **S3** (opcional): Para producción escalable

---

## 📊 Estimación Total

| Fase | Días | Prioridad |
|------|------|-----------|
| Fase 1: Fundamentos | 2-3 | 🔴 Alta |
| Fase 2: Middleware | 2-3 | 🔴 Alta |
| Fase 3: Branding Config | 2 | 🟡 Media |
| Fase 4: Panel Admin | 3-4 | 🟡 Media |
| Fase 5: Branding Dinámico | 3 | 🟡 Media |
| Fase 6: Migración Datos | 1-2 | 🔴 Alta |
| Fase 7: Seguridad | 2 | 🔴 Alta |
| Fase 8: Onboarding | 2-3 | 🟢 Baja |
| Fase 9: Performance | 2 | 🟡 Media |
| Fase 10: Avanzadas | 4-5 | 🟢 Baja |

**Total MVP (Fases 1-7)**: ~15-20 días de desarrollo
**Total Completo**: ~25-30 días de desarrollo

---

## 🎯 MVP Recomendado

Para una primera versión funcional, implementar:

1. ✅ **Fase 1**: Fundamentos de Multi-Tenancy
2. ✅ **Fase 2**: Middleware y Contexto
3. ✅ **Fase 3**: Configuración de Branding
4. ✅ **Fase 4**: Panel de Administración
5. ✅ **Fase 5**: Branding Dinámico
6. ✅ **Fase 6**: Migración de Datos
7. ✅ **Fase 7**: Aislamiento y Seguridad

**Resultado**: Sistema multi-tenant funcional con branding personalizable, listo para onboarding de primeros clientes.

---

## 📚 Recursos y Referencias

### Paquetes Útiles
- [spatie/laravel-multitenancy](https://github.com/spatie/laravel-multitenancy)
- [tenancy/tenancy](https://tenancy.dev/) (si se requiere DB por tenant)

### Documentación
- [Laravel Multi-Tenancy Guide](https://laravel.com/docs/10.x/database#multi-tenancy)
- [Tailwind CSS Variables](https://tailwindcss.com/docs/customizing-colors#using-css-variables)

### Consideraciones Futuras
- **Database per Tenant**: Si se requiere aislamiento total
- **Kubernetes**: Para escalabilidad horizontal
- **CDN**: Para assets de tenants

---

## 🚨 Riesgos y Mitigaciones

| Riesgo | Impacto | Mitigación |
|--------|---------|------------|
| Data leakage entre tenants | 🔴 Crítico | Tests exhaustivos, global scopes, auditoría |
| Performance con muchos tenants | 🟡 Medio | Caching, índices, optimización de queries |
| Migración de datos falla | 🔴 Crítico | Backups, scripts de rollback, testing en staging |
| Complejidad de mantenimiento | 🟡 Medio | Documentación, tests automatizados |

---

## ✅ Próximos Pasos

1. **Revisar y aprobar este roadmap**
2. **Crear rama `feature/multi-tenant-mvp`**
3. **Comenzar con Fase 1** cuando estés listo
4. **Iterar fase por fase** con testing continuo

---

**Última actualización**: 2026-01-21  
**Versión**: 1.0  
**Autor**: Roadmap generado para EP-Back Multi-Tenant System
