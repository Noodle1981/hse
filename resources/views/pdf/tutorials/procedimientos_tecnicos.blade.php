<h1>Procedimientos de Trabajo en Terreno: Técnicos</h1>

<h2>1. Inmutabilidad de Tiempo: Cómo Protegemos los Registros</h2>
<p>Para garantizar la integridad técnica y legal de las auditorías, XamaSafe implementa un sistema de <strong>protección inmutable de tiempos</strong>.</p>
<h3>Principio de Funcionamiento</h3>
<ol>
    <li><strong>El reloj del dispositivo no cuenta:</strong> Al finalizar un checklist o registrar una visita, el servidor ignora deliberadamente la hora de su teléfono, tablet o laptop.</li>
    <li><strong>Timestamp del servidor:</strong> El sistema aplica de forma automática la hora exacta del servidor en el momento de recibir la información. Este valor no puede ser alterado desde el dispositivo.</li>
    <li><strong>Protección activa:</strong> Si se detecta un intento de manipulación del reloj del dispositivo, la plataforma registra una alerta de seguridad con los datos del técnico y el contenido descartado.</li>
</ol>

<hr>

<h2>2. Trabajo en Sedes de Operación Continua (Roster 24/7)</h2>
<p>Las sedes en campamentos mineros, plantas petroquímicas o instalaciones de operación continua funcionan bajo el régimen <strong>Roster Minero 24/7</strong>.</p>
<ul>
    <li><strong>Validación automática:</strong> Al realizar la entrada en una sede configurada con este régimen, el sistema omite las alertas de días u horarios no laborales.</li>
    <li><strong>Ingresos continuos:</strong> Puede registrar visitas e inspecciones a cualquier hora del día o la noche, incluyendo domingos y feriados, sin que el sistema genere advertencias.</li>
</ul>

<hr>

<h2>3. Contingencias GPS y Conectividad</h2>
<h3>Cómo se calcula la cercanía</h3>
<p>Al realizar la entrada o salida de una visita, la plataforma compara su ubicación GPS contra las coordenadas de la Sede mediante cálculo de distancia esférica. El radio de tolerancia estándar es de <strong>500 metros</strong>.</p>
<h3>Problemas frecuentes y soluciones</h3>
<ul>
    <li><strong>Señal GPS degradada (galpón o subsuelo):</strong>
        <ol>
            <li>Realice la <strong>entrada</strong> a cielo abierto antes de ingresar a la nave.</li>
            <li>Ejecute los checklists con normalidad en el interior.</li>
            <li>Realice la <strong>salida</strong> en un punto exterior con cobertura GPS despejada.</li>
        </ol>
    </li>
    <li><strong>Ubicación fuera de radio:</strong> La visita se registrará, pero la presencia GPS quedará marcada como <em>no confirmada</em>. El Gestor verá un aviso solicitando justificación manual de la situación.</li>
</ul>

<hr>

<h2>4. Redacción de Informes y Buenas Prácticas Fotográficas</h2>
<h3>Borradores de Gabinete</h3>
<ul>
    <li>Guarde sus avances periódicamente mientras redacta.</li>
    <li>Mientras el informe esté en estado <em>En Redacción</em>, puede editar libremente.</li>
    <li>Al presionar <strong>"Finalizar Redacción"</strong>, los campos pasan a solo lectura y el informe ingresa a la cola de revisión del Gestor.</li>
    <li>Si el Gestor le devuelve el informe con observaciones de corrección, los campos vuelven a habilitarse para que aplique los ajustes requeridos.</li>
</ul>
<h3>Evidencia Fotográfica</h3>
<ul>
    <li><strong>Foto de condición insegura:</strong> Tome la foto a 1-2 metros de distancia para identificar tanto el riesgo específico (ej. cable expuesto) como el contexto del sector.</li>
    <li><strong>Foto de acción correctiva:</strong> Al cambiar el estado de un hallazgo a <em>Resuelto</em>, es obligatorio adjuntar la foto que demuestre la corrección realizada. Sin esta foto, el Gestor no puede proceder al cierre definitivo.</li>
</ul>