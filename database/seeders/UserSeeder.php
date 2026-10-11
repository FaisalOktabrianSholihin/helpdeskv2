<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleId = fn (string $nama) => Role::where('nama', $nama)->value('id');

        $users = [
            ['name' => 'Super Administrator', 'email' => 'superadmin@mitratani27.co.id', 'role' => 'Superadmin', 'divisi_id' => null],
            ['name' => 'Muhammad Andre Sahrindra', 'email' => 'andre@mitratani27.co.id', 'role' => 'Admin IT', 'divisi_id' => 15], // Teknologi Informasi (db_indukk)
            ['name' => 'Admin HRGA', 'email' => 'admin.hrga@mitratani27.co.id', 'role' => 'Admin HRGA', 'divisi_id' => null],
            ['name' => 'Agent Helpdesk', 'email' => 'agent@mitratani27.co.id', 'role' => 'Agent', 'divisi_id' => 15],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $roleId($u['role']),
                    'divisi_id' => $u['divisi_id'],
                    'active' => true,
                ]
            );
        }
    }
}
