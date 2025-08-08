<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        $roles = [
            'super_admin',
            'admin',
            'coordinador',
            'docente',
            'lider_investigacion',
            'director',
            'evaluador',
            'estudiante'
        ];

        foreach ($roles as $roleName) {
            Role::create([
                'name' => $roleName,
                'description' => 'Rol de ' . ucfirst(str_replace('_', ' ', $roleName))
            ]);
        }

        // Permisos para Roles
        $rolePermissions = [
            'view_roles',
            'list_roles',
            'create_role',
            'edit_role',
            'delete_role',
            'assign_roles'
        ];

        // Permisos para Permisos
        $permissionPermissions = [
            'view_permissions',
            'list_permissions',
            'create_permission',
            'edit_permission',
            'delete_permission',
            'assign_permissions'
        ];

        // Permisos para Usuarios
        $userPermissions = [
            'view_users',
            'list_users',
            'create_user',
            'edit_user',
            'delete_user'
        ];

        $assignRoles = [
            'assign_roles'
        ];

        // Permisos para Banco de ideas
        $bancoPermissions = [
            'view_banco_ideas',
            'list_banco_ideas',
        ];

        // Permisos para Banco de ideas
        $bancoDocentePermissions = [
            'view_propuestas_banco',
            'list_propuestas_banco',
            'create_propuesta_banco',
            'details_propuesta_banco',
        ];

        // Permisos para Banco de ideas
        $bancoAdminPermissions = [
            'reply_propuesta_banco',
            'delete_banco_ideas',
        ];

        // Permisos para iniciar proyecto
        $proyectosPermissions = [
            'view_proyectos_grado',
            'list_proyectos_grado',
            'details_proyecto_grado'
        ];

        // Permisos para modulo historico
        $historicoPermissions = [
            'view_historico',
            'list_historico'
        ];

        $proyectosEstudiantePermissions = [
            'create_proyecto_grado'
        ];

        $proyectosAdminPermissions = [
            'reply_proyecto_grado'
        ];

        $allPermissions = array_merge($rolePermissions, $permissionPermissions, $userPermissions, $bancoPermissions, $bancoDocentePermissions, $bancoAdminPermissions, $proyectosPermissions, $proyectosAdminPermissions, $proyectosEstudiantePermissions, $historicoPermissions);
        foreach ($allPermissions as $permissionName) {
            Permission::create([
                'name' => $permissionName,
                'description' => 'Permiso para ' . str_replace('_', ' ', $permissionName)
            ]);
        }

        $adminPermissions = array_merge($assignRoles, $userPermissions, $bancoPermissions, $bancoDocentePermissions, $bancoAdminPermissions, $proyectosPermissions, $proyectosAdminPermissions, $historicoPermissions);
        $liderPermissions = array_merge($bancoPermissions, $bancoDocentePermissions, $bancoAdminPermissions, $proyectosPermissions, $proyectosAdminPermissions);
        $docentePermissions = array_merge($bancoPermissions, $bancoDocentePermissions);
        $directorPermissions = array_merge($proyectosPermissions);
        $evaluadorPermissions = array_merge($proyectosPermissions);
        $estudiantePermissions = array_merge($bancoPermissions, $proyectosPermissions, $proyectosEstudiantePermissions);

        // Asignar los permisos al rol
        $superAdminRole = Role::findByName('super_admin');
        $superAdminRole->syncPermissions($allPermissions);

        $adminRole = Role::findByName('admin');
        $adminRole->syncPermissions($adminPermissions);

        $coordinadorRole = Role::findByName('coordinador');
        $coordinadorRole->syncPermissions($adminPermissions);

        $liderRole = Role::findByName('lider_investigacion');
        $liderRole->syncPermissions($liderPermissions);

        $docenteRole = Role::findByName('docente');
        $docenteRole->syncPermissions($docentePermissions);

        $directorRole = Role::findByName('director');
        $directorRole->syncPermissions($directorPermissions);

        $evaluadorRole = Role::findByName('evaluador');
        $evaluadorRole->syncPermissions($evaluadorPermissions);

        $estudianteRole = Role::findByName('estudiante');
        $estudianteRole->syncPermissions($estudiantePermissions);
    }
}
