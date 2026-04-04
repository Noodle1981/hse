# 📚 Manual de Usuario: Sistema de Capacitaciones (v1.5)

Este manual describe el flujo de trabajo para la programación, dictado y registro de capacitaciones dentro de **XamaSafe**, utilizando la interfaz **Terminal Táctica**.

---

## 🎯 Visión General
El módulo de capacitaciones permite a los Gestores programar sesiones de formación y a los Técnicos registrar la asistencia en tiempo real desde el campo, generando certificados PDF legales de forma automática.

---

## 👤 Roles y Responsabilidades

| Rol | Acción |
|---|---|
| **Gestor (Administrador)** | Programar capacitaciones, gestionar plantillas y descargar certificados consolidados. |
| **Técnico de Campo** | Acceder a la capacitación asignada, tomar asistencia con firma digital y cerrar el registro. |

---

## 🚀 Guía Paso a Paso

### 1. Programación (Solo Gestores)
Para iniciar un nuevo ciclo de formación:
1. Navega a la sección **Capacitaciones** desde el menú principal.
2. Pulsa el botón **"Nueva Capacitación"** (identificado con el estilo *Tactical Pill* naranja).
3. Selecciona la **Empresa** y la **Sede** correspondiente.
4. Define si la capacitación será **Presencial** (en planta) o **Virtual**.
5. Asigna un **Técnico Responsable** para que la visualice en su dashboard.
6. Guarda la sesión. Verás que queda en estado `Programada` con un panel de estilo *Glassmorphism*.

### 2. Toma de Asistencia (En Campo)
El Técnico responsable debe seguir estos pasos durante la sesión:
1. Busca la capacitación en el panel de **Tareas del Día**.
2. Presiona el botón de **"Registro de Asistencia"**.
3. Verás el **Attendance Tracker** táctico.
4. Por cada asistente:
    - Ingresa el **Nombre Completo**.
    - Ingresa el **DNI** (validación automática).
    - Solicita al asistente que firme en el **Digital Signature Pad**.
    - *Tip Táctico*: La firma utiliza un trazo blanco de alta visibilidad sobre fondo oscuro para asegurar claridad.
5. Pulsa **"Guardar Asistente"**. El contador se actualizará automáticamente.

### 3. Cierre y Certificación
Una vez finalizada la sesión:
1. Verifica que la lista de asistentes sea correcta en la tabla de alta fidelidad.
2. Pulsa el botón **"Generar Certificado PDF"**.
3. El sistema integrará el branding de la consultora y todas las firmas capturadas en un documento legal inviolable.

---

## 💡 Tips Tácticos
- **Persistencia de Datos**: Si pierdes conexión en medio del registro, el *Attendance Tracker* mantiene los datos cargados en el navegador para evitar pérdidas.
- **Validación Anti-duplicados**: El sistema impedirá registrar dos veces el mismo DNI en una misma sesión.
- **Modo Tablet**: La interfaz está optimizada para ser usada con el pulgar mientras caminas por la planta.

---

## 📝 Resolución de Problemas
- **¿Qué pasa si un asistente no tiene DNI a mano?** Debe proporcionarlo verbalmente, ya que es un campo requerido para la validez legal del certificado.
- **La firma no se ve clara**: Utiliza el botón **"Limpiar"** y repite el trazo. Asegúrate de presionar el stylus o dedo con firmeza en el pad.

---
*Manual actualizado para XamaSafe v1.5 | Terminal Táctica Design*
