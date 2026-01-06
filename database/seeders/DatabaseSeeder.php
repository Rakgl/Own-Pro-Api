<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PaymentMethodSeeder::class,
            LocaleSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            RolePermissionSeeder::class,
            FAQSeeder::class,
            AnnouncementSeeder::class,
            StaticContentSeeder::class,
            NotificationSeeder::class,
            AppVersionSeeder::class
            ]);
    }
}
