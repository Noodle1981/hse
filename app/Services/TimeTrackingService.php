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
        if (! in_array($dayOfWeekInSpanish, $diasOperativos, true)) {
            return false;
        }

        // Verificar horario de apertura y cierre
        if ($sede->hora_apertura && $sede->hora_cierre) {
            $timeFormat = 'H:i:s';
            $checkinTimeStr = $carbonCheckin->format($timeFormat);

            // Asegurar formato de comparación HH:MM:SS
            try {
                $aperturaStr = Carbon::createFromFormat('H:i:s', $sede->hora_apertura)->format($timeFormat);
            } catch (\Exception $e) {
                try {
                    $aperturaStr = Carbon::createFromFormat('H:i', $sede->hora_apertura)->format($timeFormat);
                } catch (\Exception $e2) {
                    $aperturaStr = '00:00:00';
                }
            }

            try {
                $cierreStr = Carbon::createFromFormat('H:i:s', $sede->hora_cierre)->format($timeFormat);
            } catch (\Exception $e) {
                try {
                    $cierreStr = Carbon::createFromFormat('H:i', $sede->hora_cierre)->format($timeFormat);
                } catch (\Exception $e2) {
                    $cierreStr = '23:59:59';
                }
            }

            if ($checkinTimeStr < $aperturaStr || $checkinTimeStr > $cierreStr) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calcula las horas de campo acumuladas de una visita restando el start_time y end_time.
     * Retorna el valor en formato decimal (ej: 4.5 horas).
     */
    public function calculateFieldHours(VisitCertificate $visit): float
    {
        if (! $visit->start_time || ! $visit->end_time) {
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
     * Valida la presencia GPS del técnico comparando coordenadas con la Sede.
     */
    public function verifyGpsPresence(VisitCertificate $visit): bool
    {
        $sede = $visit->sede;
        if (! $sede) {
            return true;
        }

        $isVerified = true;
        $tolerance = $sede->radio_tolerancia_metros ?? 500;

        if ($visit->checkin_lat !== null && $visit->checkin_lng !== null && $sede->latitud !== null && $sede->longitud !== null) {
            $checkinDistance = $this->calculateHaversineDistance(
                (float) $visit->checkin_lat,
                (float) $visit->checkin_lng,
                (float) $sede->latitud,
                (float) $sede->longitud
            );

            if ($checkinDistance > $tolerance) {
                \Illuminate\Support\Facades\Log::warning("GPS Check-in fuera de tolerancia para la visita #{$visit->id}", [
                    'checkin_distance' => $checkinDistance,
                    'tolerance' => $tolerance,
                ]);
                $isVerified = false;
            }
        }

        if ($visit->checkout_lat !== null && $visit->checkout_lng !== null && $sede->latitud !== null && $sede->longitud !== null) {
            $checkoutDistance = $this->calculateHaversineDistance(
                (float) $visit->checkout_lat,
                (float) $visit->checkout_lng,
                (float) $sede->latitud,
                (float) $sede->longitud
            );

            if ($checkoutDistance > $tolerance) {
                \Illuminate\Support\Facades\Log::warning("GPS Check-out fuera de tolerancia para la visita #{$visit->id}", [
                    'checkout_distance' => $checkoutDistance,
                    'tolerance' => $tolerance,
                ]);
                $isVerified = false;
            }
        }

        $visit->gps_verified = $isVerified;
        return $isVerified;
    }

    /**
     * Calcula la distancia en metros entre dos coordenadas geográficas usando la fórmula de Haversine.
     *
     * @return float Distancia en metros
     */
    public function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
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
