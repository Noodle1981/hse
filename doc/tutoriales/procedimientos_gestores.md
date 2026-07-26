# 📄 Manual de Procedimientos Operativos: Gestores

---

## 1. Ciclo de Vida y Auditoría de Informes Maestros (`MasterReport`)

El **Informe Maestro** es el documento legal y técnico consolidado que la Consultora entrega mensualmente a la empresa cliente para certificar el estado de cumplimiento de la planta.

### Flujo de Estados del Informe Maestro

```
  ┌──────────────┐      ┌─────────────────┐      ┌──────────────────┐      ┌──────────────┐
  │  SOLICITADO  │ ───> │   EN REDACCIÓN  │ ───> │    FINALIZADO    │ ───> │   APROBADO   │
  │ (Por Gestor) │      │  (Por Técnico)  │      │  (Revisión Gestor)│      │  (Cierre)    │
  └──────────────┘      └─────────────────┘      └────────┬─────────┘      └──────────────┘
                                                          │ Devolución con
                                                          │ observacion_revision
                                                          └─────────┘
```

### Procedimiento de Auditoría y Revisión
1. **Paso 1 (Solicitud):** El Gestor crea la solicitud del informe indicando el periodo, la empresa cliente, la sede y el técnico redactor asignado. El informe nace en estado `'solicitado'`.
2. **Paso 2 (Revisión de Borrador Finalizado):** Cuando el Técnico completa el trabajo de gabinete y presiona "Finalizar Redacción", el informe cambia a `'finalizado'`. La botonera del Gestor se activa permitiendo dos acciones:
   * **Observar / Devolver a Redacción:** Si detecta inconsistencias o faltantes, el Gestor redacta las correcciones en el campo **`observacion_revision`** y presiona **"Devolver a Técnico"**. El estado pasa a `'en_redaccion'` y los inputs del técnico vuelven a ser editables.
   * **Aprobar Informe:** Si el informe es conforme, el Gestor presiona **"Aprobar Informe"**. El estado pasa a `'aprobado'`, el campo `observacion_revision` se limpia automáticamente y el documento se inmuta de forma permanente.
3. **Paso 3 (Inmutabilidad de Firma):** Al aprobarse el informe, el motor PDF vincula directamente la firma de perfil aprobada del Técnico (`users.firma`). El documento final renderiza la firma inmutable garantizando la autenticidad e integridad legal.

---

## 2. Auditoría SRE y Monitoreo de Desvíos Críticos

Para garantizar el cumplimiento de normativas de seguridad e higiene laboral (ISO 45001 / Ley 19.587):

### Consola de Monitoreo en Tiempo Real
1. Ingrese a **"Historial de Inspecciones"** (`gestor/inspections`).
2. **Semáforo de Cumplimiento:**
   * 🟢 **Pasó (Verde Esmeralda):** Inspección completada sin hallazgos ni desvíos normativos.
   * 🔴 **Falló ⚠️ (Rojo Alerta Neón):** Se detectó una no-conformidad crítica.
3. **Counter Badges de Falla Crítica:** Si el técnico activó el flujo de intercepción en campo ("Rechazar y Finalizar"), el checklist mostrará la insignia de advertencia indicando que las preguntas posteriores fueron marcadas automáticamente como `"No evaluado por falla crítica"`.

---

## 3. Emisión de Constancias de Visita y Certificados PDF

### Consolidación Diaria por Servidor
Para evitar la saturación de documentos cuando un técnico inspecciona múltiples activos (ej. 20 vehículos) el mismo día:
1. El backend consolida automáticamente las inspecciones por fecha (`DATE(completed_at)`) y sede (`sede_id`).
2. Cada fila agrupada genera la **Constancia de Visita Principal** (`VisitCertificate`), firmada digitalmente por el responsable del cliente en planta (`responsible_signature`).

### Descarga de Certificados Oficiales
* **Constancia de Visita:** Descargue el PDF consolidado con el desglose de horas de campo, horas de gabinete y firma del cliente.
* **Certificado de Capacitaciones:** Descargue el acta de asistencia oficial (`training_attendances`) con la lista nominal de trabajadores, sus DNI y sus firmas digitalizadas.

---

## 4. Gestión y Cierre de Hallazgos (`cerrado` / `verificado`)

Para mantener el control en el pilar HSE, la transición de estados de los hallazgos reportados en campo sigue una política estricta de permisos:

### Matriz de Permisos de Transición

| Estado Objetivo | Quién puede aplicarlo | Requisito |
|---|---|---|
| `en_proceso` | Técnico / Gestor | Inicio de tareas de mitigación |
| `resuelto` | Técnico / Gestor | Carga de foto de evidencia "después" |
| `cerrado` | **Exclusivo Gestor** | Auditoría final de conformidad aprobada |
| `verificado` | **Exclusivo Gestor** | Constatación presencial en la siguiente visita |

### Procedimiento de Cierre por el Gestor
1. Ingrese a la consola de **"Hallazgos"** (`gestor/hallazgos`).
2. Seleccione el hallazgo en estado `resuelto`.
3. Valide la prueba fotográfica de corrección presentada por la planta o el técnico.
4. Si la solución es satisfactoria, presione **"Cerrar Auditoría (Cerrado)"**.
5. El sistema registrará la trazabilidad inmutable en `HallazgoSeguimiento` con su ID de usuario y marca de tiempo.
