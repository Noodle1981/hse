# 🎯 Caso de Uso Completo - EP-Back
## Testing End-to-End del Sistema de Checklists

> **Objetivo**: Probar el flujo completo desde la configuración inicial hasta la finalización de un checklist, cubriendo todas las dependencias y módulos del sistema.

---

## 📋 Resumen del Caso de Uso

**Escenario**: Una empresa de construcción "Constructora ABC" necesita implementar un checklist de seguridad para inspecciones diarias en obra.

**Actores**:
- **Admin/Gestor** (admin@ep.com) - Configura el sistema y asigna tareas
- **Operario/Técnico** (operario@ep.com) - Completa checklists en campo

**Flujo completo**:
1. Configuración de empresa y sede
2. Creación de categorías y subcategorías
3. Creación de checklist con secciones
4. Asignación de checklist al operario
5. Completado del checklist por el operario
6. Revisión de resultados por el admin

---

## 🔧 FASE 1: Configuración Inicial (Admin)

### 1.1 Crear Empresa [x]

**Login**: `gestor@ep.com` / `password`

**Navegación**: Menú → Empresas → Nueva Empresa

**Datos a ingresar**:
```
Nombre: Constructora ABC
CUIT: 30-12345678-9
Razón Social: Constructora ABC S.A.
```

**Validar**:
- [x] Empresa creada correctamente
- [x] Aparece en listado de empresas
- [x] Se puede editar
- [x] Validación de CUIT funciona
- [x] Campos obligatorios marcados con *

**Nota**: El formulario actual es minimalista con solo 3 campos. Esto es correcto para el MVP. Campos adicionales (dirección, teléfono, email) pueden agregarse en futuras versiones si es necesario.


---

### 1.2 Crear Sede [x]

**Navegación**: Menú → Sedes → Nueva Sede

**Datos a ingresar**:
```
Nombre de la Sede: Obra Central - Torre Norte
Tipo de Sede: Obra
Dirección: Calle Obra 456
Localidad: Buenos Aires
Teléfono: 011-9876-5432
Correo: contacto@torrenorte.com
```

**Validar**:
- [x] Sede creada y asociada a la empresa
- [x] Aparece en listado de sedes
- [x] Filtro por empresa funciona
- [x] Autocompletado aprenderá 'Obra' y 'Buenos Aires' para la próxima vez

**Nota**: El campo "Responsable" se maneja asignando usuarios a la sede en el paso 1.3, no en el formulario de creación.


---

### 1.3 Asignar Operario a la Sede [x]

**Navegación**: Menú → Usuarios → Gestión de Usuarios

**Acción**: Editar usuario "operario@ep.com"

**Asignar**:
```
Sede: Obra Central - Torre Norte
```

**Validar**:
- [x] Operario asignado a la sede
- [x] Relación visible en perfil del usuario

---

## 📚 FASE 2: Creación de Categorías (Admin) [x]

### 2.1 Crear Categoría Principal [x]

**Navegación**: Menú → Categorías → Nueva Categoría

**Datos a ingresar**:
```
Nombre: Seguridad en Obra
Descripción: Categoría para checklists de seguridad en construcción
Tipo: Principal
Estado: Activo
```

**Validar**:
- [x] Categoría creada
- [x] Aparece en listado

---

### 2.2 Crear Subcategorías [x]

**Navegación**: Menú → Categorías → Nueva Subcategoría

**Subcategoría 1**:
```
Categoría Padre: Seguridad en Obra
Nombre: Equipos de Protección Personal (EPP)
Descripción: Verificación de uso correcto de EPP
Estado: Activo
```

**Subcategoría 2**:
```
Categoría Padre: Seguridad en Obra
Nombre: Condiciones del Sitio
Descripción: Estado general del área de trabajo
Estado: Activo
```

**Subcategoría 3**:
```
Categoría Padre: Seguridad en Obra
Nombre: Herramientas y Equipos
Descripción: Verificación de herramientas en buen estado
Estado: Activo
```

**Validar**:
- [x] 3 subcategorías creadas
- [x] Relación jerárquica correcta
- [x] Aparecen bajo la categoría padre

---

## 📝 FASE 3: Creación de Checklist (Admin) [x]

### 3.1 Crear Checklist Base [x]

**Navegación**: Menú → Checklists → Nuevo Checklist

**Datos básicos**:
```
Nombre: Inspección Diaria de Seguridad - Obra
Categoría: Seguridad en Obra
Descripción: Checklist para inspección diaria antes de iniciar trabajos
Tipo: Con Secciones
Estado: Activo
```

**Validar**:
- [x] Checklist creado
- [x] Aparece en listado de checklists

---

### 3.2 Agregar Sección 1: EPP [x]

**Navegación**: Editar Checklist → Agregar Sección

**Datos de la sección**:
```
Nombre: Verificación de EPP
Subcategoría: Equipos de Protección Personal (EPP)
Orden: 1
```

**Preguntas/Items de la sección**:

1. **Item 1**:
   ```
   Pregunta: ¿El personal usa casco de seguridad?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: Sí
   ```

2. **Item 2**:
   ```
   Pregunta: ¿El personal usa chaleco reflectivo?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: No
   ```

3. **Item 3**:
   ```
   Pregunta: ¿El personal usa calzado de seguridad?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: No
   ```

4. **Item 4**:
   ```
   Pregunta: ¿Se utilizan guantes apropiados para la tarea?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: Sí
   ```

---

### 3.3 Agregar Sección 2: Condiciones del Sitio [x]

**Datos de la sección**:
```
Nombre: Estado del Área de Trabajo
Subcategoría: Condiciones del Sitio
Orden: 2
```

**Preguntas/Items de la sección**:

1. **Item 1**:
   ```
   Pregunta: ¿El área de trabajo está libre de obstáculos?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: Sí
   ```

2. **Item 2**:
   ```
   Pregunta: ¿Las vías de circulación están señalizadas?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: Sí
   ```

3. **Item 3**:
   ```
   Pregunta: ¿Hay buena iluminación en el área?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: No
   Permite foto: No
   ```

4. **Item 4**:
   ```
   Pregunta: ¿Las salidas de emergencia están despejadas?
   Tipo: Sí/No
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: Sí
   ```

5. **Item 5**:
   ```
   Pregunta: Calificación general del orden y limpieza (1-5)
   Tipo: Numérico (1-5)
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: No
   ```

---

### 3.4 Agregar Sección 3: Herramientas [x]

**Datos de la sección**:
```
Nombre: Verificación de Herramientas
Subcategoría: Herramientas y Equipos
Orden: 3
```

**Preguntas/Items de la sección**:

1. **Item 1**:
   ```
   Pregunta: ¿Las herramientas eléctricas tienen cables en buen estado?
   Tipo: Sí/No/N/A
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: Sí
   ```

2. **Item 2**:
   ```
   Pregunta: ¿Los andamios están correctamente armados?
   Tipo: Sí/No/N/A
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: Sí
   ```

3. **Item 3**:
   ```
   Pregunta: ¿Las escaleras están en buen estado?
   Tipo: Sí/No/N/A
   Obligatorio: Sí
   Permite observaciones: Sí
   Permite foto: No
   ```

4. **Item 4**:
   ```
   Pregunta: Observaciones generales de herramientas
   Tipo: Texto libre
   Obligatorio: No
   Permite observaciones: N/A
   Permite foto: Sí
   ```

**Validar**:
- [x] Checklist tiene 3 secciones
- [x] Total de 13 items/preguntas
- [x] Orden de secciones correcto
- [x] Tipos de respuesta variados

---

## 👥 FASE 4: Asignación de Checklist (Admin) [PENDIENTE]

### 4.1 Crear Asignación

**Navegación**: Menú → Checklists → Asignaciones → Nueva Asignación

**Datos de la asignación**:
```
Checklist: Inspección Diaria de Seguridad - Obra
Técnico/Operario: Juan Operario (operario@ep.com)
Empresa: Constructora ABC
Sede: Obra Central - Torre Norte
Fecha de inicio: [Hoy]
Fecha de vencimiento: [Hoy + 7 días]
Prioridad: Alta
Instrucciones: Realizar inspección diaria antes de las 8:00 AM
Recurrencia: Diaria (Lunes a Viernes)
Estado: Activo
```

**Validar**:
- ✅ Asignación creada correctamente
- ✅ Aparece en listado de asignaciones
- ✅ Estado: Pendiente
- ✅ Notificación enviada al operario (si aplica)

---

## 📱 FASE 5: Completado de Checklist (Operario)

### 5.1 Login como Operario

**Cerrar sesión de Admin**

**Login**: `operario@ep.com` / `password`

**Validar**:
- ✅ Dashboard de operario visible
- ✅ Checklist asignado aparece en "Mis Tareas Pendientes"
- ✅ Indicador de prioridad "Alta" visible

---

### 5.2 Iniciar Checklist

**Navegación**: Mis Tareas → Ver Checklist "Inspección Diaria de Seguridad"

**Acción**: Hacer clic en "Iniciar Inspección"

**Validar**:
- ✅ Checklist se abre en modo edición
- ✅ Todas las secciones visibles
- ✅ Campos obligatorios marcados

---

### 5.3 Completar Sección 1: EPP

**Respuestas**:
```
1. ¿El personal usa casco de seguridad?
   Respuesta: Sí
   Observación: Todo el personal cumple
   Foto: [Opcional - subir foto]

2. ¿El personal usa chaleco reflectivo?
   Respuesta: No
   Observación: Falta chaleco en 2 trabajadores nuevos
   Foto: No

3. ¿El personal usa calzado de seguridad?
   Respuesta: Sí
   Observación: -
   Foto: No

4. ¿Se utilizan guantes apropiados para la tarea?
   Respuesta: Sí
   Observación: Guantes de cuero para soldadura
   Foto: [Subir foto de guantes]
```

**Validar**:
- ✅ Respuestas guardadas
- ✅ Fotos subidas correctamente
- ✅ Observaciones registradas

---

### 5.4 Completar Sección 2: Condiciones del Sitio

**Respuestas**:
```
1. ¿El área de trabajo está libre de obstáculos?
   Respuesta: No
   Observación: Materiales apilados en zona de paso
   Foto: [Subir foto de obstáculos]

2. ¿Las vías de circulación están señalizadas?
   Respuesta: Sí
   Observación: Señalización recién pintada
   Foto: [Subir foto]

3. ¿Hay buena iluminación en el área?
   Respuesta: Sí
   Observación: -
   Foto: No

4. ¿Las salidas de emergencia están despejadas?
   Respuesta: Sí
   Observación: -
   Foto: No

5. Calificación general del orden y limpieza (1-5)
   Respuesta: 3
   Observación: Mejorable, hay escombros en algunas áreas
   Foto: No
```

---

### 5.5 Completar Sección 3: Herramientas

**Respuestas**:
```
1. ¿Las herramientas eléctricas tienen cables en buen estado?
   Respuesta: No
   Observación: Amoladora con cable pelado - RETIRADA DE SERVICIO
   Foto: [Subir foto del cable dañado]

2. ¿Los andamios están correctamente armados?
   Respuesta: Sí
   Observación: Revisados por supervisor
   Foto: [Subir foto]

3. ¿Las escaleras están en buen estado?
   Respuesta: Sí
   Observación: -
   Foto: No

4. Observaciones generales de herramientas
   Respuesta: "Se requiere reemplazo de amoladora. 
              Resto de herramientas en buen estado.
              Inventario completo."
   Foto: No
```

---

### 5.6 Finalizar Checklist

**Acción**: Hacer clic en "Finalizar Inspección"

**Datos finales**:
```
Firma digital: [Firmar con dedo/mouse]
Fecha y hora de completado: [Automático]
Ubicación GPS: [Automático si está habilitado]
```

**Validar**:
- ✅ Checklist marcado como "Completado"
- ✅ Firma registrada
- ✅ Fecha y hora guardadas
- ✅ Ya no aparece en tareas pendientes
- ✅ Aparece en historial de inspecciones completadas

---

## 📊 FASE 6: Revisión de Resultados (Admin)

### 6.1 Login como Admin

**Cerrar sesión de Operario**

**Login**: `admin@ep.com` / `password`

---

### 6.2 Ver Dashboard de Checklists

**Navegación**: Menú → Checklists → Dashboard

**Validar**:
- ✅ Estadística de checklists completados actualizada
- ✅ Checklist recién completado aparece en "Últimas Inspecciones"
- ✅ Indicadores de cumplimiento visibles

---

### 6.3 Revisar Checklist Completado

**Navegación**: Menú → Checklists → Historial de Inspecciones

**Filtrar por**:
```
Técnico: Juan Operario
Fecha: Hoy
Estado: Completado
```

**Acción**: Hacer clic en el checklist completado

**Revisar**:
- ✅ Todas las respuestas visibles
- ✅ Observaciones legibles
- ✅ Fotos cargadas correctamente
- ✅ Firma del operario visible
- ✅ Fecha y hora de completado
- ✅ Ubicación GPS (si aplica)

---

### 6.4 Identificar Hallazgos

**Hallazgos detectados en el checklist**:

1. **Hallazgo 1**: Falta de chaleco reflectivo en 2 trabajadores
   - Sección: EPP
   - Severidad: Media
   - Acción requerida: Proveer chalecos

2. **Hallazgo 2**: Materiales apilados en zona de paso
   - Sección: Condiciones del Sitio
   - Severidad: Alta
   - Acción requerida: Reordenar materiales

3. **Hallazgo 3**: Amoladora con cable pelado
   - Sección: Herramientas
   - Severidad: Crítica
   - Acción requerida: Reemplazo inmediato

**Acción**: Crear hallazgos desde el checklist (si el sistema lo permite) o manualmente

---

### 6.5 Generar Reporte PDF

**Navegación**: Checklist completado → Exportar PDF

**Validar**:
- ✅ PDF generado correctamente
- ✅ Incluye todas las secciones
- ✅ Fotos embebidas en el PDF
- ✅ Firma visible
- ✅ Formato profesional

---

## 🔍 FASE 7: Gestión de Hallazgos (Admin)

### 7.1 Crear Hallazgo desde Inspección

**Navegación**: Menú → Hallazgos → Nuevo Hallazgo

**Hallazgo 1**:
```
Título: Falta de EPP - Chalecos Reflectivos
Descripción: 2 trabajadores nuevos sin chaleco reflectivo
Empresa: Constructora ABC
Sede: Obra Central - Torre Norte
Categoría: Seguridad
Severidad: Media
Estado: Abierto
Responsable: Juan Operario
Fecha límite: [Hoy + 2 días]
Origen: Inspección Diaria de Seguridad
```

**Hallazgo 2**:
```
Título: Obstrucción de vía de circulación
Descripción: Materiales apilados bloqueando zona de paso
Empresa: Constructora ABC
Sede: Obra Central - Torre Norte
Categoría: Orden y Limpieza
Severidad: Alta
Estado: Abierto
Responsable: Supervisor de Obra
Fecha límite: [Hoy + 1 día]
Origen: Inspección Diaria de Seguridad
```

**Hallazgo 3**:
```
Título: Herramienta eléctrica en mal estado
Descripción: Amoladora con cable pelado - PELIGRO ELÉCTRICO
Empresa: Constructora ABC
Sede: Obra Central - Torre Norte
Categoría: Herramientas
Severidad: Crítica
Estado: Abierto
Responsable: Encargado de Mantenimiento
Fecha límite: [Inmediato]
Origen: Inspección Diaria de Seguridad
Foto: [Adjuntar foto del cable]
```

**Validar**:
- ✅ 3 hallazgos creados
- ✅ Vinculados a la inspección
- ✅ Responsables asignados
- ✅ Notificaciones enviadas (si aplica)

---

## ✅ FASE 8: Validación Final

### 8.1 Checklist de Validación Completa

**Módulo: Empresas y Sedes**
- ✅ Empresa creada correctamente
- ✅ Sede creada y asociada a empresa
- ✅ Relaciones funcionando

**Módulo: Usuarios**
- ✅ Operario asignado a sede
- ✅ Permisos correctos por rol

**Módulo: Categorías**
- ✅ Categoría principal creada
- ✅ 3 subcategorías creadas
- ✅ Jerarquía correcta

**Módulo: Checklists**
- ✅ Checklist creado con 3 secciones
- ✅ 13 items/preguntas configurados
- ✅ Tipos de respuesta variados funcionando

**Módulo: Asignaciones**
- ✅ Asignación creada correctamente
- ✅ Operario puede ver su tarea

**Módulo: Inspecciones**
- ✅ Checklist completado exitosamente
- ✅ Todas las respuestas guardadas
- ✅ Fotos subidas correctamente
- ✅ Firma digital registrada
- ✅ Fecha y hora correctas

**Módulo: Reportes**
- ✅ PDF generado correctamente
- ✅ Dashboard actualizado
- ✅ Estadísticas correctas

**Módulo: Hallazgos**
- ✅ Hallazgos creados desde inspección
- ✅ Severidades asignadas
- ✅ Responsables notificados

---

## 📈 Métricas de Éxito

Al completar este caso de uso, deberías tener:

- **1** Empresa creada
- **1** Sede creada
- **1** Categoría principal
- **3** Subcategorías
- **1** Checklist con 3 secciones
- **13** Items/preguntas configurados
- **1** Asignación activa
- **1** Inspección completada
- **3** Hallazgos registrados
- **1** PDF generado
- **2** Usuarios activos (Admin + Operario)

---

## 🐛 Bugs a Documentar Durante el Testing

Mientras realizas este caso de uso, documenta:

1. **Errores encontrados**:
   - Descripción del error
   - Pasos para reproducir
   - Comportamiento esperado vs actual
   - Screenshot/video

2. **Validaciones faltantes**:
   - Campos que deberían ser obligatorios
   - Formatos no validados
   - Duplicados permitidos

3. **Problemas de UX**:
   - Flujos confusos
   - Botones poco claros
   - Falta de feedback al usuario

4. **Performance**:
   - Pantallas lentas
   - Queries pesadas
   - Tiempos de carga largos

---

## 📝 Notas Adicionales

### Variaciones del Caso de Uso

Una vez completado el caso base, puedes probar variaciones:

1. **Checklist sin secciones** (flat)
2. **Checklist con solo preguntas Sí/No**
3. **Checklist con muchas fotos**
4. **Asignación a múltiples operarios**
5. **Checklist recurrente (diario/semanal)**

### Datos de Prueba Adicionales

Si quieres expandir el testing:
- Crear 2-3 empresas más
- Crear múltiples sedes por empresa
- Crear diferentes tipos de checklists
- Asignar a diferentes operarios

---

## 🎯 Objetivo Final

Este caso de uso completo te permitirá:
- ✅ Validar el flujo end-to-end
- ✅ Identificar bugs y problemas
- ✅ Documentar el comportamiento del sistema
- ✅ Tener datos de prueba realistas
- ✅ Preparar el sistema para producción

**¡Buena suerte con el testing!** 🚀
