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

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin.test@example.com',
            'password' => 'lu753951',
            'department_id' => 1,
            'position' => 'Developer',
            'access_profile_id' => 1,
            'is_active' => true,
        ]);

        $this->call([
             //DepartmentSeeder::class,
             //AccessProfileSeeder::class,
            //$this->call(SalasSeeder::class)
             //DepartmentSeeder::class,
             //AccessProfileSeeder::class,
             //SalasSeeder::class,
             //ReservationsSeeder::class,
             FeriadosNacionaisSeeder::class,
        ]);
    }
}
