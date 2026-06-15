# 📅 Manual de Usuario: Planificación de Servicios (v2.2)

Este manual guía a los **Gestores** y **Técnicos** en el uso del tablero de **Planificación Anual (PPD)**, el cerebro estratégico del servicio de Higiene y Seguridad en **XamaSafe**.

---

## 🎯 Visión General
El Tablero de Planificación Anual permite visualizar y auditar de un vistazo el cumplimiento mensual de las visitas técnicas, inspecciones y capacitaciones pactadas con cada cliente a lo largo del año. Es la herramienta para asegurar que no se pierdan compromisos contractuales obligatorios.

---

## 👤 Roles y Responsabilidades

| Rol | Acción |
|---|---|
| **Gestor (Administrador)** | Configurar y editar las coberturas del Plan Anual, definiendo duraciones, modalidades (Presencial, Virtual, Híbrida) y co-instructores. |
| **Técnico de Campo** | Consultar las obligaciones sugeridas por el plan para sus sedes asignadas desde su HUD de asignaciones del mes. |

---

## 🚀 Operación del Tablero y Grilla Adaptable

### 1. Acceso y Consulta
1. Navega a **Plan Anual** o **Contrato Anual**.
2. Selecciona la **Empresa** y la **Sede** correspondiente.
3. Verás la grilla de la matriz anual (Enero a Diciembre) con las actividades planificadas.

### 2. Edición y Parametrización (Solo Gestores)
Para ajustar el plan de un cliente:
1. Pulsa el botón **"Activar Modo Edición"**.
2. Modifica o agrega actividades seleccionando el checklist o tema, y completando:
   - **Duración Estimada:** Tiempos fijos (de 5m a 3h).
   - **Modalidad:** Presencial, Virtual u Híbrida.
   - **Multi-Instructor:** Switch para soporte de co-instructores.
3. Haz clic en los meses para programar (ej. Enero, Febrero, Julio).
4. Pulsa **"Guardar Plan Anual"**.

### 3. Responsividad y Navegación HUD en Pantallas
Para laptops y celulares, el tablero implementa adaptabilidad premium:
* **Notebooks / Escritorio Compacto:** El contenedor ajusta márgenes a `p-4 sm:p-6 lg:p-8 xl:p-12` para optimizar el espacio horizontal y evitar scroll innecesario.
* **Móviles y Tablets (Scroll con Columna Fija):** 
  - La primera columna (nombre de la actividad) se congela (`sticky left-0`) para no perder de vista qué actividad estás viendo al deslizar.
  - El ancho de la columna fija es dinámico (`w-[140px] sm:w-[220px] md:w-[300px]`) con paddings compactos para que en móviles no cubra toda la pantalla.
  - **Scrollbar Naranja de Marca:** El contenedor horizontal de la matriz lleva la clase `.custom-scrollbar` con una barra naranja translúcida de marca visible, guiando intuitivamente sobre la posibilidad de deslizar hacia la derecha.

---

## 🎨 Experiencia "Terminal Táctica"
- **Seguimiento Semafórico:**
  - *Cuadro Vacío:* Sin actividad programada.
  - *Borde Naranja:* Actividad programada pendiente del mes.
  - *Fondo Naranja Sólido:* Actividad realizada y aprobada en tiempo y forma (con resplandor de éxito `glow-text`).

---

## 💡 Tips de Gestión
- **Vínculo Automático:** No necesitas marcar el cumplimiento manual. El sistema detecta cuando finalizas un checklist o capacitación asociada en el periodo y actualiza la matriz.
- **Exportación en Caliente:** Permite exportar a PDF para presentarlo ante auditorías externas o ART.

---

## 📝 Resolución de Problemas
- **La matriz no se desplaza en mi celular:** Asegúrate de deslizar sobre las columnas de los meses (a la derecha de la columna fija de actividades). El scrollbar naranja inferior te indicará si hay más meses por ver.
- **No puedo ver el botón de edición:** Está restringido al rol Gestor o Administrador. Los técnicos y clientes corporativos solo acceden en modo lectura.

---
*Manual actualizado para XamaSafe v2.2 | Terminal Táctica Design*
