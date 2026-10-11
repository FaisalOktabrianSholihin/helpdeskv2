<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $all = PermissionCatalog::allKeys();

        $roles = [
            ['nama' => 'Superadmin', 'permissions' => $all],
            ['nama' => 'Admin IT', 'permissions' => [
                'dashboard.view', 'tiket.view', 'tiket.create', 'tiket.edit', 'tiket.reply', 'tiket.close', 'tiket.print',
                'divisi.view', 'karyawan.view', 'analitik.view', 'users.view', 'users.create', 'users.edit',
            ]],
            ['nama' => 'Admin HRGA', 'permissions' => [
                'dashboard.view', 'tiket.view', 'tiket.create', 'tiket.edit', 'tiket.reply', 'tiket.print', 'analitik.view',
            ]],
            ['nama' => 'Agent', 'permissions' => [
                'dashboard.view', 'tiket.view', 'tiket.reply', 'tiket.close', 'tiket.print', 'analitik.view',
            ]],
            ['nama' => 'User', 'permissions' => [
                'dashboard.view', 'tiket.view', 'tiket.create', 'tiket.reply',
            ]],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['nama' => $role['nama']], ['permissions' => $role['permissions']]);
        }
    }
}
