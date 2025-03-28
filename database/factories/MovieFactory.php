<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'director' => $this->faker->name,
            'release_date' => $this->faker->date(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
