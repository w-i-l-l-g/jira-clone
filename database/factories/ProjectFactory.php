<?php

namespace Database\Factories;

use App\Models\{Project, Organization};
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name'            => $this->faker->unique()->catchPhrase(),
            'key'             => strtoupper($this->faker->unique()->bothify('??##')), // <= 12 chars
            'description'     => $this->faker->optional()->sentence(),
            'issue_seq'       => 0,
        ];
    }
}