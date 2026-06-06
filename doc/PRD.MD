# PRD — XamaSafe: Plataforma de Gestión de Seguridad Laboral
**Versión:** 1.5 — Tactical Modernization (Post-Clean Up & Dark UI)
**Fecha:** 2026-04-04
**Autor:** Producto XamaSafe

---

## 1. Visión General

### 1.1 ¿Qué es XamaSafe?

XamaSafe es una plataforma SaaS B2B de **gestión integral de Higiene y Seguridad Laboral** para consultoras y técnicos habilitados. Permite digitalizar el ciclo completo de la gestión de campo: visitas, checklists, capacitaciones, hallazgos, incidentes e informes.

### 1.2 Estado Actual — v1.5 Tactical Modernization

La plataforma ha migrado a la estética **"Terminal Táctica"**, un sistema de diseño High-End Dark UI con efectos de glassmorphism orientado a la inmersión técnica y eficiencia en campo. Se ha realizado un proceso de "refactorización profunda" eliminando controladores y vistas legadas (legacy) para optimizar el rendimiento y la mantenibilidad.

Toda la lógica de entrega, gestión de stock y trazabilidad de Elementos de Protección Personal (EPP) se mantiene como integración futura con la Tienda EPP (Sección 5).

### 1.3 Arquitectura del Ecosistema Completo (Visión Futura)

```
XamaSafe (este repositorio)          Tienda EPP (plataforma separada)
────────────────────────────         ────────────────────────────────
• Gestión de visitas                 • Catálogo de EPP certificado
• Checklists dinámicos               • Proveedores verificados
• Capacitaciones                     • Órdenes de compra
• Hallazgos e incidentes             • Trazabilidad de lotes
• Informes Maestros                  • Cumplimiento Res. 18/2025
        │                                       │
        └──────────── API / Webhook ────────────┘
                  (Conexión futura planeada)
```

---

## 2. Funcionalidades de la v1.5 (Estado Actual)

### 2.1 Módulo de Administración (Gestor/Admin)

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Gestión de Empresas/Sedes | ✅ Activo | CRUD optimizado con buscador dinámico y tracking de sedes. |
| Gestión de Usuarios | ✅ Activo | Admins y técnicos con perfiles detallados. |
| Gestión de Técnicos | ✅ Activo | Perfil técnico con firma digital optimizada y especialidades. |
| Branding por Consultora | ✅ Activo | Logo y colores aplicados a reportes PDF profesionales. |
| Dashboard de Gestión | ✅ Activo | Nueva interfaz Dark UI con KPIs de actividad en tiempo real. |
| **Modo Alternancia** | ✅ Activo | Los gestores pueden actuar como técnicos sin salir de su sesión. |

### 2.2 Módulo de Visitas Técnicas (Core del Producto)

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Gestión de Visitas | ✅ Activo | Asignación táctica de visitas desde el tablero central. |
| Visita en Progreso | ✅ Activo | Interfaz inmersiva con persistencia de datos y guardado automático. |
| Firma Digital Táctica | ✅ Activo | Canvas de firma optimizado para móviles con trazo blanco de alta visibilidad. |
| Auto-asignación | ✅ Activo | El técnico puede agregar checklists dinámicamente durante la visita. |
| Firma Remota | ✅ Activo | Envío de link por email para firma diferida por el cliente. |
| **Attendance Tracker** | ✅ Activo | Nuevo componente de asistencia modernizado e integrado a la visita. |

### 2.3 Módulo de Checklists & Hallazgos

| Funcionalidad | Estado | Descripción |
|---|---|---|
| Constructor Dinámico | ✅ Activo | Respuestas múltiples, booleanas y selección de fotos. |
| Ciclo de Hallazgo | ✅ Activo | Registro → Seguimiento (Pendiente/Resuelto) → Verificación → Cierre. |
| Criticidad Visual | ✅ Activo | Alertas de color (Naranja/Rojo) según el nivel de riesgo detectado. |
| Evidencia Fotográfica | ✅ Activo | Compresión de imágenes en cliente para subir fotos pesadas desde campo. |

---

## 3. Arquitectura Técnica

### 3.1 Stack (Modernized)

| Capa | Tecnología | Versión |
|---|---|---|
| **Backend** | Laravel 12 (PHP 8.3+) | 🚀 Última generación |
| **Frontend** | Livewire 3.7+ / Alpine.js | Reactividad modular |
| **UI Design** | Terminal Táctica (Glassmorphism) | Dark Standard v2.0 |
| **CSS** | Tailwind CSS 3.4+ | Utility-first |
| **PDFs** | Spatie Laravel PDF / DomPDF | Generación de alta fidelidad |
| **DB** | MySQL 8.0+ | Relacional robusto |

### 3.2 Roles del Sistema

| Rol | Descripción |
|---|---|
| `super_admin` | Acceso global y configuración de consultoras. |
| `gestor` | Administra la consultora, asigna visitas y revisa informes finales. |
| `tecnico` | Operación en campo, checklists, firmas y fotos. |

### 3.3 Filosofía "Terminal Táctica" (Design Language)

1.  **Dark UI High Contrast**: Fondo profundo (`#11111D`) con elementos en blanco vibrante o naranja técnico (`#F69239`).
2.  **Glassmorphism**: Uso de `backdrop-blur` en modales y filtros para mantener el contexto visual.
3.  **Inmersión en Campo**: Interfaz limpia, quitando distracciones para que el técnico se enfoque en el punto de control.
4.  **Mobile First Real**: Botones "Tactical Pill" de tamaño grande y selectores amigables para pulgares en movimiento.

---

## 4. Reglas de Negocio Clave

- **Trazabilidad Sellada**: No se permiten hallazgos ni capacitaciones fuera de una visita activa (excepción: gestores autorizados).
- **Consistencia de Firma**: Si el sistema detecta fondo oscuro, la firma guardada se procesa para máxima legibilidad en el PDF (fondo blanco).
- **Alertas de Persistencia**: Notificaciones cada 30 min para visitas abiertas sin actividad para cerrar el reporte diario.
- **Limpieza de Sesión**: Al alternar de Gestor a Técnico, los permisos se re-validan en cada request.

---

## 5. Ecosistema Futuro — Tienda EPP

XamaSafe detecta la necesidad (diagnóstico técnico) y la Tienda EPP resuelve la provisión.
La integración se realizará vía **Webhooks** enviando un JSON estructurado con el `epp_requerido` y el `cuit` de la empresa para automatizar cotizaciones.

---

## 6. Implementaciones Futuras — Tecnología

- **Certificación Blockchain**: Anclaje del hash de los informes en Polygon para validez legal pericial.
- **Geolocalización / GPS**: Registro de coordenadas `lat/long` al iniciar/finalizar visitas.
- **App Mobile Nativa**: Soporte 100% Offline para plantas sin señal (Sincronización manual).

---

## 7. Checklist de Auditoría del Sistema (v1.5)

- [ ] Las vistas Blade usan componentes `@layout('layouts.app')` o `x-layouts`.
- [ ] No hay referencias a controladores en `app/Http/Controllers/Old/`.
- [ ] El sistema de firmas funciona correctamente en modo oscuro y claro.
- [ ] Los PDFs se generan sin errores de sintaxis en las vistas Blade.
- [ ] Las auditorías de hallazgos muestran las fotos correctamente desde `storage`.

### 7.1 Seguridad e Integridad

- [ ] Los técnicos solo ven sus propios informes y visitas
- [ ] Los gestores solo ven datos de su propia consultora
- [ ] No hay endpoints API sin autenticación
- [ ] Las firmas digitales se almacenan de forma segura

---

*Documento actualizado: 2026-04-04 | XamaSafe v1.5 Tactical Modernization*
