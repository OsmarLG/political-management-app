<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $master = User::factory()->create([
            'name' => 'Master',
            'email' => 'master@surcode.com',
            'username' => 'master',
            'password' => bcrypt('password'),
            'apellido_paterno' => '',
            'apellido_materno' => '',
        ]);

        $role = Role::create(['name' => 'MASTER']);
        $master->assignRole($role);
    }
}
