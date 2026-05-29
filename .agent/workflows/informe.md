---
description: 📁 Contexto del Sistema: Rama feature-inertia-react (Laravel 12 + React 19 + Inertia 2.x)
---

1. 🔍 Hallazgo de Diagnóstico en Capacitaciones
Durante la auditoría del módulo de Capacitaciones (Formación) en Campo, se identificó un conflicto lógico de precedencia en el accesor dinámico del modelo core VisitTraining.php (getAttendeesCountAttribute()), encargado de contabilizar a los operarios capacitados en las sesiones físicas in-situ:

La Causa Raíz: El sistema cuenta con dos flujos paralelos para registrar asistentes:
Vía Tabla Normalizada (training_attendances): Utilizada para capacitaciones corporativas planificadas de fondo.
Vía Columna JSON local (attendees en visit_trainings): Utilizada para firmas manuscritas rápidas capturadas en caliente por la tablet del técnico durante la visita presencial.
El Conflicto: Si la sesión física de la visita estaba vinculada a una capacitación de fondo en el Plan Anual ($this->training era true), el accesor intentaba consultar la tabla relacional externa de asistencias (que estaba vacía al no haber sido procesada en oficina aún), ignorando por completo el campo JSON local del modelo (donde sí residían físicamente las firmas capturadas en caliente de los técnicos y operarios en la mina). Esto resultaba en un conteo erróneo de 0 Asistentes en el Dashboard.
2. 🛡️ Solución Preventiva Aplicada (Fijada y Estable)
Para garantizar la visualización de los datos actuales de prueba sin alterar el esquema relacional, se inyectó una compuerta lógica con fallback dinámico en VisitTraining.php:

php


public function getAttendeesCountAttribute()
{
    // 1. Si está vinculado a un entrenamiento de fondo, intenta contar en la tabla relacional
    if ($this->training) {
        $count = $this->training->attendances()->count();
        if ($count > 0) {
            return $count;
        }
    }
    // 2. Si la tabla relacional está vacía (o es una sesión ad-hoc), cae de forma segura en la columna JSON local
    return is_array($this->attendees) ? count($this->attendees) : 0;
}
3. ⚠️ El Dilema de Arquitectura Actual (A Mediano/Largo Plazo)
Trabajar con columnas de tipo JSON serializado (attendees) para persistir relaciones dinámicas (operarios y sus firmas Base64) presenta desventajas severas a medida que el sistema escala:

Cero Integridad Referencial: No existen llaves foráneas (Foreign Keys). Si un operario modifica su DNI, puesto o firma oficial, los registros antiguos en formato de texto JSON quedan con datos huérfanos desactualizados.
Penalización de Rendimiento: SQLite/MySQL tienen que parsear cadenas de texto completas para realizar búsquedas o filtros (ej. buscar qué capacitaciones tomó el DNI 95827361), inhabilitando el uso de índices de base de datos veloces.
Saturación de Memoria (Bloat): Almacenar firmas manuscritas completas codificadas en Base64 en una columna de texto dentro de la misma fila de visit_trainings ralentiza drásticamente las consultas del historial de visitas.
4. 🔮 Propuesta de Refactorización Relacional Futura (Clean Architecture)
Dado que el entorno actual cuenta con el script táctico resetplanes.php para reiniciar y depurar tablas operativas sin alterar registros maestros, la hoja de ruta de base de datos exige la depuración absoluta del JSON local:

Paso 1: Eliminar por completo la columna attendees de la tabla visit_trainings.
Paso 2: Normalizar la persistencia de firmas en campo. Cada vez que un operario firme una tablet en la mina, el backend insertará una fila indexada en la tabla relacional e independiente training_attendances vinculada mediante visit_training_id.
Paso 3: Simplificar la relación de Eloquent en VisitTraining.php a una única y veloz consulta:
php


public function getAttendeesCountAttribute() {
    return $this->attendances()->count();
}
5. 💎 Ventajas de la Nueva Estructura del Historial
Con los arreglos backend e interfaz React inyectados en la Matriz de Cumplimiento Operativo (AnnualPlanMatrix.jsx), se han desbloqueado beneficios invaluables:

Trazabilidad Multitécnico: Si hay 4 técnicos en frentes mineros de turnos rotativos ejecutando el mismo plan, el modal interactivo de celda detalla cronológicamente quién hizo qué día, logrando auditoría interna forense al 100%.
Métricas HUD en Columnas: Visualización premium en columnas de checklists realizados, casos exitosos y fallidos (encendiendo glows rojos reactivos solo si hay fallas, manteniendo limpio el HUD si todo está OK).
Safe ISO-8601 Date Parsing: Se eliminaron las excepciones Invalid Date de Javascript mediante normalizadores del lado del cliente.
Pragmatismo para Planificar: El modal es puramente informativo, diseñado para incentivar conversaciones ejecutivas y análisis de datos en caliente en lugar de forzar descargas redundantes de PDFs.