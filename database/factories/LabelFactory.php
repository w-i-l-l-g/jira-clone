<?php

namespace Database\Factories;

use App\Models\{Label, Organization};
use Illuminate\Database\Eloquent\Factories\Factory;

class LabelFactory extends Factory
{
    protected $model = Label::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name'            => ucfirst($this->faker->unique()->word()),
            'color'           => $this->faker->hexColor(),
        ];
    }
}