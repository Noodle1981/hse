<h1>Manual de Operación: Gestores</h1>

<h2>1. Estructura de Roles dentro de la Consultora</h2>
<p>En XamaSafe, la administración cotidiana está a cargo del perfil <strong>Gestor</strong>. Existen dos niveles de gestión con responsabilidades distintas.</p>

<h3>👑 Gestor Titular</h3>
<p>Es el responsable administrativo oficial de la consultora registrado en la plataforma. Sus privilegios exclusivos son:</p>
<ul>
    <li>Administrar la nómina del equipo: dar de alta, modificar y deshabilitar a Gestores de Apoyo y Técnicos.</li>
    <li>Resetear contraseñas de cualquier miembro del equipo.</li>
    <li>Auditar y aprobar las firmas digitales de los técnicos.</li>
    <li>Gestionar empresas cliente y sedes físicas asociadas.</li>
</ul>

<h3>💼 Gestor de Apoyo</h3>
<p>Son administradores o consultores de respaldo asignados a la consultora. Sus capacidades incluyen:</p>
<ul>
    <li>Diseñar plantillas de checklists dinámicos y configurar el Plan Anual de actividades.</li>
    <li>Asignar capacitaciones, inspecciones y tareas a los técnicos.</li>
    <li>Supervisar en tiempo real el avance de las visitas y hallazgos reportados.</li>
    <li>Revisar y redactar observaciones en los Informes Maestros antes de aprobarlos.</li>
</ul>
<p><strong>Restricción:</strong> No pueden resetear contraseñas de otros usuarios ni modificar la estructura de operadores de la consultora.</p>

<hr>

<h2>2. Creación y Administración del Personal</h2>
<h3>Paso 1: Acceder al Módulo de Personal</h3>
<ol>
    <li>Inicie sesión como Gestor Titular.</li>
    <li>En el menú lateral o desde la Consola de Administración, seleccione <strong>"Operadores / Personal"</strong>.</li>
    <li>Haga clic en el botón <strong>"+ Registrar Nuevo Operador"</strong>.</li>
</ol>
<h3>Paso 2: Formulario de Alta</h3>
<ul>
    <li><strong>Nombres y Apellidos:</strong> Identidad completa del operador.</li>
    <li><strong>Email Corporativo:</strong> Dirección única para el inicio de sesión.</li>
    <li><strong>Rol:</strong>
        <ul>
            <li><strong>Técnico Operativo:</strong> Inspector de terreno con acceso a la app de campo.</li>
            <li><strong>Gestor Administrativo:</strong> Consultor de apoyo con acceso a la consola de mando.</li>
        </ul>
    </li>
    <li><strong>Matrícula / Registro Profesional:</strong> Código habilitante en HyS (convertido a mayúsculas automáticamente).</li>
    <li><strong>Teléfono y Nº de Documento:</strong> Canales de contacto y verificación legal.</li>
</ul>
<blockquote><strong>Control de cupo:</strong> El sistema valida en tiempo real que no se superen los límites del plan contratado. Si se alcanza el cupo, deberá contactar a su Vendedor para ampliar el plan.</blockquote>

<hr>

<h2>3. Reseteo de Contraseñas (Exclusivo del Gestor Titular)</h2>
<h3>🔑 Generación de Clave Temporal</h3>
<p>XamaSafe no permite ingresar contraseñas de forma manual al resetear usuarios. El proceso es:</p>
<ol>
    <li>Abra la ficha del usuario en la sección <strong>"Operadores / Personal"</strong> haciendo clic en <strong>"Configurar"</strong>.</li>
    <li>En la parte inferior del modal, presione el botón <strong>"🔑 Resetear Clave"</strong>.</li>
    <li>Se mostrará un aviso indicando que se generará una clave temporal de <strong>16 caracteres alfanuméricos</strong>.</li>
    <li>Al confirmar, la pantalla mostrará la clave generada con un botón <strong>"Copiar"</strong> para transmitírsela al operador.</li>
</ol>
<h3>🔒 Seguridad de Primer Ingreso</h3>
<ul>
    <li>Todo usuario creado o reseteado queda sujeto a un control de seguridad de primer ingreso.</li>
    <li>Al ingresar por primera vez con la clave temporal, la plataforma redirigirá al usuario a la pantalla <strong>Cambiar Contraseña</strong>.</li>
    <li>El operador no podrá acceder a la plataforma hasta establecer su clave privada definitiva.</li>
</ul>

<hr>

<h2>4. Auditoría y Aprobación de Firmas Digitales</h2>
<p>Las firmas de perfil de los técnicos garantizan la validez legal de las constancias de inspección y capacitaciones.</p>
<h3>Proceso de Aprobación</h3>
<ol>
    <li>Cuando un Técnico sube o actualiza su firma digital, esta queda en estado <strong>"en revisión"</strong>.</li>
    <li>En la lista de operadores, el Gestor Titular verá un indicador de auditoría sobre el técnico correspondiente.</li>
    <li>Al hacer clic en el ícono 👁️, se abre el panel de inspección de firma:
        <ul>
            <li><strong>Aprobar:</strong> La firma se convierte en la oficial y se estampará automáticamente en todos los informes futuros del técnico.</li>
            <li><strong>Rechazar:</strong> La firma es descartada y se notifica al técnico para que realice una nueva captura.</li>
        </ul>
    </li>
</ol>

<hr>

<h2>5. Asignación de Técnicos a Sedes y Clientes</h2>
<p>Para que un Técnico pueda realizar inspecciones en una sede cliente:</p>
<ol>
    <li>Ingrese a <strong>"Asignación de Técnicos"</strong> en el menú lateral.</li>
    <li>Seleccione la Empresa Cliente y la Sede física correspondiente.</li>
    <li>Marque los técnicos habilitados para operar en dicha sede.</li>
    <li>El sistema sincronizará de inmediato las sedes visibles en la aplicación del inspector.</li>
</ol>

<hr>

<h2>6. Plan Anual de Actividades</h2>
<p>El Plan Anual es la matriz estratégica que programa el cumplimiento normativo mensual de cada sede:</p>
<ul>
    <li><strong>Nombre y código regulatorio</strong> de la actividad.</li>
    <li><strong>Vinculación con una plantilla de checklist</strong> dinámico.</li>
    <li><strong>Duración estimada</strong> (ej: 15 min, 1 hora).</li>
    <li><strong>Modalidad:</strong> Presencial, Virtual o Híbrida.</li>
    <li><strong>Multi-Instructor:</strong> Activar si la actividad requiere co-instructores.</li>
    <li><strong>Meses programados:</strong> Seleccione en qué meses del año debe ejecutarse la actividad.</li>
</ul>

<hr>

<h2>7. Configuración de Perfil y Firma Digital Propia</h2>
<ol>
    <li>En la esquina superior derecha, haga clic en su nombre y seleccione <strong>"Mi Perfil"</strong>.</li>
    <li><strong>Datos Personales:</strong> Actualice su teléfono de contacto y matrícula profesional.</li>
    <li><strong>Firma Digital:</strong> Trace su firma sobre el panel táctil. Esta firma se estampará en los Informes Maestros que apruebe.</li>
</ol>
