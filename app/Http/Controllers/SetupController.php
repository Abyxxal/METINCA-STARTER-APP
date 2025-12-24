<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Seeders\DepartmentAndPositionSeeder;
use Database\Seeders\EmployeeSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class SetupController extends Controller
{
    /**
     * Show setup page
     */
    public function show()
    {
        return view('setup');
    }

    /**
     * Store user ke database
     */
    public function storeUser(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=' . urlencode($request->name) . '&background=0d6efd&color=ffffff',
                'email_verified_at' => now(),
            ]);

            return back()->with('success', 'User created successfully! Email: ' . $request->email . ' | Password: ' . $request->password);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Run all seeders
     */
    public function seed()
    {
        try {
            // Run seeders manually
            $departmentSeeder = new DepartmentAndPositionSeeder();
            $departmentSeeder->run();

            $employeeSeeder = new EmployeeSeeder();
            $employeeSeeder->run();

            // Create test users
            User::truncate();

            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=Admin+User&background=0d6efd&color=ffffff',
                'email_verified_at' => now(),
            ]);

            User::create([
                'name' => 'User Karyawan',
                'email' => 'user@example.com',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'profile_photo_url' => 'https://ui-avatars.com/api/?name=User+Karyawan&background=198754&color=ffffff',
                'email_verified_at' => now(),
            ]);

            return back()->with('success', 'All seeders executed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
}
