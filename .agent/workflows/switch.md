---
description: Documentación de arquitectura y diseño para la futura implementación del Switch de Roles (Gestor ↔ Técnico). Documentación de Implementación Futura: Switch de Roles (Gestor ↔ Técnico)
---

ste documento detalla la planificación arquitectónica, los requisitos del backend (ya soportados) y las tareas de frontend (diferidas para futuras versiones) necesarias para implementar un interruptor (switch) que permita a un usuario con múltiples capacidades alternar entre su perfil de **Gestor** y **Técnico** sin necesidad de cerrar sesión.

#### 1. 🎯 Contexto y Filosofía SRE
Bajo el enfoque de **System Reliability Engineering (SRE)**, la implementación de la interfaz visual del switch se difirió deliberadamente ("diferir la complejidad de la interfaz") para priorizar el lanzamiento del núcleo (core) del sistema [1, 2]. 
A nivel de base de datos y backend, el sistema es 100% capaz de soportar esta funcionalidad gracias a su arquitectura de parsing universal de roles.

--------------------------------------------------------------------------------

#### 2. ⚙️ Preparación del Backend (Estado Actual: Listo)

La arquitectura de Laravel ya está blindada y preparada para soportar usuarios con perfiles duales.

*   **Modelo de Datos (`users.rol`):** El campo `rol` en la tabla `users` es de tipo `varchar` [3], lo que permite almacenar múltiples roles concatenados.
*   **Parsing Universal de Roles:** El modelo `User` ya cuenta con el método `User::parseRoles(string|array $roles)` [1]. Este motor es recursivo y normaliza cadenas complejas como `'gestor,tecnico'` o `['gestor', 'tecnico']`, resolviéndolas bidireccionalmente [1].
*   **Autorización Centralizada:** El middleware `CheckRole` delega toda la validación directamente a `User::hasRole()` [1, 4]. Esto significa que si un usuario tiene el rol `'gestor,tecnico'`, Laravel le permitirá el acceso tanto a las rutas de administración como a las rutas de campo de forma nativa.

--------------------------------------------------------------------------------

#### 3. 🖥️ Requisitos para la Futura Integración (Frontend / UI)

La integración futura radica exclusivamente en la capa de presentación (React + Inertia.js) y la gestión de la sesión activa.

##### 3.1. Gestión del "Rol Activo" (Session State)
Dado que el usuario tiene permisos globales para ambas áreas, el sistema necesita saber qué tablero mostrarle al iniciar sesión y cómo mantener el contexto:
*   **Default Landing:** Al hacer login, el sistema debe evaluar: si tiene ambos roles, ¿cuál es el predeterminado? (Generalmente, se lo enviaría a `resources/js/Pages/Gestor/Dashboard`) [5].
*   **Variable Global (Inertia Shared Props):** El middleware `HandleInertiaRequests` deberá compartir una variable `active_role` para que la barra de navegación (HUD) sepa qué menú renderizar.

##### 3.2. Componente React: El "Role Switch" (Tactical Pill)
Se deberá construir un componente modular (`RoleSwitcher.jsx`) ubicado en la barra superior (TopBar/Header):
*   **Comportamiento Visual:** Debe diseñarse como un botón tipo "Tactical Pill" [6].
*   **Transición Sutil:** Al accionarlo, no debe recargar la página abruptamente, sino usar `Inertia.visit()` para saltar de las rutas `/tecnico/*` [7] a `/gestor/*` [5].
*   **Paleta de Colores Dinámica (Reglas Visuales):** 
    *   Al estar en modo **Técnico**, el switch y los acentos del HUD deben iluminarse con el **Naranja Técnico** (Inspecciones/Campo) [8].
    *   Al cambiar al modo **Gestor**, la paleta de acentos del HUD podría transicionar hacia el color corporativo general de la consultora (ej. azul/índigo) para darle retroalimentación visual inmediata al usuario de que está en el perfil administrativo [8, 9].

--------------------------------------------------------------------------------

#### 4. 🛡️ Consideraciones de Auditoría e Integridad (Security Hardening)

Dado que el intercambio de roles permite a una misma persona ejecutar inspecciones en terreno y luego aprobar o auditar sus propios informes en oficina, la plataforma debe mantener una trazabilidad absoluta:
*   **Activity Log Contextual:** Cada vez que el usuario realice una acción crítica (ej. autorizar una firma temporal o cerrar un Reporte Maestro), la bitácora de actividad debe registrar no solo el `causer_id` [2], sino bajo qué contexto/rol estaba operando en ese momento.
*   **Trazabilidad de Firmas:** Si el usuario firma un checklist como Técnico y luego firma el Reporte Maestro como Gestor, el motor de DomPDF [10] y la base de datos deben procesarlo con total normalidad, pero manteniendo los registros inmutables de fecha, hora y ubicación GPS (Proof of Presence) de la instancia técnica [11].

--------------------------------------------------------------------------------

#### 5. 📋 Prompt Sugerido para la Futura Implementación
*(Guarda este prompt para cuando decidas construir el componente con tu IA)*

> **Contexto:** Estamos en la rama `feature-inertia-react` del proyecto XamaSafe. El backend ya soporta usuarios con múltiples roles (ej. `'gestor,tecnico'`).
> 
> **Objetivo:** Implementar un componente `RoleSwitcher.jsx` en la navegación (HUD) para usuarios duales, y actualizar el middleware para manejar el redireccionamiento.
> 
> **Tareas Frontend:**
> 1. Crea el componente `RoleSwitcher.jsx` estilo *Tactical Pill*. Debe usar `Inertia.visit` para redirigir al usuario entre `/gestor/dashboard` y `/tecnico/dashboard`.
> 2. Aplica un renderizado condicional: el switch solo debe ser visible si `auth.user.roles` incluye tanto 'gestor' como 'tecnico'.
> 3. Utiliza la regla de color estricta: si está en modo Técnico, el indicador del switch usa `bg-ep-primary` (Naranja Técnico). Si está en modo Gestor, usa `bg-indigo-500`.
> 
> **Tareas Backend:**
> 1. Actualiza el `AuthenticatedSessionController` o equivalente para que, al iniciar sesión un usuario dual, se le asigne en la sesión un `active_role` por defecto y se le dirija a la ruta correspondiente.

--------------------------------------------------------------------------------
*Documento preparado para integración con NotebookLM*
*Módulo: Funcionalidades Diferidas (Backlog)*