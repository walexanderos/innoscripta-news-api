<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content' => $this->faker->text,
            'author' => $this->faker->name,
            'source' => $this->faker->company,
            'category' => $this->faker->word,
            'published_at' => $this->faker->dateTime,
        ];
    }
}
