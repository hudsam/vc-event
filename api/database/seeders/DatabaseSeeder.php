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
        // User::factory(10)->create();

        if (!User::where('email', 'huda@maxy.academy')->exists()) {
            User::create([
                'name' => 'huda',
                'email' => 'huda@maxy.academy',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
        }

        if (!User::where('email', 'smith@maxy.academy')->exists()) {
            User::create([
                'name' => 'smith',
                'email' => 'smith@maxy.academy',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]);
        }
    }
}
