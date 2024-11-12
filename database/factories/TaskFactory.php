<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(15), // Generate a string less than 20 characters
            'description' => $this->faker->text(25), // Still meets the <30 character limit
            'type' => $this->faker->randomElement(['Bug', 'Feature', 'Improvement']),
            'status' => $this->faker->randomElement(['Open', 'In Progress', 'Completed', 'Blocked']),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High']), // Strings only
            'due_date' => $this->faker->date(),
            'assigned_to' => null,
        ];
    }

}
