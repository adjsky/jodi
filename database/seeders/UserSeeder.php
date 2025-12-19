<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
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
        $categories = collect(['Work', 'Shopping', 'Health', 'Diary']);

        User::factory()
            ->has(Category::factory($categories->count())->sequence(
                ...$categories->map(fn ($name) => ['name' => $name])->all()
            ))
            ->has(Todo::factory(5)->sequence(
                ['title' => 'Prepare merch files'],
                ['title' => 'Marketing sync'],
                ['title' => 'Sprint tasks to Sarah'],
                ['title' => 'Follow up'],
                ['title' => '16:00 Language session'],
            ))
            ->afterCreating(function (User $user) {
                $user->todos->each(function ($todo) use ($user) {
                    $todo->update([
                        'category_id' => $user->categories->random()->id,
                    ]);
                });
            })
            ->create([
                'email' => 'kirill.t@tuta.io',
                'name' => 'Kirill T.',
            ]);
    }
}
