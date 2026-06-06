<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'genero' => fake()->randomElement(['M', 'F']),
            'telefono' => fake()->optional()->phoneNumber(),
            'n_doc' => fake()->optional()->numerify('########'),
            'rol' => fake()->randomElement(['Administrador', 'Técnico']),
            'matricula' => fake()->numerify('MAT-####'),
            'estado' => true,
            'firma' => 'data:image/png;base64,'.fake()->regexify('[A-Za-z0-9]{20}'),
            'consultora_id' => \App\Models\Consultora::factory(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * User as admin
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'Administrador',
        ]);
    }

    /**
     * User as inspector
     */
    public function inspector(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'Técnico',
        ]);
    }
}
