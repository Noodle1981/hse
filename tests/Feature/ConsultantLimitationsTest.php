<?php

namespace Tests\Feature;

use App\Models\Consultora;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConsultantLimitationsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function cannot_exceed_max_tecnicos_when_saving_active_user()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'max_tecnicos' => 1,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        $gestor = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Uno',
            'email' => 'gestor1@test.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'consultora_id' => $consultora->id,
        ]);

        // Create 1 active technician (within limit of 1)
        User::create([
            'nombres' => 'Tecnico',
            'apellidos' => 'Uno',
            'email' => 'tecnico1@test.com',
            'password' => bcrypt('password'),
            'rol' => 'tecnico',
            'estado' => 'activo',
            'matricula' => 'M1',
            'telefono' => '1111',
            'consultora_id' => $consultora->id,
        ]);

        $this->actingAs($gestor);

        // Try to create a second active technician (exceeds limit)
        $response = $this->post(route('gestor.users.save'), [
            'nombres' => 'Tecnico',
            'apellidos' => 'Dos',
            'email' => 'tecnico2@test.com',
            'password' => 'password',
            'rol' => 'tecnico',
            'estado' => 'activo',
            'matricula' => 'M2',
            'telefono' => '2222',
        ]);

        $response->assertSessionHasErrors('rol');
        $this->assertDatabaseMissing('users', ['email' => 'tecnico2@test.com']);
    }

    #[Test]
    public function cannot_exceed_max_gestores_when_saving_active_user()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'max_tecnicos' => 2,
            'max_gestores' => 1,
            'max_empresas' => 5,
        ]);

        // Already has 1 active gestor
        $gestor = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Uno',
            'email' => 'gestor1@test.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'consultora_id' => $consultora->id,
        ]);

        $this->actingAs($gestor);

        // Try to create a second active gestor (exceeds limit)
        $response = $this->post(route('gestor.users.save'), [
            'nombres' => 'Gestor',
            'apellidos' => 'Dos',
            'email' => 'gestor2@test.com',
            'password' => 'password',
            'rol' => 'gestor',
            'estado' => 'activo',
            'matricula' => 'M-G2',
            'telefono' => '3333',
        ]);

        $response->assertSessionHasErrors('rol');
        $this->assertDatabaseMissing('users', ['email' => 'gestor2@test.com']);
    }

    #[Test]
    public function cannot_exceed_max_empresas_when_creating_company()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 1,
        ]);

        $gestor = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Uno',
            'email' => 'gestor1@test.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'consultora_id' => $consultora->id,
        ]);

        // Create 1 company
        Empresa::create([
            'nombre' => 'Empresa Uno',
            'razon_social' => 'Empresa Uno S.A.',
            'cuit' => '30123456781',
            'consultora_id' => $consultora->id,
        ]);

        $this->actingAs($gestor);

        // Try to create a second company
        $response = $this->post(route('gestor.companies.save'), [
            'nombre' => 'Empresa Dos',
            'razon_social' => 'Empresa Dos S.A.',
            'cuit' => '30123456782',
        ]);

        $response->assertSessionHasErrors('nombre');
        $this->assertDatabaseMissing('empresas', ['nombre' => 'Empresa Dos']);
    }

    #[Test]
    public function super_admin_cannot_exceed_user_limits_for_consultant()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'max_tecnicos' => 1,
            'max_gestores' => 1,
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

        // One active technician
        User::create([
            'nombres' => 'Tecnico',
            'apellidos' => 'Uno',
            'email' => 'tecnico1@test.com',
            'password' => bcrypt('password'),
            'rol' => 'tecnico',
            'estado' => 'activo',
            'consultora_id' => $consultora->id,
        ]);

        $this->actingAs($superAdmin);

        // Try to create a second active technician via super admin route
        $response = $this->post(route('admin.consultants.users.save', $consultora->id), [
            'nombres' => 'Tecnico',
            'apellidos' => 'Dos',
            'email' => 'tecnico2@test.com',
            'password' => 'password',
            'rol' => 'tecnico',
            'estado' => 'activo',
        ]);

        $response->assertSessionHasErrors('rol');
        $this->assertDatabaseMissing('users', ['email' => 'tecnico2@test.com']);
    }

    #[Test]
    public function cannot_restore_user_if_it_exceeds_limits()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'max_tecnicos' => 1,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        $gestor = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Uno',
            'email' => 'gestor1@test.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'consultora_id' => $consultora->id,
        ]);

        // Active technician (uses the only slot)
        User::create([
            'nombres' => 'Tecnico',
            'apellidos' => 'Activo',
            'email' => 'tecnico_activo@test.com',
            'password' => bcrypt('password'),
            'rol' => 'tecnico',
            'estado' => 'activo',
            'consultora_id' => $consultora->id,
        ]);

        // Soft-deleted technician
        $deletedUser = User::create([
            'nombres' => 'Tecnico',
            'apellidos' => 'Borrado',
            'email' => 'tecnico_borrado@test.com',
            'password' => bcrypt('password'),
            'rol' => 'tecnico',
            'estado' => 'inactivo',
            'consultora_id' => $consultora->id,
        ]);
        $deletedUser->delete();

        $this->actingAs($gestor);

        // Attempt to restore user
        $response = $this->post(route('gestor.users.restore', $deletedUser->id));
        $response->assertSessionHas('error');
        $this->assertTrue(User::onlyTrashed()->where('id', $deletedUser->id)->exists());
    }

    #[Test]
    public function cannot_restore_company_if_it_exceeds_limits()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 1,
        ]);

        $gestor = User::create([
            'nombres' => 'Gestor',
            'apellidos' => 'Uno',
            'email' => 'gestor1@test.com',
            'password' => bcrypt('password'),
            'rol' => 'administrador',
            'estado' => 'activo',
            'consultora_id' => $consultora->id,
        ]);

        // One active company
        Empresa::create([
            'nombre' => 'Empresa Activa',
            'razon_social' => 'Empresa Activa S.A.',
            'cuit' => '30123456781',
            'consultora_id' => $consultora->id,
        ]);

        // One deleted company
        $deletedCompany = Empresa::create([
            'nombre' => 'Empresa Borrada',
            'razon_social' => 'Empresa Borrada S.A.',
            'cuit' => '30123456782',
            'consultora_id' => $consultora->id,
        ]);
        $deletedCompany->delete();

        $this->actingAs($gestor);

        // Attempt to restore
        $response = $this->post(route('gestor.companies.restore', $deletedCompany->id));
        $response->assertSessionHas('error');
        $this->assertTrue(Empresa::onlyTrashed()->where('id', $deletedCompany->id)->exists());
    }
}
