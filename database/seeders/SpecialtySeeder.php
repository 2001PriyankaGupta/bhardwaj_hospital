<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Cardiology',
                'description' => 'Heart and cardiovascular system',
                'is_active' => true,
            ],
            [
                'name' => 'Dermatology',
                'description' => 'Skin, hair, and nails',
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Medicine',
                'description' => 'Emergency medical care',
                'is_active' => true,
            ],
            [
                'name' => 'Endocrinology',
                'description' => 'Hormones and endocrine glands',
                'is_active' => true,
            ],
            [
                'name' => 'Gastroenterology',
                'description' => 'Digestive system',
                'is_active' => true,
            ],
            [
                'name' => 'General Medicine',
                'description' => 'General medical practice',
                'is_active' => true,
            ],
            [
                'name' => 'Gynecology',
                'description' => 'Female reproductive system',
                'is_active' => true,
            ],
            [
                'name' => 'Hematology',
                'description' => 'Blood and blood-forming organs',
                'is_active' => true,
            ],
            [
                'name' => 'Neurology',
                'description' => 'Nervous system',
                'is_active' => true,
            ],
            [
                'name' => 'Obstetrics',
                'description' => 'Pregnancy and childbirth',
                'is_active' => true,
            ],
            [
                'name' => 'Oncology',
                'description' => 'Cancer treatment',
                'is_active' => true,
            ],
            [
                'name' => 'Ophthalmology',
                'description' => 'Eye and vision',
                'is_active' => true,
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Bones, joints, and muscles',
                'is_active' => true,
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Child healthcare',
                'is_active' => true,
            ],
            [
                'name' => 'Psychiatry',
                'description' => 'Mental health',
                'is_active' => true,
            ],
            [
                'name' => 'Radiology',
                'description' => 'Medical imaging',
                'is_active' => true,
            ],
            [
                'name' => 'Surgery',
                'description' => 'Surgical procedures',
                'is_active' => true,
            ],
            [
                'name' => 'Urology',
                'description' => 'Urinary system',
                'is_active' => true,
            ],
        ];

        foreach ($specialties as $specialty) {
            Specialty::updateOrCreate(
                ['name' => $specialty['name']],
                $specialty
            );
        }
    }
}