<?php

namespace Tests\Feature;

use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use App\Models\DynamicInspection;
use App\Models\DynamicAnswer;
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

        $this->user = User::factory()->create();
        $this->checklist = DynamicChecklist::factory()->create([
            'title' => 'Test Checklist',
        ]);

        // Agregar preguntas
        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $this->checklist->id,
            'label' => '¿Nombre del inspector?',
            'type' => 'text',
            'is_required' => true,
            'order' => 0,
        ]);

        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $this->checklist->id,
            'label' => '¿Cumple con la norma?',
            'type' => 'radio',
            'options' => ['Sí', 'No', 'N/A'],
            'is_required' => true,
            'order' => 1,
        ]);

        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $this->checklist->id,
            'label' => 'Observaciones',
            'type' => 'textarea',
            'is_required' => false,
            'order' => 2,
        ]);
    }

    /** @test */
    public function filler_page_can_be_rendered(): void
    {
        $response = $this->get("/checklists/{$this->checklist->id}/fill");

        $response->assertStatus(200);
        $response->assertSee($this->checklist->title);
        $response->assertSee('¿Nombre del inspector?');
    }

    /** @test */
    public function can_complete_a_checklist(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test('checklists.checklist-filler', ['id' => $this->checklist->id])
            ->set("answers.{$questions[0]->id}", 'Juan Pérez')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->set("answers.{$questions[2]->id}", 'Todo en orden')
            ->set('notes', 'Inspección sin novedades')
            ->call('save')
            ->assertRedirect(route('checklists.index'));

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

        Livewire::test('checklists.checklist-filler', ['id' => $this->checklist->id])
            ->set("answers.{$questions[0]->id}", '') // Requerido
            ->set("answers.{$questions[1]->id}", '') // Requerido
            ->call('save')
            ->assertHasErrors(["answers.{$questions[0]->id}", "answers.{$questions[1]->id}"]);
    }

    /** @test */
    public function non_required_fields_can_be_empty(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test('checklists.checklist-filler', ['id' => $this->checklist->id])
            ->set("answers.{$questions[0]->id}", 'Juan')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->set("answers.{$questions[2]->id}", '') // No requerido
            ->call('save')
            ->assertHasNoErrors();
    }

    /** @test */
    public function inspection_is_linked_to_user(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test('checklists.checklist-filler', ['id' => $this->checklist->id])
            ->set("answers.{$questions[0]->id}", 'Test')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->call('save');

        $inspection = DynamicInspection::first();
        
        $this->assertEquals($this->user->id, $inspection->user_id);
        $this->assertEquals($this->user->id, $inspection->user->id);
    }

    /** @test */
    public function answers_are_linked_to_questions(): void
    {
        $this->actingAs($this->user);

        $questions = $this->checklist->questions;

        Livewire::test('checklists.checklist-filler', ['id' => $this->checklist->id])
            ->set("answers.{$questions[0]->id}", 'María López')
            ->set("answers.{$questions[1]->id}", 'No')
            ->call('save');

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

        Livewire::test('checklists.checklist-filler', ['id' => $this->checklist->id])
            ->set("answers.{$questions[0]->id}", 'Test')
            ->set("answers.{$questions[1]->id}", 'Sí')
            ->set("answers.{$checkboxQuestion->id}", ['Casco', 'Guantes'])
            ->call('save');

        $answer = DynamicAnswer::where('dynamic_question_id', $checkboxQuestion->id)->first();
        
        $this->assertJson($answer->value);
        $decoded = json_decode($answer->value, true);
        $this->assertContains('Casco', $decoded);
        $this->assertContains('Guantes', $decoded);
    }
}
