<?php

namespace Database\Factories;

use App\Models\{Comment, Issue, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'issue_id' => Issue::factory(),
            'user_id'  => User::factory(),
            'body'     => $this->faker->sentence(),
        ];
    }
}
