<?php

namespace Tests\Feature;

use App\Livewire\Technician\InspectionWizard;
use App\Models\ChecklistSection;
use App\Models\DynamicAnswer;
use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\DynamicQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChecklistFillerTest extends TestCase
{
    use RefreshDatabase;

    private DynamicChecklist $checklist;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->inspector()->create();
        $this->checklist = DynamicChecklist::factory()->create([
            'title' => 'Test Checklist',
        ]);

        // Crear una sección ya que el Wizard la requiere para mostrar preguntas
        $section = ChecklistSection::create([
            'dynamic_checklist_id' => $this->checklist->id,
            'title' => 'Datos Generales',
            'order' => 0,
        ]);

        // Agregar preguntas vinculadas a la sección
        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $this->checklist->id,
            'section_id' => $section->id,
            'label' => '¿Nombre del inspector?',
            'type' => 'text',
            'is_required' => true,
            'order' => 0,
        ]);

        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $this->checklist->id,
            'section_id' => $section->id,
            'label' => '¿Cumple con la norma?',
            'type' => 'radio',
            'options' => ['Sí', 'No', 'N/A'],
            'is_required' => true,
            'order' => 1,
        ]);

        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $this->checklist->id,
            'section_id' => $section->id,
            'label' => 'Observaciones',
            'type' => 'textarea',
            'is_required' => false,
            'order' => 2,
        ]);
    }

    /** @test */
    public function filler_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get("/tecnico/checklists/{$this->checklist->id}/fill");

        $response->assertStatus(200);
        $response->assertSee($this->checklist->title);
        // En el Wizard (Paso 0 - Intro), se ve el botón "Empezar"
        $response->assertSee('Empezar');
    }

    /** @test */
    public function can_complete_a_checklist(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test(InspectionWizard::class, ['id' => $this->checklist->id])
            ->set('currentStep', 1) // Saltar intro a sección 1
            ->set("answers.{$questions[0]->id}", 'Juan Pérez')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->set("answers.{$questions[2]->id}", 'Todo en orden')
            ->set('currentStep', 2) // Avanzar al paso final
            ->set('supervisorName', 'Supervisor Test')
            ->set('supervisorSignature', 'data:image/png;base64,123')
            ->set('technicianSignature', 'data:image/png;base64,456')
            ->call('complete')
            ->assertRedirect(route('tecnico.inspections.show', DynamicInspection::latest()->first()->id));

        $this->assertDatabaseHas('dynamic_inspections', [
            'dynamic_checklist_id' => $this->checklist->id,
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $this->assertEquals(3, DynamicAnswer::count());
    }

    /** @test */
    public function required_fields_are_validated(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test(InspectionWizard::class, ['id' => $this->checklist->id])
            ->set('currentStep', 1)
            ->set("answers.{$questions[0]->id}", '')
            ->set("answers.{$questions[1]->id}", '')
            ->call('nextStep')
            ->assertHasErrors(["answers.{$questions[0]->id}", "answers.{$questions[1]->id}"]);
    }

    /** @test */
    public function non_required_fields_can_be_empty(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test(InspectionWizard::class, ['id' => $this->checklist->id])
            ->set('currentStep', 1)
            ->set("answers.{$questions[0]->id}", 'Juan')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->set("answers.{$questions[2]->id}", '')
            ->call('nextStep')
            ->assertHasNoErrors();
    }

    /** @test */
    public function inspection_is_linked_to_user(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test(InspectionWizard::class, ['id' => $this->checklist->id])
            ->set('currentStep', 1)
            ->set("answers.{$questions[0]->id}", 'Test')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->set('currentStep', 2)
            ->set('supervisorName', 'Test Supervisor')
            ->set('supervisorSignature', 'data:image/png;base64,123')
            ->set('technicianSignature', 'data:image/png;base64,456')
            ->call('complete');

        $inspection = DynamicInspection::first();

        $this->assertEquals($this->user->id, $inspection->user_id);
        $this->assertEquals($this->user->id, $inspection->user->id);
    }

    /** @test */
    public function answers_are_linked_to_questions(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test(InspectionWizard::class, ['id' => $this->checklist->id])
            ->set('currentStep', 1)
            ->set("answers.{$questions[0]->id}", 'María López')
            ->set("answers.{$questions[1]->id}", 'No')
            ->set('currentStep', 2)
            ->set('supervisorName', 'Test Supervisor')
            ->set('supervisorSignature', 'data:image/png;base64,123')
            ->set('technicianSignature', 'data:image/png;base64,456')
            ->call('complete');

        $answer = DynamicAnswer::where('dynamic_question_id', $questions[0]->id)->first();

        $this->assertEquals('María López', $answer->value);
        $this->assertEquals('¿Nombre del inspector?', $answer->question->label);
    }

    /** @test */
    public function checkbox_answers_are_stored_as_json(): void
    {
        $this->actingAs($this->user);

        // Agregar pregunta tipo checkbox
        $checkboxQuestion = DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $this->checklist->id,
            'label' => 'EPP utilizados',
            'type' => 'checkbox',
            'options' => ['Casco', 'Guantes', 'Lentes'],
            'is_required' => false,
            'order' => 3,
        ]);

        $questions = $this->checklist->fresh()->questions;

        Livewire::test(InspectionWizard::class, ['id' => $this->checklist->id])
            ->set('currentStep', 1)
            ->set("answers.{$questions[0]->id}", 'Test')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->set("answers.{$checkboxQuestion->id}", ['Casco', 'Guantes'])
            ->set('currentStep', 2)
            ->set('supervisorName', 'Test Supervisor')
            ->set('supervisorSignature', 'data:image/png;base64,123')
            ->set('technicianSignature', 'data:image/png;base64,456')
            ->call('complete');

        $answer = DynamicAnswer::where('dynamic_question_id', $checkboxQuestion->id)->first();

        $this->assertJson($answer->value);
        $decoded = json_decode($answer->value, true);
        $this->assertContains('Casco', $decoded);
        $this->assertContains('Guantes', $decoded);
    }
}
