<?php

namespace Database\Seeders;

// Removed unused model imports to keep the seeder clean
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Safety: avoid accidental seeding in production without confirmation
        if ($this->command && app()->environment('production')) {
            if (! $this->command->confirm('You are in production. Do you really wish to run the database seeders?')) {
                $this->command->info('Seeding aborted.');
                return;
            }
        }

        // Ordered list of seeders (order may matter for FK dependencies)
        $seeders = [
            SystemSettingsSeeder::class,
            RolePermissionSeeder::class,
            SpecialtySeeder::class,
            DepartmentSeeder::class,
            DoctorSeeder::class,
            StaffSeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
            BedSeeder::class,
            NotificationTemplatesSeeder::class,
            EventsTableSeeder::class,
            UsersSeeder::class,
        ];

        // Disable foreign key constraints during seeding and ensure they're re-enabled
        Schema::disableForeignKeyConstraints();
        try {
            foreach ($seeders as $seeder) {
                if ($this->command) {
                    $this->command->getOutput()->writeln("<info>Seeding:</info> {$seeder}");
                }

                $this->call($seeder);
            }

            // Example: only create demo data in non-production environments
            if (! app()->environment('production')) {
                // Example factory usage (uncomment if needed):
                // \App\Models\User::factory()->count(5)->create();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        // Optional: DB driver-specific cleanup (e.g., sequences for PostgreSQL)
        if (DB::getDriverName() === 'pgsql') {
            // DB::statement('SELECT setval(...)');
        }
    }
}
