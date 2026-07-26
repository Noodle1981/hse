# 📘 Manual de Uso y Capacitación: Vendedores y Súper Administradores

---

## 1. Visión General del Rol Vendedor & Súper Admin

El ecosistema **XamaSafe** opera bajo un modelo de arquitectura *multi-tenancy* donde múltiples consultoras de Higiene, Seguridad y Medio Ambiente (HyS) conviven de forma aislada e independiente. 

Para garantizar el correcto licenciamiento, soporte comercial y alta de operaciones, el sistema dispone de dos roles globales de administración central:

### 💼 Rol Vendedor (`vendedor`)
El **Vendedor** es el perfil de gestión comercial y soporte operativo de primera línea. Tiene potestad para administrar el ciclo de vida comercial de las consultoras asignadas o creadas por su cuenta.
* **Capacidades:**
  * Dar de alta nuevas Consultoras de HyS en la plataforma.
  * Configurar y actualizar los límites de licencias contratadas (máximo de técnicos, gestores y empresas).
  * Personalizar el branding corporativo de las consultoras (logotipo oficial y paleta cromática de acento).
  * Gestionar el estado de la suscripción (`activa`, `morosa` o `baja`).
  * Resetear la contraseña del **Gestor Principal** de cada consultora ante olvidos o bloqueos.
  * Resolver solicitudes in-app de reseteo de claves (`password_reset_requests`).
* **Restricciones de Seguridad:**
  * No puede eliminar físicamente consultoras ni registros maestros del sistema (recibe `403 Forbidden`).
  * No tiene acceso a la administración del personal global (*Staff de Súper Administradores*).
  * No interactúa directamente con los operadores secundarios o técnicos de una consultora (tarea delegada al Gestor Principal).

### 👑 Rol Súper Administrador (`super_admin`)
El **Súper Administrador** ostenta el control técnico y operativo global absoluto sobre el SaaS. Posee todas las capacidades del Vendedor y adiciona:
* Eliminación definitiva de consultoras de la base de datos.
* Administración del personal global (Alta, baja y reseteos del staff interno de súper admins y vendedores).
* Acceso completo a auditorías inmutables de cambios de licencias y actividad del sistema (`AuditLog`).

---

## 2. Onboarding y Creación de Consultoras

### Paso 1: Ingreso a la Consola de Administración
1. Inicie sesión en la plataforma XamaSafe con su cuenta de Vendedor o Súper Admin.
2. En el menú lateral izquierdo (HUD Táctico), seleccione la opción **"Consultoras"**.
3. Haga clic en el botón superior **"+ Nueva Consultora"**.

### Paso 2: Datos de Identidad Comercial
En el modal de registro, complete los campos obligatorios:
* **Nombre Comercial / Razón Social:** Nombre con el que opera la consultora de HyS.
* **CUIT / NIIF:** Identificador fiscal único de la empresa (sin guiones ni espacios).
* **Provincia y Localidad:** Ubicación geográfica de la sede central de la consultora.
* **Email & Teléfono Institucional:** Vías de contacto directo del titular.

---

## 3. Asignación del Gestor Principal (`is_primary`)

### ¿Qué es el Gestor Principal?
Cada consultora requiere **obligatoriamente un único Gestor Principal titular** (`is_primary = true`). Este usuario es el responsable legal y operativo ante XamaSafe y es la única persona dentro de su consultora facultada para:
1. Administrar la nómina de personal propio (crear gestores secundarios y técnicos).
2. Resetear las contraseñas de su equipo de trabajo.

### Flujo de Alta del Gestor Principal
Al crear la consultora desde el panel comercial:
1. Complete los datos personales del titular: **Nombres, Apellidos y Email Corporativo**.
2. El sistema marcará automáticamente al usuario creado como `is_primary = true`.
3. Se registrará la trazabilidad `created_by_user_id` asociando la consultora al Vendedor autor de la creación.

---

## 4. Configuración de Licencias y Branding

### 📊 Gestión de Límites de Licencias
Desde la vista de edición o configuración de la consultora (`/admin/consultants/{id}/edit`), el Vendedor debe ajustar la capacidad contratada:
* **Límite de Técnicos (`max_tecnicos`):** Cantidad máxima de inspectores de campo que la consultora puede tener en estado `activo` simultáneamente.
* **Límite de Gestores (`max_gestores`):** Cantidad máxima de administradores secundarios autorizados.
* **Límite de Empresas (`max_empresas`):** Cantidad de clientes corporativos que la consultora puede auditar en la plataforma.

> ⚠️ **Regla de Validación:** Si una consultora intenta dar de alta un técnico adicional superando su cupo `max_tecnicos`, el sistema bloqueará la operación notificando que debe contactar a su Vendedor para ampliar el plan.

### 🎨 Branding Corporativo Personalizado
XamaSafe aplica el esquema de colores de la consultora en los informes PDF y en la interfaz visual:
* **Logotipo Oficial:** Suba la imagen en formato PNG/JPG (fondo transparente recomendado).
* **Color Primario (`color_primary`):** Color dominante utilizado en cabeceras de tablas y botones.
* **Color Secundario (`color_secondary`):** Utilizado en subtítulos, badges y separadores.
* **Color de Acento (`color_accent`):** Utilizado para resaltar conformidades y estados de éxito.

---

## 5. Política de Seguridad y Contraseñas Únicas (16 Caracteres)

Para cumplir con normativas de seguridad bancarias y auditorías ISO 27001, XamaSafe implementa una política estricta de credenciales:

### 🔑 Generación Automática de Contraseña Temporal
1. **Sin Tipeo Manual:** Al registrar un nuevo Gestor Principal o al procesar un reseteo de clave, el sistema **NO** permite ingresar contraseñas arbitrarias de forma manual.
2. **Generación Aleatoria:** El servidor genera automáticamente un token seguro de 16 caracteres alfanuméricos aleatorios (`Str::random(16)`).
3. **Modal con Botón Copiar:** El Vendedor visualiza en pantalla un modal HUD táctico con la contraseña temporal generada y un botón de un toque **"Copiar Contraseña"**.

### 🔒 Cambio Obligatorio de Clave (`ForcePasswordChange`)
* Todo usuario nuevo o cuya clave haya sido reseteada por el Vendedor se marca internamente con el flag `must_change_password = true`.
* Al iniciar sesión con la clave temporal de 16 caracteres, el middleware de seguridad intercepta la navegación y lo redirige automáticamente a la pantalla **/change-password**.
* El usuario **no puede acceder al dashboard ni operar** hasta que defina su nueva contraseña personal y privada.

---

## 6. Jerarquía de Reseteos de Claves y Solicitudes In-App

### 🛡️ Jerarquía Estricta de Permisos de Reseteo
Para prevenir accesos no autorizados o suplantación de identidad, los permisos de reseteo están segmentados:

```
┌─────────────────────────────────────────────────────────────┐
│                 SÚPER ADMIN / VENDEDOR                       │
│     Puede resetear contraseñas de: GESTORES PRINCIPALES     │
└──────────────────────────────┬──────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────┐
│                      GESTOR PRINCIPAL                       │
│  Puede resetear contraseñas de: GESTORES SECUNDARIOS Y TÉCNICOS │
└──────────────────────────────┴──────────────────────────────┘
```

### 📩 Solicitudes de Reseteo In-App (Sin Servidor SMTP)
En entornos de despliegue aislados o sin servidor de correo SMTP enlazado:
1. Si un usuario olvida su clave, presiona **"¿Olvidaste tu contraseña?"** en la pantalla de Login.
2. El sistema registra una solicitud en la tabla `password_reset_requests`.
3. Se genera un badge de notificación en el panel del Vendedor o Gestor Principal correspondiente.
4. Al hacer clic en **"Aprobar y Resetear"**, el sistema genera la nueva clave de 16 caracteres, la muestra en el modal para que el Vendedor se la transmita al cliente, y marca la solicitud como `resolved`.

---

## 7. Control de Estado de Suscripción

El Vendedor puede ajustar el estado comercial de la consultora:
* **`activa`:** Operación normal sin restricciones.
* **`morosa`:** Despliega un aviso preventivo en la consola del Gestor indicando regularización de pagos, sin bloquear el trabajo en terreno de los técnicos.
* **`baja`:** Bloquea el acceso al sistema para todos los usuarios pertenecientes a dicha consultora. Los datos históricos se conservan intactos para auditorías futuras.
