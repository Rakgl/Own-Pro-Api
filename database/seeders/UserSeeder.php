<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        // Fetch roles
        $adminRole = Role::where('name', 'Admin')->first();
        $developerRole = Role::where('name', 'Developer')->first();

        if (!$adminRole || !$developerRole) {
            if (!$adminRole) {
                $this->command->error('Role "Admin" not found.');
            }
            if (!$developerRole) {
                $this->command->error('Role "Developer" not found.');
            }

            return;
        }

        // Define system users
        $systemUsers = [
            [
                'username' => 'admin',
                'name' => 'Admin',
                'password' => bcrypt('admin@123'),
                'role_id' => $adminRole->id,
            ],
            [
                'username' => 'developer',
                'name' => 'Developer',
                'password' => bcrypt('dev@123'),
                'role_id' => $developerRole->id,
            ],
        ];

        // Insert or update system users
        foreach ($systemUsers as $userData) {
            $user = User::updateOrCreate(
                ['username' => $userData['username']],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $userData['name'],
                    'email' => '', // Add appropriate email if needed
                    'password' => $userData['password'],
                    'role_id' => $userData['role_id'],
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );


        }

        $this->command->info('System users and their ID tags seeded successfully.');

    }
}
