# ⚡ Manual de Procedimientos de Trabajo en Terreno: Técnicos

---

## 1. Protocolo de Sincronización Inmutable de Tiempo (`Sync Servidor ⌚`)

Para mantener la máxima integridad técnica y legal en auditorías regulatorias (ISO, ART, Ministerio de Trabajo), XamaSafe implementa un sistema de **Blindaje e Inmutabilidad de Tiempos**.

### Principio de Funcionamiento Servidor
1. **Descarte de Tiempos del Cliente:** Al finalizar un checklist o registrar una visita, el backend de Laravel ignora deliberadamente cualquier valor de hora o fecha proveniente del reloj de su teléfono, tablet o laptop.
2. **Timestamp Inmutable:** El servidor aplica de forma estricta y automática el timestamp exacto del momento de recepción (`now()`).
3. **Auditoría Activa SRE:** Si se detecta un intento de bypass o alteración del reloj del dispositivo cliente, la plataforma registra una alerta SRE en la bitácora de auditoría (`laravel.log`), guardando la IP, el ID del técnico y el payload descartado para prevenir inconsistencias en inspecciones legales.

---

## 2. Procedimiento de Trabajo en Sedes Roster Minero 24/7

Las sedes ubicadas en campamentos mineros, plantas petroquímicas o instalaciones de operación continua operan bajo el régimen de **Roster Minero 24/7**.

### Operación en Terreno
* **Validación Automática:** Al realizar Check-in en una sede configurada con Roster Minero, el sistema omite las alertas ordinarias de días u horarios no laborales.
* **Ingresos Continuos:** Puede registrar visitas e inspecciones a cualquier hora del día o la noche, incluyendo domingos y feriados, sin que el sistema dispare advertencias de sobrejornada en la consola de mando.

---

## 3. Procedimiento de Contingencia ante Desvíos GPS o Conectividad

### Cálculo de Cercanía (Haversine)
1. Al realizar Check-in o Check-out, la plataforma compara su ubicación GPS contra las coordenadas registradas de la Sede utilizando la fórmula matemática de **Haversine**.
2. **Radio de Tolerancia:** El límite estándar configurado por sede es de **500 metros**.

### Contingencias Frecuentes
* **Interferencia Estructural / Naves Industriales:** Si se encuentra dentro de un galpón de estructura metálica o subsuelo donde la señal GPS se degrada:
  1. Realice la apertura de visita (**Check-in**) a cielo abierto antes de ingresar a la nave.
  2. Ejecute los checklists de forma habitual.
  3. Al retirarse, realice el **Check-out** en un punto con cobertura GPS despejada.
* **Inconsistencia de Ubicación:** Si la ubicación calculada excede el radio de tolerancia, la visita se registrará pero marcará `gps_verified = false`. El Gestor visualizará un aviso de verificación manual para requerir su justificación.

---

## 4. Guía de Redacción de Borradores y Captura Fotográfica

### Redacción de Borradores de Gabinete
* Cuando redacte Informes Maestros asignados por su Gestor, guarde sus avances periódicamente.
* Mientras el informe esté en estado `'en_redaccion'`, puede editar el texto libremente.
* Al presionar **"Finalizar Redacción"**, los campos se volverán de solo lectura (`readOnly`) y el informe pasará a la cola de revisión del Gestor. Si el Gestor le devuelve el informe con observaciones (`observacion_revision`), los campos volverán a habilitarse para que aplique los ajustes requeridos.

### Buenas Prácticas de Captura Fotográfica
1. **Evidencia Fotográfica de Hallazgos:** Tome la foto a una distancia adecuada (1 a 2 metros) que permita identificar tanto el riesgo específico (ej. cable expuesto) como el contexto de la máquina o sector.
2. **Foto de "Después" en Hallazgos Resueltos:** Al cambiar el estado de un hallazgo a `resuelto`, es obligatorio adjuntar la foto que demuestre la acción correctiva efectuada para que el Gestor pueda proceder al cierre definitivo (`cerrado`).
