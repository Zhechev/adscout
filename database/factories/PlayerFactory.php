<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => function () {
                return \App\Models\Team::inRandomOrder()->first()->id;
            },
            'name' => $this->faker->name,
            'position' => $this->faker->randomElement(['Goalkeeper', 'Defender', 'Midfielder', 'Forward']),
            'age' => $this->faker->numberBetween(18, 40),
            'nationality' => $this->faker->country,
            'goals_season' => $this->faker->numberBetween(0, 50)
        ];
    }
}
