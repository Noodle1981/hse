# 🛡️ Manual de Operación y Gestión: Gestores (Principales y Secundarios)

---

## 1. Introducción y Estructura de Roles en la Consultora

En **XamaSafe**, la administración cotidiana de la Consultora de Higiene, Seguridad y Medio Ambiente (HyS) está a cargo del perfil **Gestor**. Para garantizar la seguridad del personal y el control de accesos, el sistema establece una distinción jerárquica clara entre dos perfiles:

### 👑 Gestor Principal (`is_primary = true`)
Es el titular administrativo oficial de la consultora registrado en la plataforma.
* **Privilegios Exclusivos:**
  * Administrar la nómina de operadores de la consultora (Alta, modificación y deshabilitación de Gestores Secundarios y Técnicos).
  * **Resetear contraseñas** de su equipo de trabajo (exclusivo para el Gestor Principal).
  * Auditar y aprobar las firmas digitales de perfil de los técnicos.
  * Gestionar empresas cliente y sedes físicas asociadas.

### 💼 Gestores Secundarios (`is_primary = false`)
Son administradores o consultores de apoyo técnico asignados a la consultora.
* **Capacidades:**
  * Diseñar plantillas de checklists dinámicos y configurar el Plan Anual (`AnnualPlan`).
  * Asignar capacitaciones, inspecciones y tareas a los técnicos.
  * Supervisar en tiempo real el avance de las visitas y hallazgos reportados.
  * Auditar y redactar observaciones (`observacion_revision`) en los Informes Maestros (`MasterReport`).
* **Restricciones:** No pueden resetear contraseñas de otros usuarios ni modificar la estructura de operadores de la consultora.

---

## 2. Creación y Administración del Personal (`gestor/users`)

### Paso 1: Acceso al Módulo de Personal
1. Inicie sesión como **Gestor Principal**.
2. En el menú lateral o desde la consola de Administración, seleccione **"Operadores / Personal"**.
3. Haga clic en el botón **"+ Registrar Nuevo Operador"**.

### Paso 2: Formulario de Alta de Operador
Complete los datos del nuevo miembro del equipo:
* **Nombres y Apellidos:** Identidad digital completa.
* **Email Corporativo:** Dirección única para inicio de sesión.
* **Rol en el Sistema:**
  * **TÉCNICO OPERATIVO:** Inspector de terreno con acceso a la app móvil de campo.
  * **GESTOR ADMINISTRATIVO:** Consultor secundario con acceso a la consola de mando.
* **Matrícula ID / Registro Profesional:** Código obligatorio de matrícula habilitante en HyS (convertido a mayúsculas automáticamente).
* **Teléfono Enlace & Nº Documento (DNI):** Canales de contacto y verificación legal.

> ⚠️ **Control de Licencias:** El sistema valida en tiempo real que no se superen los límites contratados (`max_tecnicos` y `max_gestores`). Si se alcanza el cupo, deberá contactar a su Vendedor para ampliar el plan.

---

## 3. Política de Reseteo de Contraseñas (Exclusivo Gestor Principal)

### 🔑 Generación Automática de Clave Temporal (16 Caracteres)
Para evitar contraseñas débiles o vulnerables, XamaSafe no permite la carga manual de contraseñas al resetear o editar usuarios:
1. Abra la ficha del usuario en `gestor/users` (haciendo clic en **"Configurar"**).
2. En el pie del modal, presione el botón **"🔑 Resetear Clave"**.
3. Se desplegará un modal de confirmación indicando que se generará una contraseña temporal de **16 caracteres alfanuméricos aleatorios** (`Str::random(16)`).
4. Al confirmar, la pantalla mostrará la clave generada con un botón **"Copiar"** para transmitírsela al operador.

### 🔒 Intercepción de Primer Ingreso (`must_change_password`)
* Todo usuario creado o reseteado queda marcado con `must_change_password = true`.
* Al ingresar a la plataforma por primera vez con la clave temporal, el middleware `ForcePasswordChange` bloqueará la navegación y lo redirigirá a la pantalla **/change-password**.
* El operador deberá establecer su clave privada definitiva antes de acceder a la plataforma.

---

## 4. Gestión, Auditoría y Aprobación de Firmas Digitales

Las firmas de perfil de los técnicos garantizan la validez legal de las constancias de inspección y capacitaciones.

### Flujo de Auditoría de Firma de Perfil
1. Cuando un Técnico sube o actualiza su firma digital desde su perfil, esta entra en estado `firma_status = 'pending'` y se guarda en un resguardo temporal (`firma_temp`).
2. En la lista de operadores de `gestor/users`, el Gestor Principal visualizará una alerta o botón de auditoría **"Ver Firma Aprobada / Pendiente"**.
3. Al hacer clic en el ícono del ojo 👁️, se abre el modal de inspección de firma:
   * **Aprobar Firma:** La firma pasa a ser la oficial (`firma`) y su estado cambia a `'approved'`. Se aplicará automáticamente en todos los informes futuros del técnico.
   * **Rechazar Firma:** La firma es descartada (`'rejected'`) notificando al técnico para que realice una nueva captura clara.

---

## 5. Asignación de Técnicos a Sedes y Clientes (`gestor/technicians`)

Para que un Técnico de campo pueda realizar inspecciones en una sede cliente:
1. Ingrese a **"Asignación de Técnicos"** (`/gestor/technicians`).
2. Seleccione la Empresa Cliente y la Sede física correspondiente.
3. Marque los técnicos habilitados para operar en dicha sede.
4. El sistema sincronizará de inmediato las sedes visibles en la aplicación móvil del inspector (`MyAssignments.jsx`).

---

## 6. Plan Anual de Actividades de HyS (`AnnualPlan`)

El Plan Anual es la matriz estratégica que programa el cumplimiento normativo mensual de cada sede:
* **Creación de Actividades:** Defina el nombre de la tarea, código regulatorio y vinculación con una plantilla de checklist dinámico.
* **Parametrización Operativa:**
  * **Duración Estimada:** Seleccione el tiempo estándar (ej: 15m, 30m, 1h).
  * **Modalidad:** Presencial, Virtual o Híbrida.
  * **Multi-Instructor:** Active si la actividad requiere co-instructores.
  * **Meses Programados:** Seleccione los meses del año (Enero a Diciembre) en que la actividad debe ejecutarse.

---

## 7. Configuración de Perfil Propio y Firma Digital

Como Gestor, usted dispone de su panel personal de perfil:
1. En la esquina superior derecha de la pantalla, haga clic en su nombre y seleccione **"Mi Perfil"**.
2. **Datos Personales:** Actualice su teléfono de contacto y matrícula profesional.
3. **Firma Digital de Perfil:** Utilice el panel táctil (*canvas digital*) para registrar su firma de perfil. Esta firma se estampará en los Informes Maestros aprobados por su persona.
