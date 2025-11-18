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
            ->has(Todo::factory(7))
            ->create([
                'email' => 'kirill.t@tuta.io',
                'name' => 'Kirill T.',
            ]);
    }
}
