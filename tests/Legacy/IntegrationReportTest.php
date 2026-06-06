<?php

namespace Tests\Feature;

use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\Empresa;
use App\Models\Hallazgo;
use App\Models\MasterReport;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    /** @test */
    public function can_download_inspection_pdf(): void
    {
        $checklist = DynamicChecklist::factory()->create();
        $inspection = DynamicInspection::create([
            'dynamic_checklist_id' => $checklist->id,
            'user_id' => $this->admin->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('gestor.inspections.pdf.download', $inspection->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function can_download_hallazgo_pdf(): void
    {
        $empresa = Empresa::factory()->create();
        $sede = Sede::factory()->create(['empresa_id' => $empresa->id]);

        $hallazgo = Hallazgo::create([
            'empresa_id' => $empresa->id,
            'sede_id' => $sede->id,
            'user_id' => $this->admin->id,
            'titulo' => 'Hallazgo Test',
            'descripcion' => 'Descripción del hallazgo',
            'criticidad' => 'medio',
            'estado' => 'pendiente',
            'fecha_deteccion' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('gestor.hallazgos.pdf', $hallazgo->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function can_download_annual_contract_pdf(): void
    {
        $empresa = Empresa::factory()->create();
        $sede = Sede::factory()->create(['empresa_id' => $empresa->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('gestor.annual-contract.pdf', ['sede_id' => $sede->id]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function can_preview_master_report_pdf(): void
    {
        $empresa = Empresa::factory()->create();
        $sede = Sede::factory()->create(['empresa_id' => $empresa->id]);

        $report = MasterReport::create([
            'sede_id' => $sede->id,
            'user_id' => $this->admin->id,
            'titulo' => 'Informe Maestro Mensual',
            'periodo' => '2026-01',
            'estado' => 'en_redaccion',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('gestor.master-reports.pdf', $report->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
