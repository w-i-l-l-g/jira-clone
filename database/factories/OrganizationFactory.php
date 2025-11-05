<?php

namespace Database\Factories;

use App\Models\{Organization, User};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();
        return [
            'owner_id' => User::factory(),
            'name'     => $name,
            'slug'     => Str::slug($name) . '-' . Str::lower(Str::random(4)),
        ];
    }
}