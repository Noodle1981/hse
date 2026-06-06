# 📋 Guía de Testing Manual — XamaSafe v1.0 MVP

> Pre-producción checklist. Completar cada ítem y marcar ✅ o ❌.  
> En caso de ❌ documentar el error en la columna "Notas".

---

## ¿Qué es el testing manual?

El testing manual consiste en recorrer la aplicación como un usuario real,
probando cada funcionalidad disponible y verificando que se comporte como
se espera. El objetivo no es solo que "no rompa", sino también verificar:

- ✅ Que los datos se guardan correctamente en la base de datos
- ✅ Que los permisos por rol funcionen (un técnico no ve lo que no debe)
- ✅ Que los mensajes de error sean claros y útiles
- ✅ Que los PDFs se generen con el contenido correcto
- ✅ Que la UI sea responsive y no tenga desbordes visuales
- ✅ Que los flujos completos funcionen de punta a punta

---

## Configuración Previa

Antes de comenzar, asegurarse de tener:
- [ ] Laravel corriendo: `php artisan serve`
- [ ] Vite corriendo: `npm run dev`
- [ ] Base de datos con datos de prueba (`php artisan db:seed`)
- [ ] Al menos 1 usuario por cada rol (super_admin, Administrador, Técnico)
- [ ] Al menos 1 Consultora, 1 Empresa, 1 Sede cargada

---

## MÓDULO 1 — Autenticación y Roles

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 1.1 | Login con usuario **Administrador** → redirige a `/gestor/dashboard` | | |
| 1.2 | Login con usuario **Técnico** → redirige a `/tecnico/dashboard` | | |
| 1.3 | Login con **super_admin** → redirige a `/admin/dashboard` | | |
| 1.4 | Login con credenciales incorrectas → muestra mensaje de error | | |
| 1.5 | Acceder a `/gestor/dashboard` sin login → redirige a `/login` | | |
| 1.6 | Técnico intenta acceder a `/gestor/dashboard` → error 403 o redirect | | |
| 1.7 | Logout → redirige a login, ya no puede acceder sin login | | |

---

## MÓDULO 2 — Gestión de Empresas y Sedes (Gestor)

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 2.1 | Ver lista de empresas `/gestor/companies` → carga correctamente | | |
| 2.2 | Crear nueva empresa con todos los campos completos | | |
| 2.3 | Crear empresa con campos requeridos vacíos → muestra errores de validación | | |
| 2.4 | Editar empresa existente → datos se actualizan | | |
| 2.5 | Ver sedes de una empresa `/gestor/companies/{id}/locations` | | |
| 2.6 | Crear nueva sede para una empresa | | |
| 2.7 | La empresa creada aparece en el listado de asignaciones | | |

---

## MÓDULO 3 — Gestión de Usuarios (Gestor)

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 3.1 | Ver lista de usuarios `/gestor/users` → carga correctamente | | |
| 3.2 | Ver lista de técnicos `/gestor/tecnicos` | | |
| 3.3 | Crear nuevo usuario Técnico → aparece en la lista | | |
| 3.4 | El nuevo técnico puede hacer login | | |
| 3.5 | Gestor solo ve usuarios de su propia consultora | | |

---

## MÓDULO 4 — Checklists (Gestor)

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 4.1 | Ver lista de checklists `/gestor/checklists` | | |
| 4.2 | Crear checklist con pregunta tipo texto | | |
| 4.3 | Crear checklist con pregunta tipo radio (opciones Sí/No/N/A) | | |
| 4.4 | Crear checklist con pregunta tipo checkbox (múltiple selección) | | |
| 4.5 | Crear checklist con pregunta tipo select | | |
| 4.6 | Crear checklist con pregunta tipo textarea | | |
| 4.7 | Editar checklist existente → cambios se guardan | | |
| 4.8 | Reordenar preguntas → el orden se persiste | | |
| 4.9 | Exportar checklist a JSON → descarga correctamente | | |
| 4.10 | Emular checklist → el simulador funciona | | |
| 4.11 | Intentar guardar checklist sin título → error de validación | | |
| 4.12 | Intentar guardar checklist sin preguntas → error de validación | | |

---

## MÓDULO 5 — Asignaciones (Gestor)

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 5.1 | Ver lista de asignaciones `/gestor/assignments` | | |
| 5.2 | Asignar un checklist a un técnico en una sede | | |
| 5.3 | La asignación aparece en "Mis Tareas" del técnico (`/tecnico/checklists/my-assignments`) | | |

---

## MÓDULO 6 — Visitas Técnicas (FLUJO CENTRAL - MÁS CRÍTICO)

### 6A — Desde el lado del Gestor

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 6A.1 | Ver historial de visitas `/gestor/visits/history` | | |
| 6A.2 | Crear nueva visita y asignarla a un técnico | | |
| 6A.3 | La visita aparece en el panel del técnico | | |
| 6A.4 | Filtrar visitas por estado, sede, técnico y fecha | | |

### 6B — Desde el lado del Técnico (flujo completo)

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 6B.1 | Técnico ve sus visitas en `/tecnico/visits` | | |
| 6B.2 | Técnico ingresa a "Visita en Progreso" | | |
| 6B.3 | Dentro de la visita: completar un checklist asignado | | |
| 6B.4 | Dentro de la visita: auto-asignarse un nuevo checklist | | |
| 6B.5 | Dentro de la visita: registrar una capacitación | | |
| 6B.6 | Dentro de la visita: registrar un hallazgo con foto | | |
| 6B.7 | Dentro de la visita: registrar un incidente | | |
| 6B.8 | Técnico finaliza la visita con datos del responsable | | |
| 6B.9 | Técnico firma en el canvas digital | | |
| 6B.10 | Sistema genera Constancia de Visita en PDF | | |
| 6B.11 | PDF de constancia incluye: empresa, técnico, firma, actividades | | |
| 6B.12 | La visita cambia de estado a "Completada" al finalizar | | |

### 6C — Firma Remota

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 6C.1 | Técnico selecciona "Enviar link de firma por email" al finalizar | | |
| 6C.2 | Email llega con el link de firma | | |
| 6C.3 | El link `/constancia/firmar/{token}` carga la página pública de firma | | |
| 6C.4 | Responsable firma → visita queda marcada como firmada | | |

### 6D — Escenarios de Uso Real (Casos de Negocio)

> Estos escenarios simulan situaciones reales de campo. Cada uno debe verificarse
> tanto desde el **lado del Técnico** (quien ejecuta) como desde el **lado del Gestor**
> (quien crea la visita y luego ve el historial y el PDF resultante).

#### Escenario 1 — Visita con todo asignado por el Gestor

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 6D-1.1 | Gestor crea visita y le asigna al técnico: 1 checklist + 1 capacitación programada | | |
| 6D-1.2 | Técnico entra a la visita, completa el checklist asignado | | |
| 6D-1.3 | Técnico ejecuta la capacitación asignada y toma asistencia | | |
| 6D-1.4 | Técnico finaliza la visita con observaciones + firma | | |
| 6D-1.5 | PDF de constancia muestra checklist y capacitación completados | | |
| 6D-1.6 | Gestor ve la visita completada con todas las actividades en el historial | | |

#### Escenario 2 — Visita sin nada asignado (auto-gestión del Técnico)

| # | Prueba | Sub-variante | Resultado | Notas |
|---|---|---|---|---|
| 6D-2.1 | Gestor crea visita sin asignar checklist ni capacitación | | | |
| 6D-2.2 | Técnico entra y **se auto-asigna un checklist + una capacitación**, los completa | variante A | | |
| 6D-2.3 | Técnico entra y **se auto-asigna solo 1 checklist**, lo completa | variante B | | |
| 6D-2.4 | Técnico entra y **registra solo 1 capacitación** (sin checklist) | variante C | | |
| 6D-2.5 | En todas las variantes: técnico finaliza con observación obligatoria y firma | | | |
| 6D-2.6 | PDF refleja correctamente solo lo que se hizo en cada variante | | | |

#### Escenario 3 — Visita mixta con hallazgo e incidente

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 6D-3.1 | Gestor asigna 1 checklist al técnico | | |
| 6D-3.2 | Técnico completa el checklist asignado | | |
| 6D-3.3 | Técnico se auto-asigna 1 capacitación y la ejecuta | | |
| 6D-3.4 | Técnico reporta 1 hallazgo (con foto y criticidad) | | |
| 6D-3.5 | Técnico reporta 1 incidente | | |
| 6D-3.6 | Técnico finaliza visita con observaciones + firma | | |
| 6D-3.7 | PDF de constancia incluye: checklist, capacitación, hallazgo e incidente | | |
| 6D-3.8 | El hallazgo aparece en `/gestor/hallazgos` y en `/tecnico/mis-hallazgos` | | |
| 6D-3.9 | El incidente aparece en `/gestor/incidentes` | | |

#### Escenario 4 — Visita sin actividades (solo observación y firma)

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 6D-4.1 | Gestor crea visita sin asignar nada | | |
| 6D-4.2 | Técnico entra, no completa ningún checklist, capacitación, hallazgo ni incidente | | |
| 6D-4.3 | Técnico intenta finalizar sin observaciones → recibe error de validación | | |
| 6D-4.4 | Técnico escribe observación (mín. 10 caracteres) explicando la visita | | |
| 6D-4.5 | Técnico firma → visita se completa correctamente | | |
| 6D-4.6 | PDF de constancia se genera sin secciones vacías o errores visuales | | |
| 6D-4.7 | Gestor ve la visita como "completada" aunque no haya actividades registradas | | |



## MÓDULO 7 — Hallazgos

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 7.1 | Gestor ve todos los hallazgos `/gestor/hallazgos` | | |
| 7.2 | Técnico ve solo sus hallazgos `/tecnico/mis-hallazgos` | | |
| 7.3 | KPIs del técnico están visibles (totales, por criticidad) | | |
| 7.4 | NO se puede crear hallazgo fuera de una visita | | |
| 7.5 | Hallazgo creado muestra: criticidad, fotos, estado | | |
| 7.6 | Cambiar estado del hallazgo: Pendiente → En Proceso → Resuelto | | |
| 7.7 | PDF de hallazgo individual se genera correctamente | | |
| 7.8 | PDF incluye fotos adjuntas | | |

---

## MÓDULO 8 — Capacitaciones

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 8.1 | Ver lista de capacitaciones (gestor y técnico) | | |
| 8.2 | Crear capacitación con tema, duración y sede | | |
| 8.3 | Tomar asistencia: asistentes firman | | |
| 8.4 | La capacitación queda vinculada a la visita del día | | |
| 8.5 | PDF de capacitación se genera con branding de consultora | | |
| 8.6 | PDF incluye lista de asistentes y firmas | | |

---

## MÓDULO 9 — Contrato Anual (Gestor)

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 9.1 | Acceder a `/gestor/contrato-anual` → carga correctamente | | |
| 9.2 | Dashboard muestra tabla de planificación anual | | |
| 9.3 | Marcar/desmarcar un servicio en un mes → cambio se guarda | | |
| 9.4 | Cambiar la frecuencia de un servicio → se actualiza | | |
| 9.5 | Exportar PDF del contrato anual → descarga correctamente | | |
| 9.6 | PDF del contrato incluye todos los servicios y meses | | |

---

## MÓDULO 10 — Informe Maestro

### 10A — Desde el Gestor

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 10A.1 | Gestor accede a `/gestor/master-reports` | | |
| 10A.2 | Crear nuevo informe: seleccionar técnico, mes y sede | | |
| 10A.3 | El informe aparece en el panel del técnico | | |

### 10B — Desde el Técnico

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 10B.1 | Técnico ve informes asignados en `/tecnico/master-reports` | | |
| 10B.2 | Técnico abre el wizard de redacción | | |
| 10B.3 | El wizard pre-carga automáticamente: visitas, checklists, capacitaciones del mes | | |
| 10B.4 | Técnico completa el análisis profesional | | |
| 10B.5 | Técnico guarda el informe | | |
| 10B.6 | PDF del Informe Maestro se genera correctamente | | |
| 10B.7 | PDF incluye: portada, resumen, visitas, checklists, capacitaciones, hallazgos, incidentes | | |
| 10B.8 | PDF aplica branding (logo + colores) de la consultora | | |

---

## MÓDULO 11 — Branding de PDFs

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 11.1 | Super admin puede configurar branding de consultora en `/admin/consultants` | | |
| 11.2 | Subir logo de consultora → se guarda y se visualiza | | |
| 11.3 | Configurar colores primario, secundario y acento | | |
| 11.4 | Los PDFs generados usan el logo y colores configurados | | |
| 11.5 | Si una consultora no tiene branding, los PDFs usan valores por defecto | | |

---

## MÓDULO 12 — Seguridad y Aislamiento de Datos

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 12.1 | El Gestor A NO ve empresas del Gestor B (consultoras distintas) | | |
| 12.2 | El Técnico A NO ve visitas del Técnico B | | |
| 12.3 | El Técnico A NO ve hallazgos del Técnico B | | |
| 12.4 | Acceder a PDF de otra consultora directamente por URL → error o redirect | | |
| 12.5 | No hay endpoints sin autenticación (verificar rutas API) | | |

---

## MÓDULO 13 — UI/UX y Responsive

| # | Prueba | Resultado | Notas |
|---|---|---|---|
| 13.1 | Probar en pantalla de escritorio (1920px) — sin desborde de tablas | | |
| 13.2 | Probar en tablet (768px) — layout se adapta | | |
| 13.3 | Probar en móvil (375px) — menú y botones accesibles | | |
| 13.4 | Páginas con tablas tienen scroll horizontal en móvil | | |
| 13.5 | Los modales se abren y cierran correctamente | | |
| 13.6 | Los mensajes de éxito/error son visibles | | |

---

## Consejos para el Tester Manual

### 🧠 Mentalidad correcta
- **Intentá romper la app**: no pruebes el camino feliz solamente.
  Probá el camino equivocado: datos incorrectos, campos vacíos, URLs directas.
- **Probá los límites**: ¿Qué pasa si el nombre tiene 500 caracteres?
  ¿Si subís una imagen de 50MB?
- **Probá la concurrencia**: ¿Qué pasa si abrís dos tabs y hacés la misma acción?

### 📝 Cómo documentar errores
Cuando encontrás un bug, anotá:
1. **Pasos para reproducirlo** (exactos, paso a paso)
2. **Qué esperabas** que pasara
3. **Qué pasó realmente**
4. **Screenshot** del error si es posible
5. **URL** en la que estabas cuando ocurrió

### 🔁 Orden recomendado
1. Primero: Autenticación y roles (base de todo)
2. Segundo: El flujo de Visita completo (es el núcleo del negocio)
3. Tercero: PDFs (alto impacto en la percepción del cliente)
4. Último: UI responsive (menos crítico pero visible)
