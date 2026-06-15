<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Consultora;
use App\Models\User;
use App\Models\ConsultoraSuscripcionLog;

class ConsultantSubscriptionTraceabilityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_consultant_creates_an_initial_subscription_log()
    {
        $consultora = Consultora::create([
            'nombre' => 'Nueva Consultora',
            'cuit' => '20123456781',
            'estado_suscripcion' => 'activa',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        $this->assertDatabaseHas('consultora_suscripcion_logs', [
            'consultora_id' => $consultora->id,
            'estado_anterior' => null,
            'estado_nuevo' => 'activa',
        ]);
    }

    /** @test */
    public function changing_subscription_status_creates_new_log_with_user_association()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'estado_suscripcion' => 'activa',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        $superAdmin = User::create([
            'nombres' => 'Super',
            'apellidos' => 'Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'rol' => 'super_admin',
            'estado' => 'activo',
            'consultora_id' => null,
        ]);

        $this->actingAs($superAdmin);

        // Update subscription status via controller/route
        $response = $this->post(route('admin.consultants.update', $consultora->id), [
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
            'estado_suscripcion' => 'morosa',
            'color_primary' => '#1e40af',
            'color_secondary' => '#6b7280',
            'color_accent' => '#16a34a',
        ]);

        $response->assertRedirect(route('admin.consultants.index'));

        // Verify log was created
        $this->assertDatabaseHas('consultora_suscripcion_logs', [
            'consultora_id' => $consultora->id,
            'estado_anterior' => 'activa',
            'estado_nuevo' => 'morosa',
            'user_id' => $superAdmin->id,
        ]);
    }

    /** @test */
    public function consultants_edit_returns_formatted_suscripcion_logs_with_duration()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'estado_suscripcion' => 'activa',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        // Manipulate dates manually for testing durations
        $log1 = ConsultoraSuscripcionLog::first();
        $log1->update(['created_at' => now()->subDays(10)]);

        // Create a transition to morosa 5 days ago
        ConsultoraSuscripcionLog::create([
            'consultora_id' => $consultora->id,
            'estado_anterior' => 'activa',
            'estado_nuevo' => 'morosa',
            'created_at' => now()->subDays(5),
            'user_id' => null,
        ]);

        $superAdmin = User::create([
            'nombres' => 'Super',
            'apellidos' => 'Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'rol' => 'super_admin',
            'estado' => 'activo',
            'consultora_id' => null,
        ]);

        $this->actingAs($superAdmin);

        $response = $this->get(route('admin.consultants.edit', $consultora->id));
        $response->assertStatus(200);

        // Get props from Inertia response
        $props = $response->original->getData()['page']['props'];

        $this->assertArrayHasKey('suscripcion_logs', $props);
        $logs = $props['suscripcion_logs'];

        $this->assertCount(2, $logs);
        // The first log in desc order is the latest (morosa, created 5 days ago)
        $this->assertEquals('morosa', $logs[0]['estado_nuevo']);
        $this->assertEquals('5 days', $logs[0]['duration']);
        $this->assertTrue($logs[0]['is_current']);

        // The second log (initial, created 10 days ago, lasted until 5 days ago = 5 days)
        $this->assertEquals('activa', $logs[1]['estado_nuevo']);
        $this->assertEquals('5 days', $logs[1]['duration']);
        $this->assertFalse($logs[1]['is_current']);
    }
}
