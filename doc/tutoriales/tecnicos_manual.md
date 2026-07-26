# 📋 Manual de Inspección y Operaciones de Campo: Técnicos

---

## 1. Perfil del Técnico & Firma Digital de Perfil

Como **Técnico Inspector de Campo**, su función es constatar las condiciones de Higiene, Seguridad y Medio Ambiente (HyS) en las sedes cliente, registrar evidencias y certificar la presencia física en terreno.

### Carga Inicial de la Firma Digital
Para que sus inspecciones y constancias de visita posean validez legal, debe registrar su firma de perfil:
1. En la esquina superior derecha, haga clic en su usuario y seleccione **"Mi Perfil"**.
2. Diríjase a la sección **"Firma Digital de Perfil"**.
3. Trace su firma sobre el panel táctil (*canvas digital*).
4. Guarde los cambios. Su firma entrará en estado de auditoría `pending`.
5. Una vez aprobada por su Gestor Principal (`approved`), su firma se estampará de forma inmutable en todas las inspecciones que ejecute.

---

## 2. Consola Táctica de Campo en 3 Pilares (`MyAssignments.jsx`)

La consola de operaciones de campo está estructurada en tres secciones tácticas para organizar su jornada de trabajo:

```
┌─────────────────────────────────────────────────────────────┐
│                 CONSOLA DE CAMPO TÁCTICA                    │
├───────────────────┬─────────────────────┬───────────────────┤
│    🎯 MISIONES    │     📚 EDUCACIÓN     │    📊 MÉTRICAS    │
│   (Inspecciones)  │   (Capacitaciones)  │    (Informes)     │
└───────────────────┴─────────────────────┴───────────────────┘
```

### 🎯 Pilar 1: Misiones (Inspecciones)
* Muestra el resumen de checklists y auditorías asignadas para la jornada.
* Muestra indicadores KPI de cumplimiento normativo, checklists completados y ratio de conformidades.
* Permite acceder al **ActionSelector** para iniciar ejecuciones libres o basadas en el Plan Anual.

### 📚 Pilar 2: Educación (Capacitaciones)
* Muestra las charlas de seguridad (ej. Charla de 5 Minutos) y capacitaciones asignadas por sede.
* Permite tomar asistencia en pantalla registrando participantes con DNI y firma digital.

### 📊 Pilar 3: Métricas (Informes)
* Espacio para redactar borradores de Informes Maestros mensuales y registrar avances de gabinete.

---

## 3. Control de Visitas con Validación GPS (Proof of Presence)

Todas las inspecciones deben ejecutarse bajo una **Visita Presencial Activa** que certifique su presencia física en la planta del cliente.

### Paso 1: Check-in (Entrada a Sede)
1. En la consola, ingrese a **"Mis Visitas"** y seleccione la Sede Cliente.
2. Presione el botón táctico **"Iniciar Visita (Check-in)"**.
3. El navegador solicitará acceso a su ubicación GPS. Otorgue el permiso obligatoriamente.
4. El sistema registrará sus coordenadas de ingreso (`checkin_lat`, `checkin_lng`) y validará que se encuentre dentro del radio de tolerancia de la sede (ej. 500 metros).

### Paso 2: Check-out (Salida de Sede)
1. Al finalizar las inspecciones y charlas del día, presione **"Finalizar Visita (Check-out)"**.
2. El sistema capturará nuevamente sus coordenadas (`checkout_lat`, `checkout_lng`), calculará las **Horas de Campo** reales trabajadas y marcará la verificación GPS (`gps_verified = true`).

> 🏔️ **Modalidad Roster Minero 24/7:** Si la sede opera bajo modalidad minera o continua, las restricciones de días u horarios no laborales quedan omitidas automáticamente.

---

## 4. Asistente de Llenado de Checklists (Wizard)

### 🕒 Inputs Temporales Nativos
En preguntas con medición temporal, el wizard dispone de inputs especializados:
* **Campos de Hora (`time`) y Fecha/Hora (`datetime`):** Adaptados a pantallas táctiles.
* **Botones de Llenado Rápido:** Presione **"🕒 Hora Actual"** o **"📅 Ahora"** para inyectar automáticamente la fecha y hora del dispositivo con un solo toque.

### ⌚ Tiempo Real e Inmutabilidad (Sync Servidor)
* En la cabecera del Wizard visualizará el badge **"⌚ Tiempo Real (Sync Servidor)"**.
* **Seguridad SRE:** Para evitar manipulaciones de horario en campo, el servidor de Laravel descarta cualquier marca de fecha o hora enviada desde el dispositivo cliente y aplica estrictamente el timestamp `now()` del servidor al guardar.

### 🚨 Modal de Intercepción por Falla Crítica
Si durante el llenado de una sección responde una opción marcada como desvío grave (falla crítica):
1. Al presionar **"Siguiente Sección"**, el sistema desplegará un modal HUD táctico de advertencia en rojo.
2. Dispondrá de dos opciones:
   * **Continuar Inspección:** Le permite seguir evaluando los puntos restantes de la auditoría.
   * **Rechazar y Finalizar:** Dispara un flujo de autocompletado en cascada que llena las preguntas restantes no respondidas con la leyenda `"No evaluado por falla crítica"` y lo lleva directamente al cierre de firmas para una finalización inmediata por emergencia.

---

## 5. Captura y Gestión de Evidencias de Campo (Hallazgos e Incidentes)

### Reporte Rápido en Visita Activa
Si durante un recorrido detecta una condición insegura:
1. Abra el formulario de **Nuevo Hallazgo** o **Nuevo Incidente**.
2. Si la visita está activa, la Sede se fijará automáticamente (**"📍 Fijo por Visita"**) para evitar errores de asociación.
3. Al guardar, el sistema lo devolverá automáticamente a la consola de su visita en progreso.

### 📸 Evidencia Fotográfica Acumulativa
* **Carga Secuencial:** Puede seleccionar y subir fotos secuencialmente (una por una) sin que las selecciones anteriores se borren.
* **Remoción en Previsualización:** Cada miniatura de foto incluye un botón flotante rojo **"✕"** que le permite eliminar fotos equivocadas antes de enviar el reporte.

---

## 6. Ejecución de Capacitaciones en Vivo

1. Seleccione la capacitación asignada e inicie la sesión en vivo.
2. **Registro de Asistentes:** Presione **"+ Agregar Asistente"**.
3. Ingrese Nombres, Apellidos, Puesto y **Nº de Documento (DNI)** obligatorio.
4. Solicite al asistente que firme directamente en la pantalla con el dedo o stylus.
5. Al finalizar la lista, presione **"Finalizar Capacitación"** para registrar la duración real mediante el cronómetro interactivo y cerrar la sesión.
