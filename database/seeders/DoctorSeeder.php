<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $doctorRole = Role::where('slug', 'doctor')->first();
            if (!$doctorRole) {
                $this->command->error('Doctor role not found. Please run RolePermissionSeeder first.');
                return;
            }

            $doctors = [
                [
                    'first_name' => 'Rajesh',
                    'last_name' => 'Kumar',
                    'email' => 'rajesh.kumar@hospital.com',
                    'specialty_name' => 'Cardiology',
                    'phone' => '+91 900000100',
                    'gender' => 'male',
                ],
                [
                    'first_name' => 'Priya',
                    'last_name' => 'Sharma',
                    'email' => 'priya.sharma@hospital.com',
                    'specialty_name' => 'Dermatology',
                    'phone' => '+91 900000101',
                    'gender' => 'female',
                ],
                [
                    'first_name' => 'Amit',
                    'last_name' => 'Verma',
                    'email' => 'amit.verma@hospital.com',
                    'specialty_name' => 'Emergency Medicine',
                    'phone' => '+91 900000102',
                    'gender' => 'male',
                ],
                [
                    'first_name' => 'Sunita',
                    'last_name' => 'Patel',
                    'email' => 'sunita.patel@hospital.com',
                    'specialty_name' => 'Gynecology',
                    'phone' => '+91 900000103',
                    'gender' => 'female',
                ],
                [
                    'first_name' => 'Rahul',
                    'last_name' => 'Mehta',
                    'email' => 'rahul.mehta@hospital.com',
                    'specialty_name' => 'Neurology',
                    'phone' => '+91 900000104',
                    'gender' => 'male',
                ],
                [
                    'first_name' => 'Anjali',
                    'last_name' => 'Gupta',
                    'email' => 'anjali.gupta@hospital.com',
                    'specialty_name' => 'Pediatrics',
                    'phone' => '+91 900000105',
                    'gender' => 'female',
                ],
                [
                    'first_name' => 'Vikram',
                    'last_name' => 'Singh',
                    'email' => 'vikram.singh@hospital.com',
                    'specialty_name' => 'Orthopedics',
                    'phone' => '+91 900000106',
                    'gender' => 'male',
                ],
                [
                    'first_name' => 'Kavita',
                    'last_name' => 'Joshi',
                    'email' => 'kavita.joshi@hospital.com',
                    'specialty_name' => 'Ophthalmology',
                    'phone' => '+91 900000107',
                    'gender' => 'female',
                ],
            ];

            foreach ($doctors as $d) {
                $name = $d['first_name'] . ' ' . $d['last_name'];

                // Get specialty
                $specialty = Specialty::where('name', $d['specialty_name'])->first();
                if (!$specialty) {
                    $this->command->warn("Specialty '{$d['specialty_name']}' not found for doctor {$name}. Skipping.");
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $d['email']],
                    [
                        'name' => $name,
                        'email' => $d['email'],
                        'password' => Hash::make('password'),
                        'user_type' => 'doctor',
                        'status' => 'active',
                        'role_id' => $doctorRole->id,
                        'phone' => $d['phone'],
                        'gender' => $d['gender'],
                        'is_verified' => true,
                    ]
                );

                $doctor = Doctor::updateOrCreate(
                    ['email' => $d['email']],
                    [
                        'first_name' => $d['first_name'],
                        'last_name' => $d['last_name'],
                        'email' => $d['email'],
                        'phone' => $d['phone'],
                        'status' => 'active',
                        'consultation_fee' => rand(500, 2000),
                        'is_verified' => true,
                        'specialty_id' => $specialty->id,
                        'license_number' => 'LIC' . rand(100000, 999999),
                        'password' => Hash::make('password'),
                        'qualifications' => 'MBBS, MD',
                        'experience' => rand(5, 20) . ' years',
                        'working_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
                        'shift_start_time' => '09:00:00',
                        'shift_end_time' => '17:00:00',
                        'average_consultation_time' => '00:15:00',
                    ]
                );

                // Sync back references
                if (Schema::hasColumn('users', 'doctor_id')) {
                    $user->doctor_id = $doctor->id;
                    $user->save();
                }

                $doctor->user_id = $user->id;
                $doctor->save();
            }

            $this->command->info('Doctors seeded: ' . Doctor::count());
        });
    }
}
