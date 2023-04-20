<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        collect([
            'Pending', 'In Progress', 'In Review', 'Done',
        ])->each(fn (string $status) => Status::updateOrCreate(['name' => $status]));
    }
}
