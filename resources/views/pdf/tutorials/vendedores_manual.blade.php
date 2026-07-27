<h1>Manual de Uso: Vendedores y Súper Administración</h1>

<h2>1. Visión General del Rol</h2>
<p>XamaSafe es una plataforma <em>multi-empresa</em> donde múltiples consultoras de Higiene, Seguridad y Medio Ambiente (HyS) trabajan de forma aislada e independiente. Para gestionar el alta comercial y el soporte de estas consultoras, el sistema cuenta con dos roles de administración central.</p>

<h3>💼 Vendedor</h3>
<p>Es el perfil de gestión comercial y soporte de primera línea. Administra el ciclo de vida comercial de las consultoras asignadas.</p>
<ul>
    <li>Dar de alta nuevas consultoras de HyS en la plataforma.</li>
    <li>Configurar los límites del plan contratado (cupo de técnicos, gestores y empresas).</li>
    <li>Personalizar la identidad visual de cada consultora (logotipo y colores corporativos).</li>
    <li>Gestionar el estado de suscripción de la consultora.</li>
    <li>Resetear la contraseña del Gestor Titular de cada consultora ante olvidos o bloqueos.</li>
    <li>Atender y resolver las solicitudes de recuperación de contraseña generadas en la plataforma.</li>
</ul>
<p><strong>Restricciones:</strong></p>
<ul>
    <li>No puede eliminar consultoras ni registros maestros (acción no autorizada para este rol).</li>
    <li>No tiene acceso a la administración del personal interno de XamaSafe.</li>
    <li>No interactúa directamente con los técnicos de una consultora (esa tarea es del Gestor Titular).</li>
</ul>

<h3>👑 Súper Administrador</h3>
<p>Posee control total sobre la plataforma. Tiene todas las capacidades del Vendedor y además puede:</p>
<ul>
    <li>Eliminar definitivamente consultoras de la plataforma.</li>
    <li>Administrar el personal interno de XamaSafe (altas, bajas y reseteos del staff).</li>
    <li>Acceder al registro de auditoría inmutable de cambios de licencias y actividad del sistema.</li>
</ul>

<hr>

<h2>2. Onboarding y Creación de Consultoras</h2>
<h3>Paso 1: Ingresar a la Consola de Administración</h3>
<ol>
    <li>Inicie sesión con su cuenta de Vendedor o Súper Administrador.</li>
    <li>En el menú lateral, seleccione <strong>"Consultoras"</strong>.</li>
    <li>Haga clic en el botón <strong>"+ Nueva Consultora"</strong>.</li>
</ol>
<h3>Paso 2: Completar los Datos de la Consultora</h3>
<ul>
    <li><strong>Nombre Comercial / Razón Social:</strong> Nombre con el que opera la consultora.</li>
    <li><strong>CUIT / NIIF:</strong> Identificador fiscal único (sin guiones ni espacios).</li>
    <li><strong>Provincia y Localidad:</strong> Ubicación de la sede central.</li>
    <li><strong>Email y Teléfono Institucional:</strong> Datos de contacto del titular.</li>
</ul>

<hr>

<h2>3. Asignación del Gestor Titular</h2>
<p>Cada consultora necesita obligatoriamente un <strong>Gestor Titular</strong>. Este usuario es el responsable legal y operativo dentro de la consultora y es la única persona facultada para:</p>
<ol>
    <li>Administrar la nómina interna (crear gestores de apoyo y técnicos).</li>
    <li>Resetear las contraseñas de su propio equipo de trabajo.</li>
</ol>
<h3>Cómo dar de alta al Gestor Titular</h3>
<ol>
    <li>Complete los datos del titular: Nombres, Apellidos y Email Corporativo.</li>
    <li>El sistema lo marcará automáticamente como Gestor Titular de la consultora.</li>
    <li>Quedará registrado que fue el Vendedor autor quien creó esta consultora.</li>
</ol>

<hr>

<h2>4. Configuración de Licencias e Identidad Visual</h2>
<h3>📊 Gestión del Plan Contratado</h3>
<p>Desde la pantalla de edición de la consultora, ajuste la capacidad del plan:</p>
<ul>
    <li><strong>Cupo de Técnicos:</strong> Cantidad máxima de inspectores de campo activos al mismo tiempo.</li>
    <li><strong>Cupo de Gestores:</strong> Cantidad máxima de administradores secundarios autorizados.</li>
    <li><strong>Cupo de Empresas:</strong> Cantidad de clientes corporativos que la consultora puede auditar.</li>
</ul>
<blockquote><strong>Control automático:</strong> Si la consultora intenta agregar un técnico superando su cupo, el sistema bloqueará la operación e indicará que deben contactar al Vendedor para ampliar el plan.</blockquote>

<h3>🎨 Identidad Visual Personalizada</h3>
<p>XamaSafe aplica los colores de la consultora en los informes PDF y en la interfaz:</p>
<ul>
    <li><strong>Logotipo Oficial:</strong> Formato PNG/JPG (fondo transparente recomendado).</li>
    <li><strong>Color Primario:</strong> Cabeceras de tablas y botones.</li>
    <li><strong>Color Secundario:</strong> Subtítulos, badges y separadores.</li>
    <li><strong>Color de Acento:</strong> Conformidades y estados de éxito.</li>
</ul>

<hr>

<h2>5. Política de Contraseñas Seguras</h2>
<h3>🔑 Generación Automática de Clave Temporal</h3>
<ol>
    <li><strong>Sin tipeo manual:</strong> Al registrar un Gestor Titular o al resetear una clave, el sistema genera automáticamente una <em>clave temporal de 16 caracteres alfanuméricos</em>.</li>
    <li><strong>Modal con botón Copiar:</strong> El Vendedor verá en pantalla la clave generada con un botón <strong>"Copiar Contraseña"</strong> para transmitírsela al cliente.</li>
</ol>
<h3>🔒 Control de Seguridad de Primer Ingreso</h3>
<ul>
    <li>Todo usuario nuevo o cuya clave fue reseteada queda sujeto a un control de seguridad de primer ingreso.</li>
    <li>Al iniciar sesión con la clave temporal, la plataforma lo redirigirá a la pantalla <strong>Cambiar Contraseña</strong>.</li>
    <li>El usuario <strong>no puede operar el sistema</strong> hasta definir su nueva contraseña personal.</li>
</ul>

<hr>

<h2>6. Jerarquía de Reseteos y Recuperación de Contraseña</h2>
<h3>🛡️ Quién puede resetear la contraseña de quién</h3>
<pre>┌─────────────────────────────────────────────────────────┐
│             SÚPER ADMINISTRADOR / VENDEDOR              │
│   Puede resetear contraseñas de: GESTORES TITULARES     │
└──────────────────────────┬──────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                    GESTOR TITULAR                       │
│  Puede resetear contraseñas de: GESTORES Y TÉCNICOS     │
└─────────────────────────────────────────────────────────┘</pre>
<h3>📩 Solicitudes de Recuperación de Contraseña</h3>
<ol>
    <li>El usuario presiona <strong>"¿Olvidaste tu contraseña?"</strong> en la pantalla de Login.</li>
    <li>El sistema genera una solicitud de recuperación y muestra un aviso en el panel del Vendedor o Gestor Titular correspondiente.</li>
    <li>Al hacer clic en <strong>"Aprobar y Resetear"</strong>, se genera una nueva clave temporal de 16 caracteres que se muestra en pantalla para que el Vendedor la transmita al cliente. La solicitud queda marcada como resuelta.</li>
</ol>

<hr>

<h2>7. Control del Estado de Suscripción</h2>
<p>El Vendedor puede ajustar el estado comercial de cada consultora:</p>
<ul>
    <li><strong>Activa:</strong> Operación normal sin restricciones.</li>
    <li><strong>Con deuda pendiente:</strong> Muestra un aviso en la consola del Gestor, sin bloquear el trabajo de los técnicos en campo.</li>
    <li><strong>Dada de baja:</strong> Bloquea el acceso al sistema para todos los usuarios de la consultora. Los datos históricos se conservan intactos para auditorías futuras.</li>
</ul>
