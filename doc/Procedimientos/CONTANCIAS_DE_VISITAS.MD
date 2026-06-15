# 📑 Manual de Usuario: Gestión de Visitas Técnicas (v2.2)

Este manual documenta el ciclo de vida completo de un servicio en campo, desde el acceso al panel táctico hasta la generación de la **Constancia de Visita Digital**.

---

## 🎯 Visión General
La Visita Técnica (`VisitCertificate`) es el "contenedor maestro" de la actividad del día. Dentro de ella se integran las inspecciones, las capacitaciones, los hallazgos y los incidentes, consolidándolos en un único documento legal firmado digitalmente por el responsable del cliente.

---

## 🚀 Ciclo de la Visita y Control de Presencia

### 1. Check-in (Entrada) con Validación GPS
Al llegar a la planta o sede:
1. Accede a tu dashboard de **Técnico**.
2. Selecciona la visita programada del día.
3. Pulsa el botón **"Iniciar Visita"**.
4. **Prueba de Presencia (GPS):** El sistema utiliza la API de geolocalización del dispositivo para capturar `checkin_lat` y `checkin_lng`. Compara la distancia terrestre con las coordenadas registradas de la Sede física utilizando la **Fórmula de Haversine** contra el `radio_tolerancia_metros` (por defecto 500 metros) configurado por la sede.

### 2. Control Inteligente de Jornada (Roster Minero vs Standard)
* **Modalidad Roster Minero 24/7 (`es_roster_24_7 = true`):** Diseñado para operaciones continuas en alta montaña o minería. Omite todas las restricciones de horarios y calendarios, autorizando ingresos los fines de semana y días festivos de forma automática.
* **Modalidad Estándar:** Si está desactivado, el sistema audita en tiempo real que los ingresos del técnico ocurran en los días (`dias_operativos`) y horarios (`hora_apertura` / `hora_cierre`) parametrizados por la sede, disparando alertas tácticas si se detecta sobrejornada.

### 3. Ejecución de Tareas en Campo
Durante la visita presencial, el técnico puede ingresar a:
- **Misiones (Inspecciones):** Checklists técnicos.
- **Educación (Capacitaciones):** Charlas teóricas con firmas de operarios.
- **Métricas (Informes):** Redacción de reportes mensuales.

### 4. Check-out (Salida) y Auditoría Horaria
Al terminar la jornada:
1. Pulsa **"Finalizar Visita"**.
2. Se realiza una nueva captura de geolocalización (`checkout_lat` y `checkout_lng`).
3. Completa la nota de observaciones y el nombre del responsable del cliente.
4. **Firma Digital en Caliente:** El cliente firma en el canvas digital de la tablet.
5. **Registro Horario:**
   - **Horas de Campo:** Diferencia de tiempo real entre el Check-in y el Check-out.
   - **Horas de Gabinete:** Permite registrar un estimativo de horas de oficina dedicadas a la confección del informe (`horas_gabinete_estimadas`).

---

## 👤 Roles Relacionados

| Rol | Acción |
|---|---|
| **Gestor** | Programar las visitas, definir coordenadas GPS y radio de tolerancia de las sedes. |
| **Técnico** | Realizar check-in/check-out GPS, cargar tareas y recolectar firma de salida del cliente. |

---

## 🎨 Consola Histórica Agrupada (Bóveda de Certificados)
Para evitar la saturación visual en la grilla del historial de visitas (`History.jsx`):
* **Agrupación Inteligente:** Las inspecciones y visitas completadas el mismo día en la misma sede se agrupan en una única fila centralizada de jornada.
* **Sub-Acordeón Detallado:** Al presionar `Ver Checklists (X)`, se despliega un subpanel con animaciones HUD que lista cada checklist individual, hora de finalización, supervisor firmante y badge semafórico de resultado.
* **Scrollbar Naranja de Contrato:** Si la pantalla de la notebook es angosta, el listado permite desplazamiento horizontal mediante un scrollbar naranja de marca para mantener fluida la interfaz.

---

## 💡 Tips de Campo
- **Multitasking**: Puedes saltar entre un checklist y una capacitación sin perder el progreso. Todo se guarda en el momento.
- **Conectividad:** Asegura que tu navegador móvil tenga activado el permiso de localización GPS permanente; de lo contrario, el sistema impedirá iniciar la visita técnica.

---

## 📝 Resolución de Problemas
- **El Check-in da error de GPS:** Asegúrate de estar dentro del radio de tolerancia configurado para la sede. Si estás en la planta y sigues bloqueado, un Gestor deberá revisar y ampliar el radio de tolerancia de la sede en metros.
- **La visita no tiene Constancia:** Si la visita se cerró sin firma por algún inconveniente técnico, se visualizará un botón gris `"Sin Constancia"` como advertencia.

---
*Manual actualizado para XamaSafe v2.2 | Terminal Táctica Design*
