<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Department;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $staffRole = Role::where('slug', 'staff')->first();
            if (!$staffRole) {
                $this->command->error('Staff role not found. Please run RolePermissionSeeder first.');
                return;
            }

            $staffMembers = [
                [
                    'name' => 'John Carter',
                    'email' => 'john.carter@hospital.com',
                    'position' => 'Receptionist',
                    'department_name' => 'Hospital Administration',
                    'phone' => '+91 900000200',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Maya Singh',
                    'email' => 'maya.singh@hospital.com',
                    'position' => 'Nurse',
                    'department_name' => 'Emergency Medicine',
                    'phone' => '+91 900000201',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Alex Brown',
                    'email' => 'alex.brown@hospital.com',
                    'position' => 'Lab Technician',
                    'department_name' => 'Human Resources',
                    'phone' => '+91 900000202',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@hospital.com',
                    'position' => 'Pharmacist',
                    'department_name' => 'Hospital Administration',
                    'phone' => '+91 900000203',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Michael Davis',
                    'email' => 'michael.davis@hospital.com',
                    'position' => 'Medical Assistant',
                    'department_name' => 'Emergency Medicine',
                    'phone' => '+91 900000204',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Emma Wilson',
                    'email' => 'emma.wilson@hospital.com',
                    'position' => 'Administrative Assistant',
                    'department_name' => 'Human Resources',
                    'phone' => '+91 900000205',
                    'gender' => 'female',
                ],
            ];

            foreach ($staffMembers as $s) {
                // Get department
                $department = Department::where('name', $s['department_name'])->first();
                if (!$department) {
                    $this->command->warn("Department '{$s['department_name']}' not found for staff {$s['name']}. Using first available department.");
                    $department = Department::first();
                    if (!$department) {
                        $this->command->error('No departments found. Please run DepartmentSeeder first.');
                        return;
                    }
                }

                $user = User::updateOrCreate(
                    ['email' => $s['email']],
                    [
                        'name' => $s['name'],
                        'email' => $s['email'],
                        'password' => Hash::make('password'),
                        'user_type' => 'staff',
                        'status' => 'active',
                        'role_id' => $staffRole->id,
                        'phone' => $s['phone'],
                        'gender' => $s['gender'],
                        'is_verified' => true,
                    ]
                );

                $staff = Staff::updateOrCreate(
                    ['email' => $s['email']],
                    [
                        'name' => $s['name'],
                        'email' => $s['email'],
                        'phone' => $s['phone'],
                        'position' => $s['position'],
                        'status' => 'active',
                        'department_id' => $department->id,
                        'joining_date' => now()->subYears(rand(0, 5))->format('Y-m-d'),
                        'password' => Hash::make('password'),
                        'address' => 'Sample address for ' . $s['name'],
                    ]
                );

                // Sync back references
                if (Schema::hasColumn('users', 'staff_id')) {
                    $user->staff_id = $staff->id;
                    $user->save();
                }

                $staff->user_id = $user->id;
                $staff->save();
            }

            $this->command->info('Staff seeded: ' . Staff::count());
        });
    }
}
