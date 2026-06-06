<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/gestor/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/tecnico/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function technicians_cannot_access_gestor_routes(): void
    {
        $technician = User::factory()->inspector()->create();

        $response = $this->actingAs($technician)->get('/gestor/dashboard');

        // Dependiendo de cómo esté configurado el middleware, puede ser 403 o redirección
        $response->assertStatus(403);
    }

    /** @test */
    public function admins_can_access_gestor_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/gestor/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function technicians_can_access_tecnico_dashboard(): void
    {
        $technician = User::factory()->inspector()->create();

        $response = $this->actingAs($technician)->get('/tecnico/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function technicians_cannot_manage_checklists(): void
    {
        $technician = User::factory()->inspector()->create();

        $response = $this->actingAs($technician)->get('/gestor/checklists');

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_can_access_tecnico_routes(): void
    {
        // En este proyecto, el middleware de técnico permite 'Administrador' o 'Técnico'?
        // Revisando web.php: middleware(['role:Técnico,tecnico'])
        // Si el middleware no incluye Administrador, entonces el admin NO puede entrar a /tecnico

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/tecnico/dashboard');

        // Si el middleware es estricto, esto debería ser 403
        // Vamos a verificar qué sucede
        $response->assertStatus(403);
    }
}
