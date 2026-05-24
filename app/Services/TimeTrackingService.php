<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Sede;
use App\Models\VisitCertificate;
use Carbon\Carbon;

class TimeTrackingService
{
    /**
     * Verifica si la visita se inició dentro de los días y horas operativos de la Sede.
     * Si la sede opera bajo modalidad "Roster 24/7" (minería), se aprueba automáticamente.
     * 
     * @param Sede $sede
     * @param \DateTimeInterface $checkinTime
     * @return bool
     */
    public function verifyWorkingHours(Sede $sede, \DateTimeInterface $checkinTime): bool
    {
        // 1. Roster 24/7: Operación continua aprobada automáticamente
        if ($sede->es_roster_24_7) {
            return true;
        }

        // 2. Si no es roster 24/7, validar días y horarios operativos
        $carbonCheckin = Carbon::instance($checkinTime);
        
        // Obtener el día de la semana en español
        $diasMap = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
        
        $dayOfWeekInSpanish = $diasMap[$carbonCheckin->dayOfWeek];

        // Verificar si el día de la semana está admitido
        $diasOperativos = $sede->dias_operativos ?? [];
        if (!in_array($dayOfWeekInSpanish, $diasOperativos, true)) {
            return false;
        }

        // Verificar horario de apertura y cierre
        if ($sede->hora_apertura && $sede->hora_cierre) {
            $timeFormat = 'H:i:s';
            $checkinTimeStr = $carbonCheckin->format($timeFormat);
            
            // Asegurar formato de comparación HH:MM:SS
            $aperturaStr = Carbon::createFromFormat('H:i:s', $sede->hora_apertura)->format($timeFormat);
            $cierreStr = Carbon::createFromFormat('H:i:s', $sede->hora_cierre)->format($timeFormat);

            if ($checkinTimeStr < $aperturaStr || $checkinTimeStr > $cierreStr) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calcula las horas de campo acumuladas de una visita restando el start_time y end_time.
     * Retorna el valor en formato decimal (ej: 4.5 horas).
     * 
     * @param VisitCertificate $visit
     * @return float
     */
    public function calculateFieldHours(VisitCertificate $visit): float
    {
        if (!$visit->start_time || !$visit->end_time) {
            return 0.0;
        }

        $startTimestamp = $visit->start_time->getTimestamp();
        $endTimestamp = $visit->end_time->getTimestamp();

        $diffInSeconds = $endTimestamp - $startTimestamp;
        if ($diffInSeconds <= 0) {
            return 0.0;
        }

        $hours = $diffInSeconds / 3600.0;

        return round($hours, 2);
    }

    /**
     * Valida de forma preliminar la presencia GPS del técnico.
     * En el futuro, este método usará calculateHaversineDistance para verificar que las
     * coordenadas de checkin y checkout estén dentro del radio de tolerancia de la Sede.
     * 
     * @param VisitCertificate $visit
     * @return bool
     */
    public function verifyGpsPresence(VisitCertificate $visit): bool
    {
        // TODO: Implementar validación cruzando con las coordenadas de la sede:
        // $sede = $visit->sede;
        // if ($visit->checkin_lat && $visit->checkin_lng && $sede->latitud && $sede->longitud) {
        //     $distance = $this->calculateHaversineDistance(
        //         (float)$visit->checkin_lat, (float)$visit->checkin_lng,
        //         (float)$sede->latitud, (float)$sede->longitud
        //     );
        //     return $distance <= $sede->radio_tolerancia_metros;
        // }
        
        return true;
    }

    /**
     * Calcula la distancia en metros entre dos coordenadas geográficas usando la fórmula de Haversine.
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distancia en metros
     */
    private function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000.0; // metros

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2.0) * sin($latDelta / 2.0) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2.0) * sin($lonDelta / 2.0);

        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return $earthRadius * $c;
    }
}
