<?php

namespace Tests\Feature;

use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Models\VisitCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gestor_cannot_access_other_consultora_company_sites_or_visits(): void
    {
        $consultoraA = Consultora::create([
            'nombre' => 'Consultora A',
            'cuit' => '20-12345678-6',
            'email' => 'a@consultora.test',
            'estado_suscripcion' => 'activa',
        ]);

        $consultoraB = Consultora::create([
            'nombre' => 'Consultora B',
            'cuit' => '27-12345678-0',
            'email' => 'b@consultora.test',
            'estado_suscripcion' => 'activa',
        ]);

        $gestorA = User::factory()->create([
            'rol' => 'gestor',
            'consultora_id' => $consultoraA->id,
            'estado' => 'activo',
        ]);

        $empresaB = Empresa::create([
            'nombre' => 'Empresa B',
            'razon_social' => 'Empresa B S.A.',
            'cuit' => '30-12345678-1',
            'consultora_id' => $consultoraB->id,
            'activo' => true,
        ]);

        $sedeB = Sede::create([
            'empresa_id' => $empresaB->id,
            'nombre_sede' => 'Sede B',
            'direccion' => 'Calle Falsa 123',
        ]);

        $gestorB = User::factory()->create([
            'rol' => 'gestor',
            'consultora_id' => $consultoraB->id,
            'estado' => 'activo',
        ]);

        $visitB = VisitCertificate::create([
            'consultora_id' => $consultoraB->id,
            'sede_id' => $sedeB->id,
            'user_id' => $gestorB->id,
            'visit_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $this->actingAs($gestorA);

        $this->getJson("/gestor/api/companies/{$empresaB->id}/sites")
            ->assertStatus(403);

        $this->get(route('gestor.operations.visits.show', $visitB->id))
            ->assertStatus(403);
    }
}
