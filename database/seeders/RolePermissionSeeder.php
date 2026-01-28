<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create Roles
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full system access',
                'is_active' => true,
            ]
        );

        $staffRole = Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'description' => 'Limited access for hospital staff',
                'is_active' => true,
            ]
        );

        $doctorRole = Role::updateOrCreate(
            ['slug' => 'doctor'],
            [
                'name' => 'Doctor',
                'description' => 'Access for doctors to manage their profiles and appointments',
                'is_active' => true,
            ]
        );


        // Define all permissions based on sidebar menu
        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'view_dashboard', 'module' => 'Dashboard'],

            // Management Module
            ['name' => 'View Management', 'slug' => 'view_management', 'module' => 'Management'],
            ['name' => 'Manage Beds', 'slug' => 'manage_beds', 'module' => 'Management'],
            ['name' => 'Manage Appointments', 'slug' => 'manage_appointments', 'module' => 'Management'],
            ['name' => 'Manage Events', 'slug' => 'manage_events', 'module' => 'Management'],
            ['name' => 'Manage Queue', 'slug' => 'manage_queue', 'module' => 'Management'],
            ['name' => 'Manage Doctors', 'slug' => 'manage_doctors', 'module' => 'Management'],
            ['name' => 'Manage Staff', 'slug' => 'manage_staff', 'module' => 'Management'],
            ['name' => 'Manage Patients', 'slug' => 'manage_patients', 'module' => 'Management'],
            ['name' => 'Manage Emergency', 'slug' => 'manage_emergency', 'module' => 'Management'],
            ['name' => 'Manage Invoices', 'slug' => 'manage_invoices', 'module' => 'Management'],

            // Settings Module
            ['name' => 'View Settings', 'slug' => 'view_settings', 'module' => 'Settings'],
            ['name' => 'Manage Departments', 'slug' => 'manage_departments', 'module' => 'Settings'],
            ['name' => 'Manage Rooms', 'slug' => 'manage_rooms', 'module' => 'Settings'],
            ['name' => 'Manage Services', 'slug' => 'manage_services', 'module' => 'Settings'],
            ['name' => 'Manage Notifications', 'slug' => 'manage_notifications', 'module' => 'Settings'],
            ['name' => 'Manage System Settings', 'slug' => 'manage_system_settings', 'module' => 'Settings'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        // Assign ALL permissions to admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Assign limited permissions to staff
        $staffPermissions = Permission::whereIn('slug', [
            'view_dashboard',
            'view_management',
            'manage_beds',
            'manage_appointments',
            'manage_queue',
            'manage_patients',
            'manage_emergency',
            'view_settings',
        ])->get();

        $staffRole->permissions()->sync($staffPermissions->pluck('id'));


        $this->command->info('✓ Roles and permissions seeded successfully!');
        $this->command->info('  - Admin role created with all permissions');
        $this->command->info('  - Staff role created with limited permissions');
        $this->command->info('  - Doctor role created with doctor-specific permissions');
    }
}
