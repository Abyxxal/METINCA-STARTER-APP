<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders
        $this->call([
            DepartmentAndPositionSeeder::class,
            EmployeeSeeder::class,
            EmployeeCompetencySeeder::class,
        ]);

        // Create Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'profile_photo_url' => 'https://ui-avatars.com/api/?name=Admin+User&background=0d6efd&color=ffffff'
        ]);

        // Create Regular User
        User::factory()->create([
            'name' => 'User Karyawan',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'profile_photo_url' => 'https://ui-avatars.com/api/?name=User+Karyawan&background=198754&color=ffffff'
        ]);
    }
}
