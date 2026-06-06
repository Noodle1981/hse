# 🔍 Manual de Usuario: Tratamiento de Desvíos y Hallazgos (v1.5)

Este manual documenta el registro, seguimiento y cierre de **Hallazgos** detectados por el personal técnico de **XamaSafe** en el campo.

---

## 🎯 Visión General
Un Hallazgo es cualquier condición insegura o acto subestándar que requiere acción correctiva. El sistema garantiza que cada hallazgo tenga un responsable, un plazo y una evidencia de cierre.

---

## 🚀 Ciclo del Hallazgo

### 1. Detección y Registro
Durante la **Visita Técnica**:
1. Accede al panel **Hallazgos** dentro de la visita.
2. Pulsa **"Nuevo Hallazgo"** (botón *Tactical Pill* naranja).
3. Completa los datos:
    - **Título**: Resumen corto (ej: "Zapatilla eléctrica sobrecargada").
    - **Descripción técnica**: Detalles de riesgo y recomendación.
    - **Ubicación específica**: Sector de la planta (ej: "Oficina Administrativa").
    - **Criticidad**: Selecciona entre *Bajo*, *Medio*, *Alto* o *Crítico*.
    - **Plazo de Corrección**: Fecha límite sugerida para la resolución.
    - **Fotos de Evidencia**: Sube hasta 2 fotos del desvío en el momento.

### 2. Estados y Seguimiento (Dashboard)
Los hallazgos pueden transitar por los siguientes estados:
- **`Pendiente`**: Hallazgo registrado pero sin acciones iniciadas (Glow-Rojo).
- **`En Proceso`**: El cliente está trabajando en la mejora (Glow-Naranja).
- **`Resuelto`**: El cliente informa que se subsanó el desvío.
- **`Verificado`**: El técnico confirma la resolución en una visita posterior.
- **`Cerrado`**: El ciclo de mejora finaliza satisfactoriamente (Glow-Verde).

### 3. Cierre de Hallazgo
Para cerrar un hallazgo:
1. Navega a **Hallazgos > Seguimiento**.
2. Selecciona el desvío verificado.
3. El técnico debe adjuntar una **Foto de Cierre** como evidencia de la corrección.
4. Cambia el estado a `Cerrado`. El sistema estampará un sello de tiempo y usuario responsable.

---

## 👤 Roles Relacionados

| Rol | Acción |
|---|---|
| **Técnico** | Registrar hallazgos en visita, adjuntar fotos y verificar cierres. |
| **Gestor** | Revisar estados de hallazgos críticos de todas las empresas y emitir avisos. |

---

## 🎨 Experiencia "Terminal Táctica"
- **High-Contrast Evidence**: Al visualizar fotos de hallazgos, el sistema aplica un filtro de claridad sobre fondos oscuros para resaltar los detalles técnicos.
- **Micro-indicadores de Riesgo**: Verás un icono de alerta con resplandor (`glow-text-red`) para hallazgos de criticidad **Crítico** que superen el plazo de resolución.

---

## 💡 Tips de Inspección
- **Descripción Clara**: Escribe pensando en el cliente. El hallazgo debe ser fácil de entender para quien debe arreglarlo.
- **Vínculo con Visita**: Recuerda que *no puedes crear hallazgos aislados*. Siempre deben nacer de una visita en progreso para asegurar la trazabilidad.

---

## 📝 Resolución de Problemas
- **¿Qué pasa si un hallazgo es recurrente?**: Registra un nuevo hallazgo mencionando la reincidencia en la descripción y adjunta las fotos actuales. No reabras el anterior si ya fue cerrado.
- **El cliente resolvió el hallazgo pero no tengo fotos**: El desvío debe quedar en estado `Resuelto` hasta que puedas visitar la sede y capturar la evidencia para el estado `Cerrado`.

---
*Manual actualizado para XamaSafe v1.5 | Terminal Táctica Design*
