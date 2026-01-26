# ⛏️ Caso de Uso: Minera del Sol (Prueba Integral de CRUDs)

Este documento detalla los datos que debes ingresar manualmente en el sistema para probar el flujo completo de gestión de seguridad en una empresa minera.

## 1. 🏁 Datos Iniciales (Ya creados por el Seeder)
Usa estas credenciales para comenzar la carga de datos.
*   **Usuario Gestor:** `gestorep@ep.com`
*   **Contraseña:** `password`
*   **Empresa:** Minera del Sol S.A.
*   **Consultora:** XamaSafe Consulting

---

## 2. 📝 Guía de Carga Manual

### A. Gestión de Usuarios (Empresas -> Usuarios)
Primero, crea al personal operativo.
1.  **Crear Técnico de Seguridad:**
    *   *Nombre:* Pedro Roca
    *   *Email:* `tecnicoep@ep.com`
    *   *Rol:* Técnico

### B. Matriz de Riesgos (Ejecuciones Avanzadas -> Matrices)
Define los problemas que vamos a controlar.
*   **Título:** Matriz Operaciones Mina Subterránea
*   **Actividad:** Extracción
*   **Puesto:** Operador de Jumbo
*   **Riesgos a Cargar:**
    1.  **⚠️ Riesgo 1: Desprendimiento de Rocas**
        *   *Peligro:* Caída de rocas desde techo/hastiales
        *   *Tipo:* Mecánico
        *   *Probabilidad:* 3 | *Severidad:* 5 (Catastrófico) -> **Nivel: 15 (Alto)**
    2.  **⚠️ Riesgo 2: Atmósfera Contaminada**
        *   *Peligro:* Inhalación de Gases (CO, NO2)
        *   *Tipo:* Químico
        *   *Probabilidad:* 4 | *Severidad:* 4 (Mayor) -> **Nivel: 16 (Alto)**

### C. Configuración (Checklists -> Categorías / Items)
Prepara los elementos base.
1.  **Nueva Categoría:**
    *   *Nombre:* Seguridad Minera
    *   *Color:* #8B4513 (Marrón)
2.  **Items EPP (Inventario):**
    *   *Item 1:* Casco Minero con Lámpara LED (Stock: 50)
    *   *Item 2:* Auto-rescatador (Stock: 30)

### D. Creación de Checklists (Checklists -> Constructor)
Aquí es donde vinculas los controles a los riesgos creados.

#### 📋 Checklist 1: "Inspección de Frente de Avance"
*   *Categoría:* Seguridad Minera
*   **Sección 1: Geomecánica**
    *   *Pregunta:* "¿Se ha realizado la acuñadura (desatado) del techo?"
        *   *Tipo:* Si/No
        *   *Vincular a Riesgo:* **Desprendimiento de Rocas** (Si responde NO -> Falla control)
    *   *Pregunta:* "¿Pernos de anclaje instalados según patrón?" (Si/No)

#### 📋 Checklist 2: "Monitoreo de Ventilación"
*   *Categoría:* Seguridad Minera
*   **Sección 1: Gases**
    *   *Pregunta:* "Concentración de Monóxido (CO) en ppm"
        *   *Tipo:* Numérico (Max: 25)
        *   *Vincular a Riesgo:* **Atmósfera Contaminada**
    *   *Pregunta:* "¿Manga de ventilación extendida?" (Si/No)

### E. Capacitaciones (Administración -> Capacitaciones)
Crea cursos necesarios para operar.
1.  **Para Checklist 1 (Frente de Avance):**
    *   *Título:* "Técnicas de Acuñadura Segura"
    *   *Vincular a:* Checklist "Inspección de Frente de Avance"
2.  **Para Checklist 1 (Frente de Avance):**
    *   *Título:* "Sistemas de Sostenimiento (Pernos y Mallas)"
    *   *Vincular a:* Checklist "Inspección de Frente de Avance"
3.  **Para Checklist 2 (Ventilación):**
    *   *Título:* "Uso de Detectores de Gases Portátiles"
    *   *Vincular a:* Checklist "Monitoreo de Ventilación"

### F. Plan de Contrato Anual (Ejecuciones Avanzadas -> Plan Anual)
Establece las metas para el cliente.
*   **Título:** Programa de Gestión de Ventilación 2026
*   **Objetivo:** Asegurar calidad de aire en frentes de trabajo.
*   **Actividades:**
    *   *Actividad 1:* Auditoría Trimestral de Ventiladores Principales.
    *   *Actividad 2:* Recertificación de Detectores de Gases.

---

## 3. 🧪 Prueba de Funcionalidad (El "Disparo")
Una vez cargado todo:

1.  **Entra como Técnico** (`tecnico@minera.com`).
2.  Realiza la **checklist "Inspección de Frente de Avance"**.
3.  En la pregunta de acuñadura, responde **NO**.
4.  **Resultado esperado:**
    *   La inspección se marca como **REPROBADA**.
    *   En la Matriz de Riesgos, el riesgo "Desprendimiento de Rocas" debería mostrar un aumento en la frecuencia de fallos.
    *   Se debería generar una alerta en el Dashboard.
