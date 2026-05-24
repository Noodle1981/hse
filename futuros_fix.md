# Plan de Prevención y Futuros Fixes (XamaSafe)

Este documento registra la resolución del crash en el detalle de inspecciones técnicas y establece las directrices y buenas prácticas operativas para prevenir fallos similares en la infraestructura de enrutamiento y vinculación de datos (Route Model Binding) de Laravel + Inertia.js.

---

## 🚀 Resoluciones de Enrutamiento y Crashes

### 1. Detalle de Inspección Técnica (`/tecnico/inspections/4`)
* **Problema**: Al ingresar a la URL de detalle [http://ep-back.test/tecnico/inspections/4](http://ep-back.test/tecnico/inspections/4), la pantalla sufría un crash crítico en la consola del navegador con el siguiente error:
  ```text
  Uncaught Error: Ziggy error: 'id' parameter is required for route 'tecnico.inspections.pdf.preview'.
  ```
* **Causa Raíz**:
  1. **Disparidad de Parámetros**: En `routes/web.php`, la ruta de detalle estaba definida con el comodín de parámetro genérico `{id}` (`tecnico/inspections/{id}`), pero el controlador `InspectionController@show` inyectaba y tipaba explícitamente el modelo `$inspection` (`DynamicInspection $inspection`).
  2. **Fallo de Route Model Binding**: Debido a la discrepancia de nombres (`{id}` vs `$inspection`), el motor de Laravel no podía enlazar automáticamente el modelo de base de datos. Inyectaba una instancia vacía de `DynamicInspection` con todos sus atributos en `null` (incluyendo su `id`).
  3. **Generación con Ziggy**: El frontend de Inertia recibía `inspection` con `id: null`. Al invocar la función de generación de rutas `route('tecnico.inspections.pdf.preview', { id: inspection.id })`, Ziggy recibía un valor indefinido para el parámetro obligatorio `id`, rompiendo la carga de la interfaz.

* **Solución de Raíz Aplicada**:
  * **Saneamiento del Enrutador**: Cambiamos el parámetro de la ruta en [routes/web.php](file:///d:/ep-back/routes/web.php) de `/{id}` a `/{inspection}`. Ahora la vinculación implícita de Laravel funciona perfectamente, hidratando el modelo con los datos de base de datos reales y el ID correcto (`4`).
  * **Seguridad Defensiva en Frontend**: En [Show.jsx](file:///d:/ep-back/resources/js/Pages/Tecnico/Inspections/Show.jsx), envolvimos el botón de descarga del PDF en una validación defensiva (`{inspection?.id && (...) }`), blindando la renderización de la página contra estados transitorios o de carga asíncrona.

---

## 🛠️ Correcciones Preventivas Adicionales

### 2. Saneamiento del Módulo de Hallazgos y Seguimiento
* **Diagnóstico**: Detectamos la misma discrepancia de nombres de parámetros en las rutas de **Hallazgos** (`FindingController@show` y `@addUpdate` inyectan `$finding` pero el enrutador usaba `{id}`).
* **Solución**: Renombramos preventivamente los parámetros a `{finding}` en las siguientes rutas en `routes/web.php`:
  * Ruta de detalle de hallazgo (Gestor): `gestor/hallazgos/{finding}`
  * Ruta de actualización/seguimiento de hallazgo (Gestor): `gestor/hallazgos/{finding}/update`
  * Ruta de detalle de hallazgo (Técnico): `tecnico/hallazgos/{finding}`

---

## 💡 Directrices Maestro para Futuros Desarrollos

> [!IMPORTANT]
> Para evitar regresiones o fallos inesperados de enrutamiento al expandir el sistema:

1. **Garantizar Coincidencia de Nombres de Parámetros**:
   * Al usar **Route Model Binding** en controladores de Laravel (ej. `public function show(Sede $sede)`), asegúrate de que el parámetro en `routes/web.php` use **exactamente** el mismo nombre del argumento en minúsculas (ej. `Route::get('/sedes/{sede}', ...)`). **Evita usar `{id}` de forma genérica** a menos que el controlador espere un entero primitivo `$id`.

2. **Programación Defensiva en Rutas Ziggy (Frontend)**:
   * Al invocar la función `route()` en componentes React para generar enlaces de descarga, botones de edición o redirecciones, verifica siempre que las propiedades de identificación existan (`inspection?.id`, `finding?.id`).
   * **Patrón Recomendado**:
     ```javascript
     {item?.id && (
         <a href={route('mi.ruta.pdf', { id: item.id })}>Ver PDF</a>
     )}
     ```

3. **Cargar Relaciones Eager con Seguridad**:
   * Si las vistas del frontend consumen datos anidados (como `inspection.checklist.title` o `finding.sede.empresa`), asegúrate de que el controlador los cargue explícitamente en el backend mediante `$model->load(...)` antes de inyectarlos al método `Inertia::render()`.
