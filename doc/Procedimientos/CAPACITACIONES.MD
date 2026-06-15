# 📚 Manual de Usuario: Sistema de Capacitaciones (v2.2)

Este manual describe el flujo de trabajo para la programación, dictado y registro de capacitaciones dentro de **XamaSafe**, utilizando la interfaz de la **Terminal Táctica**.

---

## 🎯 Visión General
El módulo de capacitaciones permite a los Gestores planificar la formación anual y a los Técnicos registrar la ejecución y asistencia en tiempo real desde el campo, generando certificados PDF legales firmados electrónicamente.

---

## 🎨 Identidad Visual (Pilar Educación)
Todas las pantallas y elementos interactivos del módulo de capacitación se representan estrictamente con la gama de color **Azul / Índigo** (`blue-500`, `indigo-500`). Si ves componentes con acentos azules o índigos, corresponden al pilar de Educación.

---

## 👤 Roles y Responsabilidades

| Rol | Acción |
|---|---|
| **Gestor (Administrador)** | Diseñar el Plan Anual, programar capacitaciones, auditar asistencias mediante el acordeón de grilla y descargar PDFs de constancias. |
| **Técnico de Campo** | Iniciar la sesión de capacitación heredada del plan o iniciar una capacitación ad-hoc en vivo, registrar firmas de asistentes y firmar como Instructor. |

---

## 🚀 Guía Paso a Paso

### 1. Planificación Anual (Solo Gestores)
1. Navega a **Contrato Anual (PPD)**.
2. Agrega o edita una actividad de capacitación, configurando:
   - **Duración Estimada:** Selector de tiempos fijos (5m, 10m, 15m, 30m, 45m, 1h, 1:30h, 2h, 3h).
   - **Modalidad:** Presencial, Virtual o Híbrida.
   - **Multi-Instructor:** Switch para habilitar co-instructores adicionales.

### 2. Toma de Asistencia y Ejecución (Técnico en Campo)
El Técnico inicia la capacitación desde su visita presencial:
1. En **Mis Asignaciones (Educación)**, visualiza las capacitaciones sugeridas del mes o pulsa `[+] Iniciar Capacitación` para una charla ad-hoc.
2. Al iniciar, el sistema hereda la duración y modalidad del plan anual.
3. Ingresa los datos de los asistentes (Nombre, Puesto y DNI obligatorio) y toma su firma en pantalla en el **Signature Pad**.
4. **Finalización y Firma de Instructor (Optimización Storage vs Base64):**
   - El Técnico (o Instructor Especialista) firma para cerrar la capacitación.
   - **Optimización de Base de Datos:** La firma se captura temporalmente como Base64 en el frontend, pero el backend la decodifica de forma segura y la guarda como un archivo físico `.png` en el storage público (`signatures/`). En la base de datos solo se persiste el path relativo, evitando el crecimiento desmedido de las filas.

### 3. Cierre y Visualización (Gestor)
1. La capacitación se da por finalizada y el estado pasa a `completed`.
2. **Acordeón Táctico de Alta Escala:** En la consola de capacitaciones del Gestor, al presionar `Ver Asistentes (X)` se despliega un sub-acordeón con una tabla minimalista de alta legibilidad (Nombre, Puesto, DNI), lo que evita la saturación visual ante grupos numerosos de más de 30 personas.
3. El Gestor pulsa **"Descargar PDF"** para generar el certificado en caliente con las firmas incrustadas y detalles agregados.

---

## 💡 Tips Tácticos
- **Buscador en Caliente:** Para capacitaciones libres ad-hoc, el técnico cuenta con un buscador reactivo de texto y filtros en cascada de categorías y subcategorías con glows de acento índigo.
- **Herencia Automática:** Las modalidades y duraciones estimadas se auto-configuran desde el plan anual para agilizar la carga del técnico.

---

## 📝 Resolución de Problemas
- **¿Cómo se visualiza la firma del instructor externa?** Si se activa el switch de "Instructor Externo", se captura el nombre del especialista e ingresa al flujo de firma física guardada.
- **La firma del Técnico no aparece:** El sistema jala automáticamente la firma de perfil aprobada del técnico firmante, asegurando total consistencia legal sin trazos manuales repetitivos.

---
*Manual actualizado para XamaSafe v2.2 | Terminal Táctica Design*
