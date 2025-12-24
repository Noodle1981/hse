<?php

namespace Database\Factories;

use App\Models\DynamicAnswer;
use App\Models\DynamicInspection;
use App\Models\DynamicQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DynamicAnswer>
 */
class DynamicAnswerFactory extends Factory
{
    protected $model = DynamicAnswer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'dynamic_inspection_id' => DynamicInspection::factory(),
            'dynamic_question_id' => DynamicQuestion::factory(),
            'value' => fake()->sentence(),
        ];
    }

    /**
     * Respuesta para pregunta de tipo específico
     */
    public function forQuestion(DynamicQuestion $question): static
    {
        return $this->state(fn (array $attributes) => [
            'dynamic_question_id' => $question->id,
            'value' => $this->generateValueForType($question),
        ]);
    }

    /**
     * Genera un valor apropiado según el tipo de pregunta
     */
    private function generateValueForType(DynamicQuestion $question): string
    {
        return match ($question->type) {
            'text' => fake()->words(3, true),
            'textarea' => fake()->paragraph(),
            'number' => (string) fake()->numberBetween(1, 100),
            'date' => fake()->date('Y-m-d'),
            'select', 'radio' => $question->options ? fake()->randomElement($question->options) : 'N/A',
            'checkbox' => $question->options ? json_encode(fake()->randomElements($question->options, rand(1, count($question->options)))) : '[]',
            'signature' => 'data:image/png;base64,' . fake()->regexify('[A-Za-z0-9]{50}'),
            default => fake()->word(),
        };
    }
}
