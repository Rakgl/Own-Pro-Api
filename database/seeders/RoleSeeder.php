<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon; // Import Carbon for consistent timestamps

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now(); // Get current timestamp once for consistency

        $roles = [
            [
                'id' => Str::uuid()->toString(),
                'name' => "Super Admin",
                'description' => "Full Control over the entire system.",
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => "Developer",
                'description' => "Full development access and system control.",
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => "Admin",
                'description' => "Administrative control over most system features.",
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // // Generate the remaining 97 roles
        // for ($i = 1; $i <= 97; $i++) {
        //     $roles[] = [
        //         'id' => Str::uuid()->toString(),
        //         'name' => "User Role " . $i, // Differentiating from admin roles
        //         'description' => "Standard access for User Role " . $i,
        //         'status' => 'ACTIVE',
        //         'created_at' => $now,
        //         'updated_at' => $now,
        //     ];
        // }

        // Insert in chunks to avoid potential issues with very large arrays
        foreach (array_chunk($roles, 50) as $chunk) {
            DB::table('roles')->insert($chunk);
        }

        $this->command->info('100 Roles (including 3 admin roles) seeded successfully.');
    }
}