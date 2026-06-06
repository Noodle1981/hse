<?php

namespace Tests\Feature;

use App\Livewire\Visits\VisitInProgress;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CoreVisitFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $sede;

    protected $empresa;

    protected $visit;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Preparar actores y contexto
        $this->user = User::factory()->admin()->create();

        $this->empresa = Empresa::factory()->create();
        $this->sede = Sede::factory()->create(['empresa_id' => $this->empresa->id]);

        // 2. Crear visita en progreso
        $this->visit = VisitCertificate::factory()->create([
            'user_id' => $this->user->id,
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
            'status' => 'in_progress',
            'start_time' => now(),
        ]);
    }

    /** @test */
    public function a_technician_can_complete_a_full_visit_flow()
    {
        $this->actingAs($this->user);

        // 3. Simular actividad en la visita vía Livewire
        $component = Livewire::test(VisitInProgress::class, ['visit' => $this->visit])
            ->assertSee($this->sede->nombre)
            ->assertSee('En Progreso');

        // 4. Simular registro de Hallazgo (disparando evento que el componente escucha)
        $hallazgo = Hallazgo::factory()->create([
            'visit_certificate_id' => $this->visit->id,
            'user_id' => $this->user->id,
            'sede_id' => $this->sede->id,
        ]);

        $component->call('onHallazgoCreated')
            ->assertSet('showHallazgoModal', false);

        // 5. Simular registro de Capacitación
        $training = Training::factory()->create([
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
        ]);

        // El componente crea una entrada en visit_trainings
        $this->visit->trainings()->create([
            'training_id' => $training->id,
            'title' => $training->title,
            'is_adhoc' => false,
        ]);

        $component->call('loadChecklists')
            ->assertCount('trainings', 1);

        // 6. Finalización con Firma
        $component->set('visitNotes', 'Visita técnica realizada sin contratiempos mayores. Se recomienda mantenimiento preventivo.')
            ->set('responsibleName', 'Juan Perez (Gerente Sede)')
            ->set('responsibleSignature', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
            ->call('finalizeVisit')
            ->assertHasNoErrors()
            ->assertRedirect(); // Redirige tras éxito

        // 7. Verificaciones Finales en DB
        $this->visit->refresh();
        $this->assertEquals('completed', $this->visit->status);
        $this->assertNotNull($this->visit->end_time);
        $this->assertEquals('Juan Perez (Gerente Sede)', $this->visit->responsible_name);
        $this->assertNotNull($this->visit->signature);

        // Verificar que los hallazgos y capacitaciones sigan vinculados
        $this->assertCount(1, $this->visit->hallazgos);
        $this->assertCount(1, $this->visit->trainings);
    }

    /** @test */
    public function visit_finalization_requires_validation()
    {
        $this->actingAs($this->user);

        Livewire::test(VisitInProgress::class, ['visit' => $this->visit])
            ->call('finalizeVisit')
            ->assertHasErrors(['visitNotes', 'responsibleName', 'responsibleSignature']);
    }
}
