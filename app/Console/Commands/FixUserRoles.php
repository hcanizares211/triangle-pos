<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class FixUserRoles extends Command
{
    protected $signature = 'fix:user-roles {--user=1 : ID del usuario a corregir}';
    protected $description = 'Asigna el rol Super Admin al usuario indicado';

    public function handle()
    {
        $userId = $this->option('user');
        $user = User::find($userId);

        if (!$user) {
            $this->error("Usuario ID {$userId} no encontrado.");
            return 1;
        }

        $this->info("Usuario: {$user->name} ({$user->email})");
        $this->info("Roles actuales: " . ($user->getRoleNames()->count() ? $user->getRoleNames()->implode(', ') : 'Ninguno'));

        // Crear rol Super Admin si no existe
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->info("Rol 'Super Admin' " . ($role->wasRecentlyCreated ? 'creado' : 'ya existía'));

        // Asignar todas las permisos al rol Super Admin
        $allPermissions = Permission::all();
        $role->syncPermissions($allPermissions);

        // Asignar rol al usuario
        $user->syncRoles(['Super Admin']);

        // Limpiar caché
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info("Rol 'Super Admin' asignado a '{$user->name}' con {$allPermissions->count()} permisos.");
        $this->info("Roles finales: " . $user->fresh()->getRoleNames()->implode(', '));

        return 0;
    }
}
