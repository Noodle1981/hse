<?php

namespace Tests\Feature;

use App\Models\Consultora;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSwitchTest extends TestCase
{
    use RefreshDatabase;

    protected $gestor;

    protected function setUp(): void
    {
        parent::setUp();

        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456789',
        ]);

        $this->gestor = User::create([
            'nombres' => 'Admin',
            'apellidos' => 'User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'rol' => 'gestor',
            'consultora_id' => $consultora->id,
            'telefono' => '12345678',
            'matricula' => 'M123',
        ]);
    }

    /** @test */
    public function a_gestor_can_switch_to_tecnico_mode()
    {
        $this->actingAs($this->gestor);

        $response = $this->get(route('switch-role', 'tecnico'));

        $response->assertRedirect(route('tecnico.dashboard'));
        $this->assertEquals('tecnico', session('active_role'));
        $this->assertTrue(auth()->user()->isTechnician());
        $this->assertFalse(auth()->user()->isGestor());
    }

    /** @test */
    public function a_gestor_can_switch_back_to_gestor_mode()
    {
        $this->actingAs($this->gestor);
        session(['active_role' => 'tecnico']);

        $response = $this->get(route('switch-role', 'gestor'));

        $response->assertRedirect(route('gestor.dashboard'));
        $this->assertNull(session('active_role'));
        $this->assertTrue(auth()->user()->isGestor());
        $this->assertFalse(auth()->user()->isTechnician());
    }

    /** @test */
    public function a_tecnico_cannot_switch_roles()
    {
        $tecnico = User::create([
            'nombres' => 'Tecnico',
            'apellidos' => 'User',
            'email' => 'tecnico@test.com',
            'password' => bcrypt('password'),
            'rol' => 'tecnico',
            'consultora_id' => $this->gestor->consultora_id,
            'telefono' => '87654321',
            'matricula' => 'T123',
        ]);

        $this->actingAs($tecnico);

        $response = $this->get(route('switch-role', 'tecnico'));
        $response->assertForbidden();
    }

    /** @test */
    public function a_gestor_in_tecnico_mode_can_access_tecnico_routes()
    {
        $this->actingAs($this->gestor);

        // 1. En modo Gestor (default) no debería entrar a rutas de técnico
        // (Nota: Depende de cómo esté configurado el middleware, pero probemos el cambio)

        // 2. Activar modo Técnico
        $this->get(route('switch-role', 'tecnico'));

        // 3. Intentar acceder a una ruta de técnico
        $response = $this->get(route('tecnico.dashboard'));
        $response->assertOk();
    }

    /** @test */
    public function a_gestor_in_tecnico_mode_cannot_access_gestor_routes()
    {
        $this->actingAs($this->gestor);

        // 1. Activar modo Técnico
        $this->get(route('switch-role', 'tecnico'));

        // 2. Intentar acceder a una ruta de gestor (que use el middleware role:Administrador)
        $response = $this->get(route('gestor.dashboard'));
        $response->assertForbidden();
    }
}
