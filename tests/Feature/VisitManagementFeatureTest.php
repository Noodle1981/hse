<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sede;
use App\Models\Empresa;
use App\Models\VisitCertificate;
use App\Livewire\Visits\VisitInProgress;
use App\Livewire\Visits\VisitHistory;
use App\Livewire\Technician\TechnicianDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VisitManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $tecnico;
    protected $sede;
    protected $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['nombres' => 'Admin', 'apellidos' => 'User']);
        $this->tecnico = User::factory()->inspector()->create(['nombres' => 'Tecnico', 'apellidos' => 'User']);
        
        // Cargar roles si el sistema usa Spatie Permission o similar
        if (method_exists($this->tecnico, 'assignRole')) {
            $this->admin->assignRole('Administrador');
            $this->tecnico->assignRole('Técnico');
        }

        $this->empresa = Empresa::factory()->create();
        $this->sede = Sede::factory()->create(['empresa_id' => $this->empresa->id]);
    }

    /** @test */
    public function technician_sees_alert_when_visit_exceeds_threshold()
    {
        $this->actingAs($this->tecnico);

        // Crear una visita que inició hace 45 minutos (excede el umbral de 30 min)
        $visit = VisitCertificate::create([
            'user_id' => $this->tecnico->id,
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
            'visit_date' => now()->toDateString(),
            'start_time' => now()->subMinutes(45),
            'status' => 'in_progress',
        ]);

        // 1. Probar en VisitInProgress
        Livewire::test(VisitInProgress::class, ['visit' => $visit])
            ->assertSet('isVisitaExcedida', true)
            ->assertSee('ATENCIÓN: Esta visita lleva 0h 45min');

        // 2. Probar en TechnicianDashboard
        Livewire::test(TechnicianDashboard::class)
            ->assertSee('⚠️ VISITA EN CURSO: Llevas más de 30 minutos');
    }

    /** @test */
    public function alert_can_be_dismissed_and_reappears_every_30_minutes()
    {
        $this->actingAs($this->tecnico);

        // 1. Inicia hace 35 min (primer bloque) -> Debería mostrar alerta
        $visit = VisitCertificate::create([
            'user_id' => $this->tecnico->id,
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
            'visit_date' => now()->toDateString(),
            'start_time' => now()->subMinutes(35),
            'status' => 'in_progress',
        ]);

        $test = Livewire::test(VisitInProgress::class, ['visit' => $visit])
            ->assertSet('isVisitaExcedida', true)
            ->call('dismissAlert')
            ->assertSet('isVisitaExcedida', false)
            ->assertSet('dismissedAlertsCount', 1);

        // 2. Simular que el tiempo avanza a 65 min (segundo bloque)
        // Como no podemos "hacer avanzar" el tiempo real en la misma instancia fácilmente
        // sin inyectar un Clock, vamos a crear una nueva prueba o manipular la propiedad.
        
        $visit->start_time = now()->subMinutes(65);
        $visit->save();

        Livewire::test(VisitInProgress::class, ['visit' => $visit])
            ->set('dismissedAlertsCount', 1) // Simulamos que ya descartó la anterior
            ->call('calculateDuration')
            ->assertSet('isVisitaExcedida', true);
    }

    /** @test */
    public function admin_can_edit_visit_times_from_history()
    {
        $this->actingAs($this->admin);

        $visit = VisitCertificate::create([
            'user_id' => $this->tecnico->id,
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
            'visit_date' => now()->toDateString(),
            'start_time' => now()->subHours(1),
            'end_time' => now(),
            'status' => 'completed',
        ]);

        $newDate = now()->addDay()->format('Y-m-d');
        $newStart = '08:00';
        $newEnd = '10:00';

        Livewire::test(VisitHistory::class)
            ->call('editVisit', $visit->id)
            ->assertSet('showEditModal', true)
            ->set('editVisitDate', $newDate)
            ->set('editStartTime', $newStart)
            ->set('editEndTime', $newEnd)
            ->call('updateVisit')
            ->assertSet('showEditModal', false);

        $visit->refresh();
        $this->assertEquals($newDate, $visit->visit_date->format('Y-m-d'));
        $this->assertEquals($newStart, $visit->start_time->format('H:i'));
        $this->assertEquals($newEnd, $visit->end_time->format('H:i'));
    }

    /** @test */
    public function non_admin_cannot_edit_visit_times()
    {
        $this->actingAs($this->tecnico);

        $visit = VisitCertificate::create([
            'user_id' => $this->tecnico->id,
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
            'visit_date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 'completed',
        ]);

        Livewire::test(VisitHistory::class)
            ->call('editVisit', $visit->id)
            ->assertSet('showEditModal', false); // No debería abrirse
            
        $this->assertNull($visit->end_time);
    }

    /** @test */
    public function super_admin_cannot_edit_visit_times()
    {
        $superAdmin = User::factory()->create(['rol' => 'super_admin']);
        $this->actingAs($superAdmin);

        $visit = VisitCertificate::create([
            'user_id' => $this->tecnico->id,
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
            'visit_date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 'completed',
        ]);

        Livewire::test(VisitHistory::class)
            ->call('editVisit', $visit->id)
            ->assertSet('showEditModal', false); // No debería abrirse
    }
}
