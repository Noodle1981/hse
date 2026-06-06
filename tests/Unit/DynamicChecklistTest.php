<?php

namespace Tests\Unit;

use App\Models\DynamicChecklist;
use App\Models\DynamicInspection;
use App\Models\DynamicQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicChecklistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_checklist(): void
    {
        $checklist = DynamicChecklist::factory()->create([
            'title' => 'Control de EPP',
            'category' => 'hys',
        ]);

        $this->assertDatabaseHas('dynamic_checklists', [
            'title' => 'Control de EPP',
            'category' => 'hys',
        ]);
    }

    /** @test */
    public function it_has_many_questions(): void
    {
        $checklist = DynamicChecklist::factory()->create();

        DynamicQuestion::factory()->count(5)->create([
            'dynamic_checklist_id' => $checklist->id,
        ]);

        $this->assertCount(5, $checklist->questions);
    }

    /** @test */
    public function it_has_many_inspections(): void
    {
        $checklist = DynamicChecklist::factory()->create();
        $user = User::factory()->create();

        DynamicInspection::factory()->count(3)->create([
            'dynamic_checklist_id' => $checklist->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $checklist->inspections);
    }

    /** @test */
    public function scope_active_returns_only_active_checklists(): void
    {
        DynamicChecklist::factory()->active()->count(3)->create();
        DynamicChecklist::factory()->inactive()->count(2)->create();

        $activeChecklists = DynamicChecklist::active()->get();

        $this->assertCount(3, $activeChecklists);
    }

    /** @test */
    public function scope_category_filters_by_category(): void
    {
        DynamicChecklist::factory()->category('hys')->count(2)->create();
        DynamicChecklist::factory()->category('calidad')->count(3)->create();

        $hysChecklists = DynamicChecklist::byCategory('hys')->get();
        $calidadChecklists = DynamicChecklist::byCategory('calidad')->get();

        $this->assertCount(2, $hysChecklists);
        $this->assertCount(3, $calidadChecklists);
    }

    /** @test */
    public function questions_are_ordered_by_order_field(): void
    {
        $checklist = DynamicChecklist::factory()->create();

        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $checklist->id,
            'label' => 'Pregunta 3',
            'order' => 2,
        ]);
        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $checklist->id,
            'label' => 'Pregunta 1',
            'order' => 0,
        ]);
        DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $checklist->id,
            'label' => 'Pregunta 2',
            'order' => 1,
        ]);

        $questions = $checklist->questions;

        $this->assertEquals('Pregunta 1', $questions[0]->label);
        $this->assertEquals('Pregunta 2', $questions[1]->label);
        $this->assertEquals('Pregunta 3', $questions[2]->label);
    }

    /** @test */
    public function question_options_are_cast_to_array(): void
    {
        $checklist = DynamicChecklist::factory()->create();

        $question = DynamicQuestion::factory()->create([
            'dynamic_checklist_id' => $checklist->id,
            'type' => 'radio',
            'options' => ['Sí', 'No', 'N/A'],
        ]);

        $this->assertIsArray($question->options);
        $this->assertContains('Sí', $question->options);
    }

    /** @test */
    public function inspection_can_be_marked_as_completed(): void
    {
        $user = User::factory()->create();
        $checklist = DynamicChecklist::factory()->create();

        $inspection = DynamicInspection::factory()->draft()->create([
            'dynamic_checklist_id' => $checklist->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals('draft', $inspection->status);
        $this->assertNull($inspection->completed_at);

        $inspection->markAsCompleted();

        $this->assertEquals('completed', $inspection->status);
        $this->assertNotNull($inspection->completed_at);
    }

    /** @test */
    public function it_deletes_questions_when_checklist_is_deleted(): void
    {
        $checklist = DynamicChecklist::factory()->create();

        DynamicQuestion::factory()->count(3)->create([
            'dynamic_checklist_id' => $checklist->id,
        ]);

        $this->assertCount(3, DynamicQuestion::all());

        $checklist->delete();

        $this->assertCount(0, DynamicQuestion::all());
    }
}
