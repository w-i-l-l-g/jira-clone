<?php

namespace Database\Factories;

use App\Models\{Board, Project};
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    protected $model = Board::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name'       => $this->faker->randomElement(['Board','Main Board','Kanban']),
        ];
    }
}