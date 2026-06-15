# 🏢 Manual de Usuario: Gestión de Entidades (v2.2)

Este manual documenta la administración de **Empresas**, **Sedes** y **Personal** en **XamaSafe**, la base estructural sobre la cual se construye toda la operación técnica y el control de licenciamiento.

---

## 🎯 Visión General
El sistema utiliza una arquitectura de tres niveles para garantizar la trazabilidad total:
1.  **Empresa Cliente**: Nivel superior (CUIT, Razón Social, Logo, Rubro).
2.  **Sede / Planta**: Lugares físicos con geolocalización GPS, radio de tolerancia y parámetros de control de jornada.
3.  **Empleados / Asistentes**: Personal de planta que asiste a las capacitaciones ( Nombre, Puesto y DNI obligatorio).

---

## 🚀 Administración Paso a Paso

### 1. Registro de Nueva Empresa (Solo Gestores)
1. Navega a **Empresas**.
2. Pulsa en **"Nueva Empresa"**.
3. Completa los datos: Nombre, CUIT (único), Rubro, Dirección, y Teléfono.
4. **Validación de Licencias (Límite Máximo):** El sistema audita que la consultora no supere el límite máximo de empresas (`max_empresas`) parametrizado comercialmente por el Súper Administrador o Vendedor. Si se excede, el sistema bloqueará el alta.

### 2. Gestión y Configuración de Sedes
Las sedes definen el comportamiento del GPS y control de jornada:
1. Entra al detalle de la empresa.
2. Pulsa **"Agregar Sede/Planta"**.
3. Completa la configuración:
   - **Geolocalización:** Define la **Latitud** y **Longitud** exactas de la planta.
   - **Radio de Tolerancia:** Distancia máxima en metros (ej. 500m) autorizada para el check-in del técnico.
   - **Control Horario Standard:** Configura los días operativos y la hora de apertura/cierre.
   - **Roster Minero 24/7:** Activa el switch para operaciones mineras continuas para evitar alertas y autorizar check-in cualquier día/hora.
4. Asigna los **Técnicos responsables** de esta sede. Solo estos técnicos verán los datos de esta planta en su dashboard, respetando el multi-tenancy.

### 3. Registro de Empleados / Participantes
1. Permite dar de alta a los operarios de la empresa.
2. Cada empleado cuenta obligatoriamente con su DNI para la validación de asistencia y generación de certificados de charlas.

---

## 👤 Roles Relacionados

| Rol | Acción |
|---|---|
| **Súper Admin / Vendedor** | Configurar límites máximos de empresas (`max_empresas`) para cada consultora. |
| **Gestor** | Alta de empresas y sedes, asignación de técnicos e ingresos de personal. |
| **Técnico** | Acceso a las sedes asignadas e inspección del personal presente. |

---

## 🎨 Experiencia "Terminal Táctica"
- **Search & Filter (Glassmorphism):** Los buscadores de empresas y sedes están integrados en paneles HUD con micro-animaciones de carga.
- **Identificadores Únicos:** Bloqueo anti-duplicados por CUIT y DNI a nivel base de datos para preservar la fidelidad de los registros.

---

## 💡 Tips de Gestión
- **Rubros Específicos:** Configura el rubro a nivel de sede si la empresa cuenta con plantas industriales de diferentes naturalezas operacionales.
- **Branding:** Sube el logo corporativo del cliente para personalizar de forma dinámica los informes maestros y constancias en PDF.

---

## 📝 Resolución de Problemas
- **El técnico no ve la sede asignada:** Verifica que el gestor haya vinculado al técnico en la configuración de la Sede correspondiente.
- **Se excedió el límite de empresas:** El administrador de la consultora debe solicitar una ampliación de licencias al Súper Administrador o Vendedor del sistema.

---
*Manual actualizado para XamaSafe v2.2 | Terminal Táctica Design*
