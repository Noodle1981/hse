<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;
use App\Models\Sede;
use App\Models\VisitCertificate;
use App\Services\TimeTrackingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimeTrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    private TimeTrackingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TimeTrackingService();
    }

    /**
     * Test calculation of field hours from start_time and end_time.
     */
    public function test_calculate_field_hours(): void
    {
        $visit = new VisitCertificate([
            'start_time' => Carbon::create(2026, 5, 24, 8, 0, 0),
            'end_time' => Carbon::create(2026, 5, 24, 13, 30, 0), // 5.5 hours
        ]);

        $hours = $this->service->calculateFieldHours($visit);
        $this->assertEquals(5.5, $hours);
    }

    /**
     * Test calculation returns 0.0 when start_time or end_time is null.
     */
    public function test_calculate_field_hours_with_null_dates(): void
    {
        $visitNullStart = new VisitCertificate([
            'start_time' => null,
            'end_time' => Carbon::create(2026, 5, 24, 13, 30, 0),
        ]);
        $this->assertEquals(0.0, $this->service->calculateFieldHours($visitNullStart));

        $visitNullEnd = new VisitCertificate([
            'start_time' => Carbon::create(2026, 5, 24, 8, 0, 0),
            'end_time' => null,
        ]);
        $this->assertEquals(0.0, $this->service->calculateFieldHours($visitNullEnd));
    }

    /**
     * Test verifyWorkingHours returns true when es_roster_24_7 is true.
     */
    public function test_verify_working_hours_roster_24_7(): void
    {
        $sede = new Sede([
            'es_roster_24_7' => true,
            'dias_operativos' => [], // Empty days
            'hora_apertura' => null,
            'hora_cierre' => null,
        ]);

        // Check-in on a Sunday at midnight (usually not standard working hours)
        $sundayCheckin = Carbon::create(2026, 5, 24, 0, 0, 0); // 2026-05-24 is a Sunday

        $isValid = $this->service->verifyWorkingHours($sede, $sundayCheckin);
        $this->assertTrue($isValid);
    }

    /**
     * Test verifyWorkingHours in standard operation mode.
     */
    public function test_verify_working_hours_standard_success(): void
    {
        $sede = new Sede([
            'es_roster_24_7' => false,
            'dias_operativos' => ['Lunes', 'Miércoles', 'Viernes'],
            'hora_apertura' => '08:00:00',
            'hora_cierre' => '17:00:00',
        ]);

        // 2026-05-25 is a Monday (Lunes) at 10:00 AM (within limits)
        $mondayCheckin = Carbon::create(2026, 5, 25, 10, 0, 0);

        $isValid = $this->service->verifyWorkingHours($sede, $mondayCheckin);
        $this->assertTrue($isValid);
    }

    /**
     * Test verifyWorkingHours returns false when checking in on a non-operating day.
     */
    public function test_verify_working_hours_standard_invalid_day(): void
    {
        $sede = new Sede([
            'es_roster_24_7' => false,
            'dias_operativos' => ['Lunes', 'Miércoles', 'Viernes'],
            'hora_apertura' => '08:00:00',
            'hora_cierre' => '17:00:00',
        ]);

        // 2026-05-26 is a Tuesday (Martes) at 10:00 AM (not in dias_operativos)
        $tuesdayCheckin = Carbon::create(2026, 5, 26, 10, 0, 0);

        $isValid = $this->service->verifyWorkingHours($sede, $tuesdayCheckin);
        $this->assertFalse($isValid);
    }

    /**
     * Test verifyWorkingHours returns false when checking in outside of operating hours.
     */
    public function test_verify_working_hours_standard_invalid_time(): void
    {
        $sede = new Sede([
            'es_roster_24_7' => false,
            'dias_operativos' => ['Lunes', 'Miércoles', 'Viernes'],
            'hora_apertura' => '08:00:00',
            'hora_cierre' => '17:00:00',
        ]);

        // 2026-05-25 is a Monday (Lunes) at 19:30 (outside 08:00 to 17:00)
        $mondayNightCheckin = Carbon::create(2026, 5, 25, 19, 30, 0);

        $isValid = $this->service->verifyWorkingHours($sede, $mondayNightCheckin);
        $this->assertFalse($isValid);
    }
}
