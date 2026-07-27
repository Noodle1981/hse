<h1>Procedimientos Operativos: Gestores</h1>

<h2>1. Ciclo de Vida del Informe Maestro</h2>
<p>El <strong>Informe Maestro</strong> es el documento legal y técnico consolidado que la Consultora entrega mensualmente a la empresa cliente para certificar el estado de cumplimiento de la planta.</p>

<h3>Flujo de Estados</h3>
<pre>
  ┌──────────────┐      ┌─────────────────┐      ┌──────────────────┐      ┌────────────┐
  │   PENDIENTE  │ ──► │  EN REDACCIÓN  │ ──► │  PARA REVISIÓN  │ ──► │  APROBADO  │
  │ (Por Gestor) │      │  (Por Técnico) │      │ (Revisión Gestor)│      │  (Cierre) │
  └──────────────┘      └─────────────────┘      └────────┬─────────┘      └────────────┘
                                                           │ Devolución con
                                                           │ observaciones
                                                           └─────────┘
</pre>

<h3>Procedimiento de Auditoría</h3>
<ol>
    <li><strong>Paso 1 (Solicitud):</strong> El Gestor crea la solicitud del informe indicando el período, la empresa cliente, la sede y el técnico redactor asignado. El informe nace en estado <em>Pendiente</em>.</li>
    <li><strong>Paso 2 (Revisión):</strong> Cuando el Técnico finaliza la redacción, el informe pasa a estado <em>Para Revisión</em>. El Gestor puede entonces:
        <ul>
            <li><strong>Devolver con observaciones:</strong> Si detecta inconsistencias, el Gestor escribe las correcciones necesarias y presiona <strong>"Devolver al Técnico"</strong>. El informe vuelve a estado <em>En Redacción</em> y el Técnico puede editar nuevamente.</li>
            <li><strong>Aprobar el Informe:</strong> Si el informe es conforme, el Gestor presiona <strong>"Aprobar Informe"</strong>. El estado pasa a <em>Aprobado</em> y el documento queda sellado de forma permanente.</li>
        </ul>
    </li>
    <li><strong>Paso 3 (Inmutabilidad):</strong> Al aprobarse, el PDF vincula directamente la firma de perfil aprobada del Técnico, garantizando la autenticidad legal del documento.</li>
</ol>

<hr>

<h2>2. Monitoreo de Inspecciones y Desvíos Críticos</h2>
<h3>Consola de Historial</h3>
<ol>
    <li>Ingrese a <strong>"Historial de Inspecciones"</strong> en el menú lateral.</li>
    <li>Semáforo de cumplimiento:
        <ul>
            <li>🟢 <strong>Aprobó (Verde):</strong> Inspección completada sin hallazgos ni desvíos normativos.</li>
            <li>🔴 <strong>Falló ⚠️ (Rojo):</strong> Se detectó una no-conformidad crítica.</li>
        </ul>
    </li>
    <li>Si el técnico activó el flujo de intercepción por falla crítica en campo, el checklist mostrará una insignia indicando que las preguntas posteriores fueron marcadas automáticamente como no evaluadas.</li>
</ol>

<hr>

<h2>3. Emisión de Constancias y Certificados PDF</h2>
<h3>Consolidación Automática</h3>
<p>Para evitar la saturación de documentos cuando un técnico inspecciona múltiples activos el mismo día, el sistema agrupa automáticamente las inspecciones por fecha y sede, generando una única Constancia de Visita Principal por jornada.</p>
<h3>Documentos Descargables</h3>
<ul>
    <li><strong>Constancia de Visita:</strong> PDF consolidado con desglose de horas de campo, horas de gabinete y firma del responsable en planta.</li>
    <li><strong>Certificado de Capacitación:</strong> Acta de asistencia oficial con lista nominal de trabajadores, sus DNI y firmas digitalizadas.</li>
</ul>

<hr>

<h2>4. Gestión y Cierre de Hallazgos</h2>
<p>La transición de estados de los hallazgos sigue una política estricta de permisos.</p>
<h3>Tabla de Permisos</h3>
<table>
    <thead>
        <tr><th>Estado Objetivo</th><th>Quién puede aplicarlo</th><th>Requisito</th></tr>
    </thead>
    <tbody>
        <tr><td>En Proceso</td><td>Técnico / Gestor</td><td>Inicio de tareas de mitigación</td></tr>
        <tr><td>Resuelto</td><td>Técnico / Gestor</td><td>Carga de foto de evidencia "después"</td></tr>
        <tr><td><strong>Cerrado</strong></td><td><strong>Exclusivo Gestor</strong></td><td>Auditoría final de conformidad aprobada</td></tr>
        <tr><td><strong>Verificado</strong></td><td><strong>Exclusivo Gestor</strong></td><td>Constatación presencial en la siguiente visita</td></tr>
    </tbody>
</table>
<h3>Procedimiento de Cierre por el Gestor</h3>
<ol>
    <li>Ingrese a la consola de <strong>"Hallazgos"</strong>.</li>
    <li>Seleccione el hallazgo en estado <em>Resuelto</em>.</li>
    <li>Valide la prueba fotográfica de corrección presentada.</li>
    <li>Si la solución es satisfactoria, presione <strong>"Cerrar Auditoría"</strong>.</li>
    <li>El sistema registrará automáticamente su identificación y la marca de tiempo de cierre en el historial de seguimiento del hallazgo.</li>
</ol>
