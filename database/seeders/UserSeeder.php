<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->has(Todo::factory(5)->sequence(
                ['title' => 'Prepare merch files', 'category' => 'Work'],
                ['title' => 'Marketing sync', 'category' => 'Work'],
                ['title' => 'Sprint tasks to Sarah', 'category' => 'Work'],
                ['title' => 'Follow up', 'category' => 'Diary'],
                ['title' => '16:00 Language session', 'category' => 'Diary'],
            ))
            ->create([
                'email' => 'kirill.t@tuta.io',
                'name' => 'Kirill T.',
            ]);
    }
}
