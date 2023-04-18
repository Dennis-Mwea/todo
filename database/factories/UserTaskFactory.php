<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserTask>
 */
class UserTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTime;

        return [
            'user_id' => User::all()->random()->id,
            'task_id' => Task::all()->random()->id,
            'due_date' => $this->faker->dateTime,
            'start_date' => $start,
            'end_date' => $this->faker->dateTimeBetween($start),
            'remarks' => $this->faker->sentences(asText: true),
            'status_id' => Status::all()->random()->id,
        ];
    }
}
