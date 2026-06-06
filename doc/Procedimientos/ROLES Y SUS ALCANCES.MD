# 👥 Manual de Usuario: Permisos y Responsabilidades (v1.5)

Este manual explica qué puede hacer cada usuario en **XamaSafe** según su **Rol**, para asegurar la integridad de la operación táctica.

---

## 🎯 Visión General
El sistema utiliza un control de acceso basado en roles para que cada persona vea solo lo que es relevante para su trabajo, garantizando seguridad y confidencialidad de la información.

---

## 👤 Perfiles del Sistema

### 1. Gestor (Administrador de Consultora)
Es el usuario de nivel superior encargado de la estrategia y configuración central.
- **Alcance Global**: Puede ver a todos los técnicos, empresas y sedes de su consultora.
- **Configuración Maestro**: Crea checklists, gestiona empresas y usuarios.
- **Supervisión**: Aprueba informes maestros y monitorea el cumplimiento anual.
- **Control de Baja**: Solo el Gestor puede eliminar o corregir datos maestros del sistema.

### 2. Técnico de Campo
Es el usuario operativo, dedicado a la ejecución táctica en la planta del cliente.
- **Alcance Restringido**: Solo visualiza las **Sedes** que le fueron asignadas por el Gestor.
- **Operación Directa**: Registra visitas, completa checklists y toma asistencias.
- **Gestión de Hallazgos**: Levanta y sigue los desvíos técnicos hasta su resolución.
- **Reportes Diarios**: Genera constancias de visitas con firmas en el momento.

---

## 📊 Matriz de Acción Rápida

| Funcionalidad | Gestor | Técnico |
|---|---|---|
| Crear Empresa / Sede | ✅ | ❌ |
| Crear Usuario Técnico | ✅ | ❌ |
| Ver todas las sedes | ✅ | ❌ (Solo asignadas) |
| Iniciar Visita Técnica | ✅ | ✅ |
| Crear Auditoría (Checklist) | ✅ | ❌ |
| Completar Inspección | ✅ | ✅ |
| Registrar Hallazgo | ✅ | ✅ |
| Descargar Certificados | ✅ | ✅ |

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
- **¿Por qué no puedo borrar un usuario?** Solo los Gestores tienen acceso a la gestión de bajas de técnicos.
- **No puedo ver el Informe Maestro del mes**: Asegúrate de que el Gestor te haya solicitado formalmente la redacción del informe.

---
*Manual actualizado para XamaSafe v1.5 | Terminal Táctica Design*
