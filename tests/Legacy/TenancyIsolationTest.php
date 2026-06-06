<?php

namespace Tests\Feature;

use App\Livewire\Companies\CompanyIndex;
use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TenancyIsolationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_only_see_companies_from_their_own_consultora()
    {
        // 1. Create two separate consultoras
        $consultoraA = Consultora::factory()->create(['nombre' => 'Consultora Alpha']);
        $consultoraB = Consultora::factory()->create(['nombre' => 'Consultora Beta']);

        // 2. Create users for each consultora
        $adminA = User::factory()->admin()->create(['consultora_id' => $consultoraA->id]);
        $adminB = User::factory()->admin()->create(['consultora_id' => $consultoraB->id]);

        // 3. Create companies for each consultora
        $empresaA1 = Empresa::factory()->create(['consultora_id' => $consultoraA->id, 'nombre' => 'Empresa A1']);
        $empresaA2 = Empresa::factory()->create(['consultora_id' => $consultoraA->id, 'nombre' => 'Empresa A2']);
        $empresaB1 = Empresa::factory()->create(['consultora_id' => $consultoraB->id, 'nombre' => 'Empresa B1']);

        // 4. Act as Admin A and verify they only see their own companies in the index
        Livewire::actingAs($adminA)
            ->test(CompanyIndex::class)
            ->assertSee('Empresa A1')
            ->assertSee('Empresa A2')
            ->assertDontSee('Empresa B1');

        // 5. Act as Admin B and verify they only see their own companies
        Livewire::actingAs($adminB)
            ->test(CompanyIndex::class)
            ->assertSee('Empresa B1')
            ->assertDontSee('Empresa A1')
            ->assertDontSee('Empresa A2');
    }

    /** @test */
    public function admin_cannot_access_company_from_another_consultora_via_builder()
    {
        $consultoraA = Consultora::factory()->create();
        $consultoraB = Consultora::factory()->create();

        $adminA = User::factory()->admin()->create(['consultora_id' => $consultoraA->id]);
        $empresaB = Empresa::factory()->create(['consultora_id' => $consultoraB->id]);

        // Assuming CompanyBuilder handles editing by ID via Livewire component property or mount
        // Let's verify how CompanyBuilder works. If it uses route binding or internal query.

        Livewire::actingAs($adminA)
            ->test(\App\Livewire\Companies\CompanyBuilder::class, ['id' => $empresaB->id])
            ->assertStatus(404);
    }

    /** @test */
    public function admin_cannot_access_user_from_another_consultora()
    {
        $consultoraA = Consultora::factory()->create();
        $consultoraB = Consultora::factory()->create();

        $adminA = User::factory()->admin()->create(['consultora_id' => $consultoraA->id]);
        $userB = User::factory()->inspector()->create(['consultora_id' => $consultoraB->id]);

        Livewire::actingAs($adminA)
            ->test(\App\Livewire\Users\UserManager::class)
            ->call('edit', $userB->id)
            ->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_locations_from_another_consultora_company()
    {
        $consultoraA = Consultora::factory()->create();
        $consultoraB = Consultora::factory()->create();

        $adminA = User::factory()->admin()->create(['consultora_id' => $consultoraA->id]);
        $empresaB = Empresa::factory()->create(['consultora_id' => $consultoraB->id]);

        Livewire::actingAs($adminA)
            ->test(\App\Livewire\Companies\LocationManager::class, ['empresa' => $empresaB])
            ->assertStatus(403);
    }
}
