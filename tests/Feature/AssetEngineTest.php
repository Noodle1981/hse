<?php

namespace Tests\Feature;

use App\Events\InspectionCompleted;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AuditLog;
use App\Models\DynamicAnswer;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\DynamicQuestion;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AssetEngineTest extends TestCase
{
    use RefreshDatabase;

    private Empresa $empresa;

    private Sede $sede;

    private AssetType $assetType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::create([
            'nombre' => 'Test Empresa HyS',
            'razon_social' => 'Test Empresa HyS S.A.',
            'cuit' => '30-12345678-9',
        ]);
        $this->sede = Sede::create([
            'empresa_id' => $this->empresa->id,
            'nombre_sede' => 'Planta Central',
        ]);

        $this->assetType = AssetType::create([
            'empresa_id' => $this->empresa->id,
            'name' => 'Matafuego',
            'description' => 'Equipamiento de extinción de incendios',
            'metadata_schema' => [
                [
                    'name' => 'vencimiento',
                    'label' => 'Fecha de Vencimiento',
                    'type' => 'date',
                    'alert_days' => 30,
                ],
                [
                    'name' => 'presion',
                    'label' => 'Presión de Carga (PSI)',
                    'type' => 'number',
                    'alert_threshold' => 150,
                ],
            ],
        ]);
    }

    public function test_can_create_asset_with_uuid_and_metadata(): void
    {
        $asset = Asset::create([
            'empresa_id' => $this->empresa->id,
            'sede_id' => $this->sede->id,
            'asset_type_id' => $this->assetType->id,
            'name' => 'Matafuego A-01',
            'identifier' => 'MF-001',
            'status' => 'operativo',
            'metadata' => [
                'vencimiento' => '2026-12-31',
                'presion' => 120,
            ],
        ]);

        $this->assertNotNull($asset->id);
        $this->assertIsString($asset->id);
        $this->assertEquals('operativo', $asset->status);
        $this->assertEquals('2026-12-31', $asset->metadata['vencimiento']);
    }

    public function test_sync_asset_metadata_and_auto_status_change_on_failed_inspection(): void
    {
        $asset = Asset::create([
            'empresa_id' => $this->empresa->id,
            'sede_id' => $this->sede->id,
            'asset_type_id' => $this->assetType->id,
            'name' => 'Matafuego B-02',
            'identifier' => 'MF-002',
            'status' => 'operativo',
            'metadata' => [
                'vencimiento' => '2026-12-31',
                'presion' => 120,
            ],
        ]);

        $checklist = DynamicChecklist::create([
            'title' => 'Control de Matafuegos',
            'is_active' => true,
        ]);

        $question = DynamicQuestion::create([
            'dynamic_checklist_id' => $checklist->id,
            'label' => 'Ingrese Presión',
            'type' => 'number',
            'vincular_a_metadata' => 'presion',
        ]);

        $user = User::factory()->create();

        $inspection = DynamicInspection::create([
            'dynamic_checklist_id' => $checklist->id,
            'user_id' => $user->id,
            'asset_id' => $asset->id,
            'sede_id' => $this->sede->id,
            'status' => 'completed',
            'result' => 'Fail',
        ]);

        DynamicAnswer::create([
            'dynamic_inspection_id' => $inspection->id,
            'dynamic_question_id' => $question->id,
            'value' => 160,
        ]);

        InspectionCompleted::dispatch($inspection);

        $asset->refresh();
        $this->assertEquals(160, $asset->metadata['presion']);
        $this->assertEquals('fuera_de_servicio', $asset->status);
    }

    public function test_asset_observer_records_audit_log(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $asset = Asset::create([
            'empresa_id' => $this->empresa->id,
            'sede_id' => $this->sede->id,
            'asset_type_id' => $this->assetType->id,
            'name' => 'Matafuego C-03',
            'identifier' => 'MF-003',
            'status' => 'operativo',
            'metadata' => [],
        ]);

        $asset->update(['name' => 'Matafuego C-03 Editado', 'status' => 'alerta']);

        $audit = AuditLog::where('target_type', Asset::class)
            ->where('target_id', (string) $asset->id)
            ->first();

        $this->assertNotNull($audit);
        $this->assertEquals('asset_updated', $audit->action);
        $this->assertEquals('Matafuego C-03 Editado', $audit->payload['dirty']['name']);
    }

    public function test_check_asset_alerts_command(): void
    {
        $user = User::factory()->create();
        $this->sede->users()->attach($user);

        // Asset near expiration (vence en 10 días <= 30 días alert_days)
        $nearExpiryDate = now()->addDays(10)->toDateString();

        $asset = Asset::create([
            'empresa_id' => $this->empresa->id,
            'sede_id' => $this->sede->id,
            'asset_type_id' => $this->assetType->id,
            'name' => 'Matafuego D-04',
            'identifier' => 'MF-004',
            'status' => 'operativo',
            'metadata' => [
                'vencimiento' => $nearExpiryDate,
                'presion' => 100,
            ],
        ]);

        Artisan::call('assets:check-alerts');

        $hallazgo = Hallazgo::where('origen_type', Asset::class)
            ->where('origen_id', $asset->id)
            ->first();

        $this->assertNotNull($hallazgo);
        $this->assertEquals($this->sede->id, $hallazgo->sede_id);
        $this->assertEquals('alto', $hallazgo->criticidad);
        $this->assertStringContainsString('vencimiento', $hallazgo->titulo);

        // Re-run to test non-duplication
        Artisan::call('assets:check-alerts');
        $this->assertEquals(1, Hallazgo::where('origen_type', Asset::class)->where('origen_id', $asset->id)->count());
    }

    public function test_gestor_asset_crud_controller(): void
    {
        $gestor = User::factory()->create(['rol' => 'Gestor']);
        $this->actingAs($gestor);

        // Test store
        $response = $this->post(route('gestor.assets.store'), [
            'empresa_id' => $this->empresa->id,
            'sede_id' => $this->sede->id,
            'asset_type_id' => $this->assetType->id,
            'name' => 'Matafuego E-05',
            'identifier' => 'MF-005',
            'status' => 'operativo',
            'metadata' => ['vencimiento' => '2027-01-01', 'presion' => '110'],
        ]);

        $asset = Asset::where('identifier', 'MF-005')->first();
        $this->assertNotNull($asset);
        $response->assertRedirect(route('gestor.assets.show', $asset->id));

        // Test index
        $indexResponse = $this->get(route('gestor.assets.index'));
        $indexResponse->assertStatus(200);

        // Test show
        $showResponse = $this->get(route('gestor.assets.show', $asset->id));
        $showResponse->assertStatus(200);

        // Test update
        $updateResponse = $this->put(route('gestor.assets.update', $asset->id), [
            'empresa_id' => $this->empresa->id,
            'sede_id' => $this->sede->id,
            'asset_type_id' => $this->assetType->id,
            'name' => 'Matafuego E-05 Renovado',
            'identifier' => 'MF-005',
            'status' => 'alerta',
            'metadata' => ['vencimiento' => '2027-01-01', 'presion' => '155'],
        ]);
        $updateResponse->assertRedirect(route('gestor.assets.show', $asset->id));
        $this->assertEquals('alerta', $asset->fresh()->status);

        // Test destroy (soft delete)
        $deleteResponse = $this->delete(route('gestor.assets.destroy', $asset->id));
        $deleteResponse->assertRedirect(route('gestor.assets.index'));
        $this->assertSoftDeleted($asset);
    }
}
