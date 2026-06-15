# 👥 Manual de Usuario: Permisos y Responsabilidades (v2.2)

Este manual explica qué puede hacer cada usuario en **XamaSafe** según su **Rol**, para asegurar la integridad de la operación táctica y administrativa.

---

## 🎯 Visión General
El sistema utiliza un control de acceso basado en roles para que cada persona vea solo lo que es relevante para su trabajo, garantizando seguridad, confidencialidad e inmutabilidad de los registros.

---

## 👤 Perfiles del Sistema

### 1. Súper Administrador (Global)
Posee privilegios absolutos de infraestructura y administración global.
- **Gestión de Consultoras:** Altas, bajas (eliminación física) y edición de consultoras.
- **Límites de Licenciamiento:** Modifica los cupos máximos de técnicos, gestores y empresas por consultora.
- **Personalización de Branding:** Asigna logotipos y colores corporativos por consultora.
- **Gestión Global de Personal (Staff):** CRUD absoluto de cuentas de Súper Administradores y Vendedores.
- **Gestión de Personal de Consultoras:** Administra gestores y técnicos de cualquier consultora en caliente.

### 2. Vendedor (Soporte Comercial Global)
Rol comercial con accesos globales orientados a ventas y soporte.
- **Gestión Comercial:** Crea y edita consultoras y límites corporativos.
- **Suscripciones:** Modifica el estado de suscripción de las consultoras (`activa`, `morosa` o `baja`).
- **Restricción Estricta:** Bloqueo absoluto de borrado de consultoras y administración de personal global/interno. Recibe un error `403` en backend y los elementos se ocultan automáticamente en la UI.

### 3. Gestor (Administrador de Consultora)
Es el usuario de nivel superior encargado de la estrategia y configuración central de una consultora.
- **Alcance Global:** Puede ver a todos los técnicos, empresas y sedes de su consultora.
- **Configuración Maestro:** Crea checklists, gestiona empresas, sedes, empleados y usuarios técnicos.
- **Supervisión y Auditoría:** Aprueba firmas pendientes de técnicos, audita, observa y aprueba informes maestros del mes.

### 4. Técnico de Campo
Es el usuario operativo, dedicado a la ejecución táctica en la planta del cliente.
- **Alcance Restringido:** Solo visualiza las **Sedes** que le fueron asignadas por el Gestor.
- **Operación Directa:** Registra visitas con geolocalización (check-in/check-out), completa checklists, dicta capacitaciones y toma firmas electrónicas.
- **Gestión de Hallazgos:** Levanta y sigue los desvíos técnicos hasta su resolución.

### 5. Cliente Corporativo (Lector)
Usuario externo con acceso de consulta a sus datos históricos.
- **Auditoría Externa:** Consulta constancias de visitas, capacitaciones realizadas de su personal, descarga PDFs y reporta resolución de hallazgos.

---

## 📊 Matriz de Acción Rápida

| Funcionalidad | Súper Admin | Vendedor | Gestor | Técnico | Cliente |
|---|---|---|---|---|---|
| Crear / Editar Consultoras | ✅ | ✅ | ❌ | ❌ | ❌ |
| Eliminar Consultoras | ✅ | ❌ | ❌ | ❌ | ❌ |
| Configurar Staff Global | ✅ | ❌ | ❌ | ❌ | ❌ |
| Crear Empresa / Sede | ✅ | ❌ | ✅ | ❌ | ❌ |
| Crear Usuario Técnico | ✅ | ❌ | ✅ | ❌ | ❌ |
| Aprobar Firmas de Perfil | ❌ | ❌ | ✅ | ❌ | ❌ |
| Iniciar Visita Técnica (GPS) | ❌ | ❌ | ✅ (Modo Tec) | ✅ | ❌ |
| Completar Inspección | ❌ | ❌ | ✅ (Modo Tec) | ✅ | ❌ |
| Aprobar Informe Maestro | ❌ | ❌ | ✅ | ❌ | ❌ |
| Descargar Certificados / PDF | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 🗃️ Registro de Auditoría Inmutable (Audit Log)
Toda modificación de accesos, suscripción o límites de licencias corporativas realizada por un **Súper Administrador** o **Vendedor** se graba en caliente en la tabla `audit_logs`:
- **Inmutabilidad:** Los registros de auditoría no pueden ser borrados ni modificados (cualquier intento a nivel backend retorna excepción).
- **Trazabilidad:** La UI de edición de consultoras integra una línea de tiempo táctica de auditoría, detallando quién hizo el cambio y el diff visual (ej. *Licencias Técnicos: 10 ➔ 15*).

---

## 🔄 El "Modo Alternancia"
Una característica clave para los **Gestores** es el **Modo Técnico**. Permite a un Gestor "convertirse" temporalmente en un Técnico para realizar una inspección de campo extraordinaria sin necesidad de una cuenta secundaria.
1. Busca el botón naranja **"Activar Modo Técnico"** en tu dashboard.
2. La interfaz cambiará su visual y permisos para verse como la de un técnico operativo.
3. El sistema registrará que las acciones fueron realizadas por un Gestor en modo alternancia para auditoría futura.

---

## 🎨 Consistencia "Terminal Táctica"
Independientemente del rol, la interfaz mantiene la estética **High-End Dark UI**, con paneles de cristal y tipografía técnica que asegura que la información importante siempre destaque.

---

## 💡 Tips de Seguridad
- **Cierre de Sesión**: Si compartes equipo con otros técnicos, asegúrate de cerrar sesión al finalizar para evitar registros erróneos.
- **Asignaciones**: Si como técnico no ves una sede, solicita al Gestor que te asigne como responsable en el panel de **Gestión de Sedes**.

---

## 📝 Resolución de Problemas
- **¿Por qué no puedo borrar una consultora?** Solo los Súper Administradores tienen acceso a la baja física de consultoras.
- **¿Por qué no puedo ver la gestión de personal de consultora si soy vendedor?** Está fuera del alcance del rol de soporte comercial.
- **No puedo ver el Informe Maestro del mes**: Asegúrate de que el Gestor te haya asignado la meta en su planificación mensual de sede.

---
*Manual actualizado para XamaSafe v2.2 | Terminal Táctica Design*
