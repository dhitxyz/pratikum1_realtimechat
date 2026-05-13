<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Andi',
            'email' => 'andi@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Rina',
            'email' => 'rina@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
