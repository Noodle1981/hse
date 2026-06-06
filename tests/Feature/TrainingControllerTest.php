<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\Training;
use App\Models\User;
use App\Models\VisitCertificate;
use App\Models\VisitTraining;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrainingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $tecnico;

    protected $empresa;

    protected $sede;

    protected $visit;

    protected $training;

    protected $visitTraining;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->admin()->create();
        $this->tecnico = User::factory()->create(['rol' => 'tecnico']);

        $this->empresa = Empresa::factory()->create();
        $this->sede = Sede::factory()->create(['empresa_id' => $this->empresa->id]);
        $this->tecnico->sedes()->attach($this->sede->id);

        $this->visit = VisitCertificate::factory()->create([
            'user_id' => $this->tecnico->id,
            'sede_id' => $this->sede->id,
            'empresa_id' => $this->empresa->id,
            'status' => 'in_progress',
            'start_time' => now(),
        ]);

        $this->training = Training::factory()->create([
            'empresa_id' => $this->empresa->id,
            'sede_id' => $this->sede->id,
            'user_id' => $this->tecnico->id,
            'title' => 'Capacitación sobre Plataformas',
            'status' => 'pending',
            'is_template' => false,
        ]);

        $this->visitTraining = VisitTraining::create([
            'visit_certificate_id' => $this->visit->id,
            'training_id' => $this->training->id,
            'is_adhoc' => false,
            'title' => $this->training->title,
            'description' => 'Capacitación táctica de seguridad',
            'duration_minutes' => 15,
            'attendees' => [],
        ]);
    }

    /** @test */
    public function an_admin_can_load_trainings_index_page()
    {
        $response = $this->actingAs($this->admin)->get(route('gestor.trainings.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function a_technician_can_load_live_training_session_page()
    {
        $response = $this->actingAs($this->tecnico)->get(route('tecnico.trainings.session', $this->visitTraining->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function a_technician_can_add_an_attendee_to_training()
    {
        $payload = [
            'name' => 'Marcos Lopez',
            'position' => 'Operador de Torre',
            'dni' => '95827361',
            'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ];

        $response = $this->actingAs($this->tecnico)->post(
            route('tecnico.trainings.add-attendee', $this->visitTraining->id),
            $payload
        );

        $response->assertRedirect();

        $this->visitTraining->refresh();
        $this->assertCount(1, $this->visitTraining->attendees);
        $this->assertEquals('Marcos Lopez', $this->visitTraining->attendees[0]['name']);
        $this->assertEquals('95827361', $this->visitTraining->attendees[0]['dni']);
    }

    /** @test */
    public function a_technician_can_finalize_training_with_external_instructor()
    {
        // Registrar al menos un asistente en la base de datos para la sesión
        \App\Models\TrainingAttendance::create([
            'training_id' => $this->training->id,
            'visit_training_id' => $this->visitTraining->id,
            'employee_name' => 'Test Attendee',
            'dni' => '12345678',
            'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ]);

        $payload = [
            'duration_minutes' => 25,
            'is_external_instructor' => true,
            'trainer_name' => 'Ing. Juan Carlos (Especialista)',
            'instructor_signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
            'responsible_name' => 'Supervisor Responsable',
            'responsible_signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
        ];

        $response = $this->actingAs($this->tecnico)->post(
            route('tecnico.trainings.finalize', $this->visitTraining->id),
            $payload
        );

        $response->assertRedirect();

        $this->visitTraining->refresh();
        $this->training->refresh();
        $this->visit->refresh();

        // 1. Duración actualizada en visit_trainings
        $this->assertEquals(25, $this->visitTraining->duration_minutes);

        // 2. Training marcado como completado y trainer_name actualizado
        $this->assertEquals('completed', $this->training->status);
        $this->assertEquals('Ing. Juan Carlos (Especialista)', $this->training->trainer_name);

        // 3. Firma guardada como archivo PNG físico en storage
        $this->assertNotNull($this->visit->instructor_signature);
        Storage::disk('public')->assertExists($this->visit->instructor_signature);
    }
}
