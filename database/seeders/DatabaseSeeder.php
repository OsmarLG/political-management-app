<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Users
        $user1 = User::create([
            'name' => 'Master',
            'email' => 'master@surcode.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Use a secure password in production.
            'created_at' => now(),
            'updated_at' => now(),
            'username' => 'master',
            'apellido_paterno' => 'S',
            'apellido_materno' => 'C',
            'status' => 'ACTIVO',
        ]);

        $user2 = User::create([
            'name' => 'Osmar Alejandro',
            'email' => 'osmarlg@surcode.com',
            'password' => Hash::make('password'), // Use a secure password in production.
            'created_at' => now(),
            'updated_at' => now(),
            'username' => 'osmarlg',
            'apellido_paterno' => 'Liera',
            'apellido_materno' => 'Gómez',
            'status' => 'ACTIVO',
        ]);

        // Roles
        $role1 = Role::create(['name' => 'MASTER', 'guard_name' => 'web']);
        $role2 = Role::create(['name' => 'ADMIN', 'guard_name' => 'web']);
        $role3 = Role::create(['name' => 'C DISTRITAL', 'guard_name' => 'web']);
        $role4 = Role::create(['name' => 'C ENLACE DE MANZANA', 'guard_name' => 'web']);
        $role5 = Role::create(['name' => 'MANZANAL', 'guard_name' => 'web']);

        // Assign roles to users
        $user1->assignRole($role1);
        $user2->assignRole($role2);

        // Permissions
        $permissionNames = [
            'All', 'Users', 'Crear Usuario', 'Editar Usuario', 'Eliminar Usuario', 
            'Crear Zona', 'Editar Zona', 'Eliminar Zona', 'Crear Seccion', 'Editar Seccion', 
            'Eliminar Seccion', 'Crear Manzana', 'Editar Manzana', 'Eliminar Manzana', 'Zonas', 
            'Secciones', 'Manzanas', 'Asignar Geografias', 'Asignar Geografias a Zonas', 
            'Asignar Geografias a Secciones', 'Asignar Geografias a Manzanas', 'Preguntas', 
            'Crear Pregunta', 'Editar Pregunta', 'Eliminar Pregunta', 'Encuestas', 'Crear Encuesta', 
            'Editar Encuesta', 'Eliminar Encuesta', 'Bardas', 'Crear Bardas', 'Editar Bardas', 
            'Eliminar Bardas'
        ];

        $permissions = [];
        foreach ($permissionNames as $permissionName) {
            $permissions[] = Permission::create([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }

        // Assign all permissions to the MASTER role
        $role1->givePermissionTo($permissions);

        // Assign some permissions to the ADMIN role
        $adminPermissions = ['Users', 'Crear Usuario', 'Editar Usuario'];
        foreach ($adminPermissions as $adminPermission) {
            $role2->givePermissionTo($adminPermission);
        }
        
    }
}
