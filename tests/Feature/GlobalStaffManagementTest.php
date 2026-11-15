<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Consultora;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GlobalStaffManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function super_admin_can_manage_global_staff()
    {
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

        // 1. Create a Vendedor
        $response = $this->post(route('admin.staff.save'), [
            'nombres' => 'Juan',
            'apellidos' => 'Vendedor',
            'email' => 'juan@vendedor.com',
            'password' => 'password123',
            'rol' => 'vendedor',
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'juan@vendedor.com',
            'rol' => 'vendedor',
            'consultora_id' => null,
        ]);

        $vendedor = User::where('email', 'juan@vendedor.com')->first();

        // 2. Edit the Vendedor
        $response = $this->post(route('admin.staff.save'), [
            'id' => $vendedor->id,
            'nombres' => 'Juan Modificado',
            'apellidos' => 'Vendedor',
            'email' => 'juan@vendedor.com',
            'rol' => 'vendedor',
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $this->assertDatabaseHas('users', [
            'id' => $vendedor->id,
            'nombres' => 'Juan Modificado',
        ]);

        // 3. Deactivate/Delete the Vendedor
        $response = $this->delete(route('admin.staff.destroy', $vendedor->id));
        $response->assertRedirect(route('admin.staff.index'));

        // No se elimina lógicamente (no soft delete), sino que se desactiva
        $this->assertDatabaseHas('users', [
            'id' => $vendedor->id,
            'estado' => 'inactivo',
            'deleted_at' => null,
        ]);
    }

    #[Test]
    public function vendedor_cannot_access_global_staff_crud()
    {
        $vendedor = User::create([
            'nombres' => 'Juan',
            'apellidos' => 'Vendedor',
            'email' => 'juan@vendedor.com',
            'password' => bcrypt('password'),
            'rol' => 'vendedor',
            'estado' => 'activo',
            'consultora_id' => null,
        ]);

        $this->actingAs($vendedor);

        // Index
        $response = $this->get(route('admin.staff.index'));
        $response->assertStatus(403);

        // Save
        $response = $this->post(route('admin.staff.save'), [
            'nombres' => 'Test',
            'apellidos' => 'Staff',
            'email' => 'test@staff.com',
            'password' => 'password123',
            'rol' => 'super_admin',
            'estado' => 'activo',
        ]);
        $response->assertStatus(403);
    }

    #[Test]
    public function vendedor_cannot_delete_consultoras()
    {
        $consultora = Consultora::create([
            'nombre' => 'Test Consultora',
            'cuit' => '20123456781',
            'estado_suscripcion' => 'activa',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        $vendedor = User::create([
            'nombres' => 'Juan',
            'apellidos' => 'Vendedor',
            'email' => 'juan@vendedor.com',
            'password' => bcrypt('password'),
            'rol' => 'vendedor',
            'estado' => 'activo',
            'consultora_id' => null,
        ]);

        $this->actingAs($vendedor);

        $response = $this->delete(route('admin.consultants.destroy', $consultora->id));
        $response->assertStatus(403);
        $this->assertDatabaseHas('consultoras', ['id' => $consultora->id]);
    }

    #[Test]
    public function audit_logs_record_creations_and_updates_for_consultant()
    {
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

        // 1. Create Consultora
        $consultora = Consultora::create([
            'nombre' => 'Audit Consultora',
            'cuit' => '20123456789',
            'estado_suscripcion' => 'activa',
            'max_tecnicos' => 5,
            'max_gestores' => 2,
            'max_empresas' => 5,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create',
            'target_type' => Consultora::class,
            'target_id' => $consultora->id,
            'user_id' => $superAdmin->id,
        ]);

        $createLog = AuditLog::where('target_id', $consultora->id)->where('action', 'create')->first();
        $this->assertNotNull($createLog->payload);
        $this->assertEquals('activa', $createLog->payload['estado_suscripcion']['new']);
        $this->assertEquals(5, $createLog->payload['max_tecnicos']['new']);

        // 2. Update Consultora
        $consultora->update([
            'max_tecnicos' => 15,
            'estado_suscripcion' => 'morosa',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'update',
            'target_type' => Consultora::class,
            'target_id' => $consultora->id,
            'user_id' => $superAdmin->id,
        ]);

        $updateLog = AuditLog::where('target_id', $consultora->id)->where('action', 'update')->first();
        $this->assertNotNull($updateLog->payload);
        $this->assertEquals(5, $updateLog->payload['max_tecnicos']['old']);
        $this->assertEquals(15, $updateLog->payload['max_tecnicos']['new']);
        $this->assertEquals('activa', $updateLog->payload['estado_suscripcion']['old']);
        $this->assertEquals('morosa', $updateLog->payload['estado_suscripcion']['new']);
    }

    #[Test]
    public function audit_logs_are_immutable()
    {
        $log = AuditLog::create([
            'action' => 'create',
            'target_type' => Consultora::class,
            'target_id' => 999,
            'user_id' => null,
            'payload' => [],
        ]);

        $this->expectException(\Exception::class);
        $log->update(['action' => 'update']);

        $this->expectException(\Exception::class);
        $log->delete();
    }

    #[Test]
    public function vendedor_and_super_admin_redirect_to_admin_dashboard()
    {
        // 1. Vendedor
        $vendedor = User::create([
            'nombres' => 'Juan',
            'apellidos' => 'Vendedor',
            'email' => 'juan@vendedor.com',
            'password' => bcrypt('password'),
            'rol' => 'vendedor',
            'estado' => 'activo',
            'consultora_id' => null,
        ]);

        $response = $this->actingAs($vendedor)->get(route('dashboard'));
        $response->assertRedirect(route('admin.dashboard'));

        // 2. Super Admin
        $superAdmin = User::create([
            'nombres' => 'Super',
            'apellidos' => 'Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'rol' => 'super_admin',
            'estado' => 'activo',
            'consultora_id' => null,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));
        $response->assertRedirect(route('admin.dashboard'));
    }
}
