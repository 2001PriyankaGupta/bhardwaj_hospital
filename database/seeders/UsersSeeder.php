<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Doctor;
use App\Models\Staff;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Ensure necessary roles exist
            $roles = [
                ['slug' => 'admin', 'name' => 'Administrator'],
                ['slug' => 'staff', 'name' => 'Staff'],
                ['slug' => 'doctor', 'name' => 'Doctor'],
                ['slug' => 'patient', 'name' => 'Patient'],
            ];

            foreach ($roles as $r) {
                Role::updateOrCreate(['slug' => $r['slug']], ['name' => $r['name'], 'is_active' => true]);
            }

            $adminRole = Role::where('slug', 'admin')->first();
            $staffRole = Role::where('slug', 'staff')->first();
            $doctorRole = Role::where('slug', 'doctor')->first();
            $patientRole = Role::where('slug', 'patient')->first();

            // Create one super-admin
            $admin = User::updateOrCreate(
                ['email' => 'admin@hospital.com'],
                [
                    'name' => 'System Admin',
                    'email' => 'admin@hospital.com',
                    'password' => Hash::make('password'),
                    'is_admin' => true,
                    'user_type' => 'admin',
                    'status' => 'active',
                    'role_id' => $adminRole->id,
                    'phone' => '+91 9000000001',
                    'gender' => 'other',
                ]
            );

            // Create staff users and staff records
            // $staffUsers = [
            //     ['name' => 'John Carter', 'email' => 'john.carter@hospital.com', 'position' => 'Receptionist'],
            //     ['name' => 'Maya Singh', 'email' => 'maya.singh@hospital.com', 'position' => 'Nurse'],
            //     ['name' => 'Alex Brown', 'email' => 'alex.brown@hospital.com', 'position' => 'Lab Technician'],
            // ];

            // foreach ($staffUsers as $s) {
            //     $user = User::updateOrCreate(
            //         ['email' => $s['email']],
            //         [
            //             'name' => $s['name'],
            //             'email' => $s['email'],
            //             'password' => Hash::make('password'),
            //             'user_type' => 'staff',
            //             'status' => 'active',
            //             'role_id' => $staffRole->id,
            //             'phone' => '+91 900000' . rand(100,999),
            //             'gender' => 'female',
            //         ]
            //     );

            //     // create or update staff profile
            //     // pick a department (if exists) for staff
            //     $departmentId = null;
            //     try {
            //         $departmentId = \App\Models\Department::inRandomOrder()->first()->id ?? null;
            //     } catch (\Throwable $e) {
            //         $departmentId = null;
            //     }

            //     $staff = Staff::updateOrCreate(
            //         ['email' => $s['email']],
            //         [
            //             'name' => $s['name'],
            //             'email' => $s['email'],
            //             'phone' => '+91 900000' . rand(100,999),
            //             'position' => $s['position'],
            //             'status' => 'active',
            //             'department_id' => $departmentId ?? 1,
            //             'joining_date' => now()->subYears(rand(0, 5))->format('Y-m-d'),
            //             'password' => Hash::make('password'),
            //         ]
            //     );

            //     // sync back reference
            //     $user->staff_id = $staff->id;
            //     $user->save();

            //     $staff->user_id = $user->id;
            //     $staff->save();
            // }

            // Create doctors and doctor records
            // $doctors = [
            //     ['first_name' => 'Rajesh', 'last_name' => 'Kumar', 'email' => 'rajesh.kumar@hospital.com'],
            //     ['first_name' => 'Priya', 'last_name' => 'Sharma', 'email' => 'priya.sharma@hospital.com'],
            //     ['first_name' => 'Amit', 'last_name' => 'Verma', 'email' => 'amit.verma@hospital.com'],
            //     ['first_name' => 'Sunita', 'last_name' => 'Patel', 'email' => 'sunita.patel@hospital.com'],
            //     ['first_name' => 'Rahul', 'last_name' => 'Mehta', 'email' => 'rahul.mehta@hospital.com'],
            // ];

            // foreach ($doctors as $d) {
            //     $name = $d['first_name'] . ' ' . $d['last_name'];
            //     $user = User::updateOrCreate(
            //         ['email' => $d['email']],
            //         [
            //             'name' => $name,
            //             'email' => $d['email'],
            //             'password' => Hash::make('password'),
            //             'user_type' => 'doctor',
            //             'status' => 'active',
            //             'role_id' => $doctorRole->id,
            //             'phone' => '+91 900000' . rand(100,999),
            //             'gender' => 'male',
            //         ]
            //     );

            //     // Create a doctor profile if missing and link
            //     // ensure there is at least one specialty
            //     $specialtyId = null;
            //     try {
            //         $specialtyId = \App\Models\Specialty::inRandomOrder()->first()->id ?? null;
            //     } catch (\Throwable $e) {
            //         $specialtyId = null;
            //     }

            //     $doctor = Doctor::firstOrCreate(
            //         ['email' => $d['email']],
            //         [
            //             'first_name' => $d['first_name'],
            //             'last_name' => $d['last_name'],
            //             'email' => $d['email'],
            //             'phone' => '+91 900000' . rand(100,999),
            //             'status' => 'active',
            //             'consultation_fee' => 1000.00,
            //             'is_verified' => true,
            //             'specialty_id' => $specialtyId ?? 1,
            //             'license_number' => 'LIC' . rand(100000, 999999),
            //             'password' => Hash::make('password'),
            //         ]
            //     );

            //     // sync back reference
            //     $user->doctor_id = $doctor->id;
            //     $user->save();

            //     $doctor->user_id = $user->id;
            //     $doctor->save();
            // }


            $this->command->info('Users seeded:');
            $this->command->info(' - Admin: ' . User::where('user_type', 'admin')->count());
            // $this->command->info(' - Staff: ' . User::where('user_type', 'staff')->count());
            // $this->command->info(' - Doctors: ' . User::where('user_type', 'doctor')->count());
            // $this->command->info(' - Patients: ' . User::where('user_type', 'patient')->count());
        });
    }
}
