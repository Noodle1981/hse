---
description:  ARQUITECTURA DE TRAZABILIDAD DE ACTIVOS Y ESTRATEGIA DE MONITOREO
---

1. Fundamentos Normativos: Implementación IRAM 3517-2:2020
La integridad del módulo de activos en XamaSafe se sustenta en el cumplimiento estricto de la norma IRAM 3517-2:2020. Como ingenieros de confiabilidad, traducimos estos requisitos físicos en restricciones de software inmutables para garantizar la validez legal de las inspecciones.
Requisitos Críticos de Dotación y Reserva (DR)
Dotación Mínima: El sistema calcula la cobertura según el riesgo de incendio, actividad y carga de fuego.
Reserva del 10%: Obligación de un 10% adicional (redondeo hacia arriba).
Lógica de Homogeneidad:
Si la dotación es homogénea, la reserva debe poseer idénticas características.
Si es heterogénea, la reserva debe ser obligatoriamente ABC de 5kg.
Identificación Visual: Los activos de reserva deben ser registrados con el atributo de Etiqueta Verde (Green Label) para su diferenciación en auditorías.
Frecuencia de Control: El scheduler de XamaSafe debe forzar controles 4 veces al año (frecuencia trimestral mínima), permitiendo intervalos menores según el riesgo.
Puntos de Control en Inspección Digital
La entidad dynamic_inspections valida los siguientes parámetros físicos mediante lógica de validación en el cliente y servidor:
Altura de Colocación:
Masa ≤ 20kg: Máximo 1,5m.
Masa > 20kg: Máximo 1m.
Señalización HUD: Validación de chapa baliza y señalización en altura.
Mantenimiento de Baldes: Verificación de capacidad (5-7 litros), presencia de tapa/manija y recambio anual de gránulos absorbentes.
2. Arquitectura de Datos: Flexibilidad mediante Metadata JSON
Para evitar el "bloat" de columnas en la tabla assets y permitir la extensibilidad a futuro (ej. hidrantes, detectores), implementamos una arquitectura de Metadata Dinámica.
Esquema de la Tabla assets
La tabla utiliza el campo asset_type_id como clave para la validación del esquema. No es posible insertar metadata sin un tipo de activo definido, ya que este actúa como el contrato de datos.
identifier: String indexado para búsqueda rápida (SKU/Serie).
status: Enum para control de ciclo de vida (operativo, fuera_de_servicio).
metadata: Columna TEXT (JSON) que almacena el estado extendido.
Vinculación Dinámica y JSON Merge
La columna vincular_a_metadata en dynamic_questions permite que una respuesta de inspección actualice el activo. El proceso es el siguiente:
Al recibir una dynamic_answer, el sistema verifica si la pregunta tiene un valor en vincular_a_metadata (ej: vencimiento_ph).
Si existe, el AssetObserver realiza un JSON Merge atómico en la columna assets.metadata.
Ejemplo de estructura persistida:
{
  "asset_type_context": "extintor_abc",
  "ultima_recarga": "2024-03-12",
  "vencimiento_ph": "2025-03-12",
  "marca": "Georgia",
  "capacidad_kg": 5,
  "reserva_etiqueta_verde": true
}
3. Lógica de Negocio y Separación de Responsabilidades
Implementación del AssetObserver (Audit Trail)
Utilizamos un AssetObserver en Laravel para desacoplar la lógica de trazabilidad. Este componente intercepta los eventos updated para mantener un Immutable Ledger en la tabla audit_logs.
Payload de Auditoría: El observador captura el json_encode de los atributos $model->getOriginal() y $model->getDirty().
Trazabilidad: Se registra el user_id del técnico y el target_id del activo, permitiendo reconstruir la historia clínica del equipo ante incidentes legales.
Transiciones de Estado y Hardening de Seguridad
Falla Crítica: Si una inspección devuelve un resultado negativo basado en la failure_logic (ej: presión baja), el sistema dispara una transición automática del status del activo a fuera_de_servicio.
Inmutabilidad por Política: Cualquier intento de modificar un asset o dynamic_inspection vinculado a un visit_certificate con estado signed o completed debe lanzar una AuthorizationException a nivel de Policy, blindando el registro ante alteraciones post-firma.
4. Estrategia de Dashboards y Visualización SRE
Identidad Visual: Technical Module
Todo dashboard de activos debe regirse por la Guía de Estética Visual Premium DARK, utilizando estrictamente la gama Naranja Técnico (ep-primary).
KPIs de Consola de Operaciones
MÉTRICA
VISUALIZACIÓN HUD
Cumplimiento Normativo (%)
font-black italic uppercase tracking-tight con resplandor (glow) naranja.
Ratio de Éxitos/Fallas
Badges rounded-2xl con sombra de neón (Emerald/Red).
Alertas de Reserva
Badges de notificación disruptivos (absolute -top-3) para señalar falta de stock del 10%.
Vencimientos Críticos
Tarjetas Glassmorphic (bg-white/5) con backdrop-blur-xl.
Se implementan Tactical Pills para el filtrado reactivo por Sede y Empresa, permitiendo una navegación fluida en notebooks de 13" y 15" sin desbordes horizontales.
5. Verificación de Presencia (Proof of Presence)
Georeferenciación y Haversine
La integridad de la visita se valida mediante el servicio TimeTrackingService. No se permite la transición a completed sin la verificación de los campos checkin_lat/lng y checkout_lat/lng.
Cálculo: Se aplica la fórmula de Haversine para determinar la distancia entre el dispositivo del técnico y las coordenadas de la Sede.
Validación: Si la distancia supera el radio_tolerancia_metros definido, el sistema marca gps_verified = 0.
Auditoría: El flag gps_verified es el KPI principal para que el Gestor valide la veracidad de la inspección física.
6. Monitoreo de Salud del Sistema (SRE Hardening)
Performance y Escalabilidad
Eager Loading: Para mitigar riesgos de consultas N+1 en la carga masiva de activos (especialmente al renderizar metadata en sedes con >500 equipos), se exige el uso de Asset::with(['assetType', 'empresa']) en el controlador.
Chunking: El procesamiento de reportes maestros y la generación de PDFs de inspección masiva debe ejecutarse mediante job_batches para evitar el agotamiento de memoria del servidor.
SRE Monitoring: Monitoreo activo de failed_jobs en TrainingPDFController mediante Laravel Pulse. Cualquier fallo en la renderización de firmas Base64 (convertidas a PNG para evitar "JSON bloat") debe alertar inmediatamente al equipo SRE.