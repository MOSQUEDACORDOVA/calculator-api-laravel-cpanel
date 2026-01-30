<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $this->call([
            CurrencySeeder::class,
        ]);

        // User::factory(10)->create();

            User::updateOrCreate(
                ['email' => 'tester@example.com'],
                [
                    'name' => 'API Tester',
                    'password' => Hash::make('password123'),
                ]
            );
    }
}
