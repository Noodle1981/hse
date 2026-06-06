<?php

namespace Tests\Feature;

use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChecklistBuilderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function checklist_builder_page_can_be_rendered(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->get('/gestor/checklists/create');

        $response->assertStatus(200);
        $response->assertSee('Nuevo Checklist');
    }

    /** @test */
    public function can_create_a_checklist_with_questions(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('title', 'Control de EPP')
            ->set('description', 'Checklist para verificar equipos de protección personal')
            ->set('category', 'hys')
            ->set('questions', [
                [
                    'label' => '¿Usa casco de seguridad?',
                    'type' => 'radio',
                    'options' => ['Sí', 'No', 'N/A'],
                    'is_required' => true,
                    'help_text' => '',
                    'min_value' => null,
                    'max_value' => null,
                    'failure_values' => null,
                ],
                [
                    'label' => 'Observaciones',
                    'type' => 'textarea',
                    'options' => [],
                    'is_required' => false,
                    'help_text' => 'Ingrese cualquier observación',
                    'min_value' => null,
                    'max_value' => null,
                    'failure_values' => null,
                ],
            ])
            ->call('save')
            ->assertRedirect(route('gestor.checklists.index'));

        $this->assertDatabaseHas('dynamic_checklists', [
            'title' => 'Control de EPP',
            'category' => 'hys',
        ]);

        $this->assertEquals(2, DynamicQuestion::count());
    }

    /** @test */
    public function title_is_required(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('title', '')
            ->set('questions', [
                ['label' => 'Pregunta', 'type' => 'text', 'options' => [], 'is_required' => false, 'help_text' => '', 'min_value' => null, 'max_value' => null, 'failure_values' => null],
            ])
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    }

    /** @test */
    public function at_least_one_question_is_required(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('title', 'Test Checklist')
            ->set('questions', [])
            ->call('save')
            ->assertHasErrors(['questions']);
    }

    /** @test */
    public function can_add_a_question(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('newQuestion.label', '¿Cumple con la norma?')
            ->set('newQuestion.type', 'radio')
            ->call('addQuestion')
            ->assertSet('questions', function ($questions) {
                return count($questions) === 1 && $questions[0]['label'] === '¿Cumple con la norma?';
            });
    }

    /** @test */
    public function can_add_options_to_a_question(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('newQuestion.type', 'select')
            ->set('optionInput', 'Opción 1')
            ->call('addOption')
            ->set('optionInput', 'Opción 2')
            ->call('addOption')
            ->assertSet('newQuestion.options', ['Opción 1', 'Opción 2']);
    }

    /** @test */
    public function can_remove_an_option(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('newQuestion.options', ['A', 'B', 'C'])
            ->call('removeOption', 1)
            ->assertSet('newQuestion.options', ['A', 'C']);
    }

    /** @test */
    public function can_remove_a_question(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('questions', [
                ['label' => 'Q1', 'type' => 'text', 'options' => [], 'is_required' => false, 'help_text' => ''],
                ['label' => 'Q2', 'type' => 'text', 'options' => [], 'is_required' => false, 'help_text' => ''],
            ])
            ->call('removeQuestion', 0)
            ->assertSet('questions', function ($questions) {
                return count($questions) === 1 && $questions[0]['label'] === 'Q2';
            });
    }

    /** @test */
    public function can_edit_existing_checklist(): void
    {
        $checklist = DynamicChecklist::factory()->create([
            'title' => 'Original Title',
        ]);
        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $checklist->id,
            'label' => 'Original Question',
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())->get("/gestor/checklists/{$checklist->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Editar Checklist');
    }

    /** @test */
    public function can_reorder_questions(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test('checklists.checklist-builder')
            ->set('questions', [
                ['label' => 'Q1', 'type' => 'text', 'options' => [], 'is_required' => false, 'help_text' => ''],
                ['label' => 'Q2', 'type' => 'text', 'options' => [], 'is_required' => false, 'help_text' => ''],
            ])
            ->call('moveDown', 0)
            ->assertSet('questions.0.label', 'Q2')
            ->assertSet('questions.1.label', 'Q1');
    }
}
