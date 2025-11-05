<?php

namespace Database\Factories;

use App\Models\{Column, Board};
use Illuminate\Database\Eloquent\Factories\Factory;

class ColumnFactory extends Factory
{
    protected $model = Column::class;

    public function definition(): array
    {
        static $pos = 0;
        return [
            'board_id' => Board::factory(),
            'name'     => $this->faker->randomElement(['Backlog','In Progress','Review','Done']),
            'position' => ($pos += 100),
        ];
    }
}