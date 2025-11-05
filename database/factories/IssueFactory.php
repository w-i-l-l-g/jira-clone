<?php

namespace Database\Factories;

use App\Models\{Issue, Project, Column, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        return [
            'project_id'  => Project::factory(),
            'column_id'   => Column::factory(), // nullable in schema, override to null if you want
            'reporter_id' => User::factory(),
            'assignee_id' => $this->faker->boolean(50) ? User::factory() : null,
            'key'         => strtoupper($this->faker->unique()->bothify('PRJ-###')),
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'priority'    => $this->faker->randomElement(['low','medium','high','urgent']),
            'estimate'    => $this->faker->optional()->numberBetween(1, 13),
        ];
    }
}