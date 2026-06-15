<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Consultora;
use App\Models\User;

class SubscriptionStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_with_active_subscription_can_access_routes()
    {
        $consultora = Consultora::create([
            'nombre' => 'Consultora Activa',
            'cuit' => '20123456781',
            'estado_suscripcion' => 'activa',
        ]);

        $user = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Activo',
            'email' => 'gestor@activa.com',
            'password' => bcrypt('password'),
            'rol' => 'gestor',
            'consultora_id' => $consultora->id,
        ]);

        $this->actingAs($user);

        // Should load dashboard or redirect normally (not 403)
        $response = $this->get(route('dashboard'));
        $response->assertStatus(302); // redirects to gestor.dashboard
    }

    /** @test */
    public function a_user_with_morosa_subscription_is_blocked()
    {
        $consultora = Consultora::create([
            'nombre' => 'Consultora Morosa',
            'cuit' => '20123456782',
            'estado_suscripcion' => 'morosa',
        ]);

        $user = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Moroso',
            'email' => 'gestor@morosa.com',
            'password' => bcrypt('password'),
            'rol' => 'gestor',
            'consultora_id' => $consultora->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertForbidden();
        $response->assertSee('El servicio de su consultora ha sido suspendido temporalmente por falta de pago.');
    }

    /** @test */
    public function a_user_with_baja_subscription_is_blocked()
    {
        $consultora = Consultora::create([
            'nombre' => 'Consultora Baja',
            'cuit' => '20123456783',
            'estado_suscripcion' => 'baja',
        ]);

        $user = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Baja',
            'email' => 'gestor@baja.com',
            'password' => bcrypt('password'),
            'rol' => 'gestor',
            'consultora_id' => $consultora->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertForbidden();
        $response->assertSee('El servicio de su consultora ha sido dado de baja.');
    }

    /** @test */
    public function a_super_admin_is_never_blocked_by_subscription_checks()
    {
        $superAdmin = User::create([
            'nombres' => 'Super',
            'apellidos' => 'Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'rol' => 'super_admin',
            'consultora_id' => null,
        ]);

        $this->actingAs($superAdmin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertOk();
    }
}
