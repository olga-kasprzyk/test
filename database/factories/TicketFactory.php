<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'subject' => $this->faker->sentences(1, true),
            'content' => $this->faker->sentences(4, true),
            'author' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
