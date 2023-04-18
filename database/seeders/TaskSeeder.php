<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Task::factory()->count(20)->has(UserTask::factory()->state(fn(array $attrs, Task $task) => [
                'user_id' => $user->id,
                'due_date' => $task->due_date,
                'status_id' => $task->status_id,
            ]), 'task')->create();
        });
    }
}
