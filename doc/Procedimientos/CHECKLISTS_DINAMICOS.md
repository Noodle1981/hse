# 📝 Manual de Usuario: Auditorías e Inspecciones (v1.5)

Este manual explica cómo construir formularios de control y realizar inspecciones técnicas en campo utilizando el motor de **Checklists Dinámicos** de **XamaSafe**.

---

## 🎯 Visión General
El sistema permite digitalizar cualquier tipo de inspección técnica, eliminando el uso de formularios en papel y centralizando los resultados en reportes automáticos con validez legal.

---

## 👤 Roles y Responsabilidades

| Rol | Acción |
|---|---|
| **Gestor (Administrador)** | Crear "Plantillas de Checklist", gestionar puntos de control y asignar inspecciones a sedes permanentes. |
| **Técnico de Campo** | Completar el "Paso a Paso" de la inspección, registrar desvíos y adjuntar evidencias. |

---

## 🚀 Guía Paso a Paso

### 1. Construcción del Formulario (Solo Gestores)
Para crear una nueva herramienta de control:
1. Dirígete a **Configuración > Catálogo de Checklists**.
2. Pulsa en **"Crear Plantilla"**.
3. Define los **Puntos de Control**:
    - Agrega preguntas claras (ej: "¿El extintor está cargado?").
    - Selecciona el tipo de respuesta: *Si/No*, *Numérico*, o *Texto Libre*.
4. Guarda la plantilla. Ahora estará lista para ser usada en cualquier visita.

### 2. Ejecución de la Inspección (Técnico)
Durante la visita técnica:
1. Accede a la **Visita en Progreso**.
2. En la sección de Auditorías, selecciona el checklist correspondiente.
3. Se abrirá el **Wizard de Inspección**:
    - Responde a cada punto de control.
    - Si detectas un incumplimiento, marca el estado como **"No Cumple"**.
    - El sistema te pedirá automáticamente una **Observación** y la opción de registrar un **Hallazgo** asociado.
4. Una vez completado el 100% de los puntos, pulsa **"Finalizar Inspección"**.

---

## 🎨 Interfaz Táctica
- **Modo Glassmorphism**: Los formularios se presentan en capas de cristal para no perder de vista el contexto de la visita.
- **Micro-animaciones**: Verás una barra de progreso sutil que se llena a medida que completas el checklist.
- **Feedback Inmediato**: Los ítems con falla resaltan con un sutil brillo rojo técnica (`glow-text-red`).

---

## 💡 Tips de Eficiencia
- **Auto-asignación**: Si estás en una planta y decides hacer un control que no estaba planeado, usa la opción **"Agregar Checklist ad-hoc"** para elegir del catálogo sobre la marcha.
- **Historial Quick-View**: Puedes consultar cómo se respondió este mismo checklist en la visita anterior pulsando el icono de reloj junto a la pregunta.

---

## 📝 Resolución de Problemas
- **¿Puedo editar una inspección terminada?** No, una vez que la inspección se cierra, los datos quedan sellados para garantizar la integridad de la auditoría. Si hubo un error, debe registrarse una nota aclaratoria en la observación general de la visita.
- **No aparece el checklist que busco**: Asegúrate de que el Gestor haya asignado la plantilla a la sede o que esté marcada como "Global" en el catálogo.

---
*Manual actualizado para XamaSafe v1.5 | Terminal Táctica Design*
