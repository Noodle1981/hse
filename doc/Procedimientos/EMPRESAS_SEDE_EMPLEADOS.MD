# 🏢 Manual de Usuario: Gestión de Entidades (v1.5)

Este manual documenta la administración de **Empresas**, **Sedes** y **Empleados** en **XamaSafe**, la base estructural sobre la cual se construye toda la operación técnica.

---

## 🎯 Visión General
El sistema utiliza una arquitectura de tres niveles para garantizar la trazabilidad total:
1.  **Empresa Cliente**: Nivel superior (CUIT, Razón Social, Logo).
2.  **Sede / Planta**: Lugares físicos de trabajo asignados a la empresa.
3.  **Responsables de Sede**: Personas de contacto encargadas de autorizar las visitas.

---

## 🚀 Administración Paso a Paso

### 1. Registro de Nueva Empresa (Solo Gestores)
Para dar de alta a un nuevo cliente:
1. Navega a **Administración > Empresas**.
2. Pulsa el botón **"Nueva Empresa"** (estilo *Tactical Pill* con color primario).
3. Completa los datos: **Nombre**, **CUIT** (validación formato), **Dirección**, y **Datos de Contacto Central**.
4. Pulsa **"Guardar"**. Ahora la empresa podrá recibir la asignación de sedes.

### 2. Gestión de Sedes
Las empresas pueden tener múltiples ubicaciones físicas:
1. Entra al detalle de la empresa registrada.
2. Pulsa el botón **"Agregar Sede/Planta"**.
3. Define el nombre descriptivo (ej: "Planta Industrial Zárate").
4. Ingresa el **Responsable de la Sede** (Nombre y Cargo), quien recibirá las notificaciones y firmará las visitas.
5. Asigna los **Técnicos responsables** de esta sede. Solo estos técnicos verán los datos de esta planta en su dashboard.

---

## 👤 Roles Relacionados

| Rol | Acción |
|---|---|
| **Gestor (Administrador)** | Alta y baja de empresas, creación de sedes y asignación de técnicos responsables. |
| **Técnico de Campo** | Visualización de datos de sedes asignadas y consulta de responsables para firmas. |

---

## 🎨 Experiencia "Terminal Táctica"
- **Search & Filter (Glassmorphism)**: Los buscadores de empresas están integrados en paneles de cristal flotante para una navegación rápida.
- **Dynamic Entities**: Al escribir el nombre de una empresa en selectores, el sistema sugiere coincidencias en tiempo real con micro-animaciones de carga.

---

## 💡 Tips de Gestión
- **CUIT Único**: El sistema impedirá duplicar una empresa con un mismo CUIT, asegurando la integridad de tus registros fiscales.
- **Logo del Cliente**: Sube el logo de la empresa para que aparezca automáticamente en la portada de los informes PDF que les entregues.

---

## 📝 Resolución de Problemas
- **¿Por qué el técnico no ve la empresa?** Revisa que el técnico esté correctamente asignado a la **Sede** de esa empresa en el panel de administración.
- **El nombre del responsable de la sede cambió**: Edita la sede correspondiente para actualizar el nombre. Las visitas futuras tomarán el nuevo dato, las pasadas conservarán el histórico por trazabilidad legal.

---
*Manual actualizado para XamaSafe v1.5 | Terminal Táctica Design*
