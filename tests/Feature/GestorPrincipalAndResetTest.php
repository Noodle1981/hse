<?php

namespace Tests\Feature;

use App\Models\Consultora;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GestorPrincipalAndResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_primary_gestor_for_consultora()
    {
        $superAdmin = User::factory()->create([
            'rol' => User::ROLE_SUPER_ADMIN,
        ]);

        $consultant = Consultora::create([
            'nombre' => 'Consultora Test',
            'cuit' => '30-11111111-9',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
            'created_by_user_id' => $superAdmin->id,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('admin.consultants.primary-gestor.save', $consultant->id), [
            'nombres' => 'Carlos',
            'apellidos' => 'Gestor',
            'email' => 'gestor.principal@test.com',
            'telefono' => '12345678',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('generated_password');

        $primaryGestor = User::where('email', 'gestor.principal@test.com')->first();
        $this->assertNotNull($primaryGestor);
        $this->assertTrue($primaryGestor->is_primary);
        $this->assertTrue($primaryGestor->must_change_password);
        $this->assertEquals(User::ROLE_GESTOR, $primaryGestor->rol);
        $this->assertEquals($consultant->id, $primaryGestor->consultora_id);
    }

    public function test_user_with_must_change_password_is_redirected_to_change_password()
    {
        $user = User::factory()->create([
            'rol' => User::ROLE_GESTOR,
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->get(route('gestor.dashboard'));

        $response->assertRedirect(route('password.change'));
    }

    public function test_user_can_update_password_and_clear_must_change_flag()
    {
        $user = User::factory()->create([
            'rol' => User::ROLE_GESTOR,
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->post(route('password.change.update'), [
            'password' => 'Nuev4ClaveSegura!',
            'password_confirmation' => 'Nuev4ClaveSegura!',
        ]);

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('Nuev4ClaveSegura!', $user->password));
    }

    public function test_technician_reset_request_notifies_primary_gestor()
    {
        $consultant = Consultora::create([
            'nombre' => 'Consultora B',
            'cuit' => '30-22222222-9',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        $primaryGestor = User::factory()->create([
            'consultora_id' => $consultant->id,
            'rol' => User::ROLE_GESTOR,
            'is_primary' => true,
            'estado' => 'activo',
        ]);

        $technician = User::factory()->create([
            'consultora_id' => $consultant->id,
            'email' => 'tecnico.perdidocli@test.com',
            'rol' => User::ROLE_TECNICO,
            'estado' => 'activo',
        ]);

        $response = $this->post(route('password.reset-request.store'), [
            'email' => 'tecnico.perdidocli@test.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $technician->id,
            'status' => 'pending',
        ]);

        $resetReq = PasswordResetRequest::where('user_id', $technician->id)->first();
        $this->assertContains($primaryGestor->id, $resetReq->notified_user_ids);
    }
}
