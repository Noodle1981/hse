# 🔍 Manual de Usuario: Tratamiento de Desvíos y Hallazgos (v2.2)

Este manual documenta el registro, seguimiento y cierre de **Hallazgos** detectados por el personal técnico de **XamaSafe** en el campo.

---

## 🎯 Visión General
Un Hallazgo es cualquier condición insegura o acto subestándar que requiere acción correctiva. El sistema garantiza que cada hallazgo tenga un responsable, un plazo y una evidencia de cierre con total trazabilidad.

---

## 🎨 Identidad Visual (Pilar Hallazgos)
Los elementos generales de control del centro de hallazgos utilizan acentos en la gama del **Índigo / Azul**. Las alertas de criticidad emplean semaforización cromática: **Crítico / Alto** en Rojo, **Medio** en Amarillo y **Bajo** en Azul.

---

## 🚀 Ciclo del Hallazgo

### 1. Detección y Registro
Durante la **Visita Técnica**:
1. Accede al panel **Hallazgos** dentro de la visita.
2. Pulsa **"Nuevo Hallazgo"** (botón *Tactical Pill* naranja/brand).
3. Completa los datos:
    - **Título**: Resumen corto (ej: "Zapatilla eléctrica sobrecargada").
    - **Descripción técnica**: Detalles de riesgo y recomendación.
    - **Ubicación específica**: Sector de la sede (ej: "Oficina Administrativa").
    - **Criticidad**: Selecciona entre *Bajo*, *Medio*, *Alto* o *Crítico*.
    - **Plazo de Corrección**: Fecha límite para la resolución.
    - **Fotos de Evidencia**: Sube las fotos del desvío en el momento.

### 2. Estados y Seguimiento (Dashboard)
Los hallazgos transitan por los siguientes estados:
- **`Pendiente`**: Hallazgo registrado pero sin acciones (Glow-Rojo).
- **`En Proceso`**: El cliente está trabajando en la mejora (Glow-Amarillo).
- **`Resuelto`**: El cliente informa que se subsanó el desvío.
- **`Verificado`**: El técnico confirma la resolución en una visita posterior.
- **`Cerrado`**: El ciclo de mejora finaliza satisfactoriamente con foto de "después" (Glow-Verde).

### 3. Cierre de Hallazgo
Para cerrar un hallazgo:
1. Navega a **Hallazgos > Seguimiento**.
2. Selecciona el desvío verificado.
3. Adjunta la **Foto de Cierre** como evidencia física e inmutable de la corrección.
4. Cambia el estado a `Cerrado`. El sistema registrará el timestamp y usuario de cierre.

---

## 📱 Responsividad y Adaptabilidad de la Consola
El Centro de Hallazgos implementa diseño adaptable para notebooks y dispositivos móviles:
* **Notebooks / Portátiles:** Reducción de la huella horizontal mediante paddings compactos (`px-4 lg:px-6 xl:px-8 py-6 lg:py-8`) y reducción del ancho mínimo de tabla a `950px` para evitar desbordamientos de pantalla.
* **Transición a Tarjetas Móviles (Breakpoint < 1024px):** Al bajar de `1024px` (`lg` en Tailwind), la tabla se oculta automáticamente (`lg:block hidden`) y se renderiza una lista ordenada de **tarjetas de hallazgos** (`lg:hidden block`) adaptadas para uso táctil.
* **Scrollbar Índigo Táctico:** El contenedor horizontal de la tabla desbordable lleva la clase `.custom-scrollbar` con un scrollbar índigo translúcido visible, advirtiendo al usuario de la posibilidad de desplazarse lateralmente en laptops compactas.

---

## 👤 Roles Relacionados

| Rol | Acción |
|---|---|
| **Técnico** | Registrar hallazgos en visita, adjuntar fotos y verificar cierres. |
| **Gestor** | Diseñar planes de acción, auditar y exportar reportes de hallazgos críticos. |

---

## 💡 Tips de Inspección
- **Vínculo con Visita:** Recuerda que todos los hallazgos deben nacer estrictamente asociados a una visita técnica en progreso para asegurar la trazabilidad del servicio.

---

## 📝 Resolución de Problemas
- **La tabla se ve desordenada en mi notebook:** El sistema reducirá los paddings y mostrará un scrollbar índigo para facilitar la lectura. Si la pantalla es muy pequeña (como un smartphone o tablet), la visual cambiará automáticamente a formato tarjetas.
- **El cliente resolvió el hallazgo pero no tengo fotos:** El desvío debe quedar en estado `Resuelto` hasta que puedas visitar la sede y capturar la evidencia para el estado `Cerrado`.

---
*Manual actualizado para XamaSafe v2.2 | Terminal Táctica Design*
