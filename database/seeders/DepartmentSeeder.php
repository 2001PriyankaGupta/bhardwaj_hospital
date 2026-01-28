<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            // Hospital Administration Departments
            [
                'name' => 'Hospital Administration',
                'code' => 'ADMIN',
                'description' => 'Overall hospital management and administration',
                'parent_id' => null,
                'head_id' => 1,
                'email' => 'admin@hospital.com',
                'phone' => '+1-555-0201',
                'display_order' => 1,
                'is_active' => true,
                'services' => json_encode(['hospital_management', 'policy_making', 'strategic_planning', 'coordination']),
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Manages hospital staff recruitment and employee relations',
                'parent_id' => 1, // Admin department
                'head_id' => 2,
                'email' => 'hr@hospital.com',
                'phone' => '+1-555-0202',
                'display_order' => 1,
                'is_active' => true,
                'services' => json_encode(['staff_recruitment', 'employee_relations', 'training', 'payroll']),
            ],

            // Medical Departments
            [
                'name' => 'Emergency Medicine',
                'code' => 'ER',
                'description' => '24/7 emergency care and trauma services',
                'parent_id' => null,
                'head_id' => 3,
                'email' => 'emergency@hospital.com',
                'phone' => '+1-555-0203',
                'display_order' => 2,
                'is_active' => true,
                'services' => json_encode(['trauma_care', 'emergency_treatment', 'critical_care', 'urgent_cases']),
            ],
            [
                'name' => 'Cardiology',
                'code' => 'CARD',
                'description' => 'Heart and cardiovascular disease treatment',
                'parent_id' => null,
                'head_id' => 4,
                'email' => 'cardiology@hospital.com',
                'phone' => '+1-555-0204',
                'display_order' => 3,
                'is_active' => true,
                'services' => json_encode(['echo_cardiogram', 'angioplasty', 'bypass_surgery', 'heart_monitoring']),
            ],
            [
                'name' => 'Orthopedics',
                'code' => 'ORTHO',
                'description' => 'Bone, joint, and muscle treatment',
                'parent_id' => null,
                'head_id' => 5,
                'email' => 'orthopedics@hospital.com',
                'phone' => '+1-555-0205',
                'display_order' => 4,
                'is_active' => true,
                'services' => json_encode(['fracture_treatment', 'joint_replacement', 'sports_medicine', 'physiotherapy']),
            ],
            [
                'name' => 'Pediatrics',
                'code' => 'PED',
                'description' => 'Medical care for children and infants',
                'parent_id' => null,
                'head_id' => 6,
                'email' => 'pediatrics@hospital.com',
                'phone' => '+1-555-0206',
                'display_order' => 5,
                'is_active' => true,
                'services' => json_encode(['child_healthcare', 'vaccinations', 'growth_monitoring', 'pediatric_surgery']),
            ],

            // Sub-departments
            [
                'name' => 'Cardiac Surgery',
                'code' => 'CARD-SURG',
                'description' => 'Specialized heart surgery unit',
                'parent_id' => 4, // Cardiology department
                'head_id' => 7,
                'email' => 'cardiac.surgery@hospital.com',
                'phone' => '+1-555-0207',
                'display_order' => 1,
                'is_active' => true,
                'services' => json_encode(['bypass_surgery', 'valve_replacement', 'heart_transplant', 'minimally_invasive_surgery']),
            ],
            [
                'name' => 'Pediatric ICU',
                'code' => 'PED-ICU',
                'description' => 'Intensive care for critically ill children',
                'parent_id' => 6, // Pediatrics department
                'head_id' => 8,
                'email' => 'pediatric.icu@hospital.com',
                'phone' => '+1-555-0208',
                'display_order' => 1,
                'is_active' => true,
                'services' => json_encode(['critical_care', 'ventilator_support', 'neonatal_care', 'monitoring']),
            ],

            // Support Departments
            [
                'name' => 'Radiology',
                'code' => 'RAD',
                'description' => 'Medical imaging and diagnostic services',
                'parent_id' => null,
                'head_id' => 9,
                'email' => 'radiology@hospital.com',
                'phone' => '+1-555-0209',
                'display_order' => 6,
                'is_active' => true,
                'services' => json_encode(['x_ray', 'mri', 'ct_scan', 'ultrasound', 'diagnostic_imaging']),
            ],
            [
                'name' => 'Pharmacy',
                'code' => 'PHARM',
                'description' => 'Medication dispensing and management',
                'parent_id' => null,
                'head_id' => 10,
                'email' => 'pharmacy@hospital.com',
                'phone' => '+1-555-0210',
                'display_order' => 7,
                'is_active' => true,
                'services' => json_encode(['medication_dispensing', 'drug_information', 'inventory_management', 'prescription_verification']),
            ],
            [
                'name' => 'Laboratory',
                'code' => 'LAB',
                'description' => 'Medical testing and analysis',
                'parent_id' => null,
                'head_id' => 11,
                'email' => 'lab@hospital.com',
                'phone' => '+1-555-0211',
                'display_order' => 8,
                'is_active' => true,
                'services' => json_encode(['blood_tests', 'pathology', 'microbiology', 'biochemistry', 'hematology']),
            ],
            [
                'name' => 'Housekeeping',
                'code' => 'HK',
                'description' => 'Hospital cleanliness and sanitation',
                'parent_id' => 1, // Admin department
                'head_id' => 12,
                'email' => 'housekeeping@hospital.com',
                'phone' => '+1-555-0212',
                'display_order' => 2,
                'is_active' => true,
                'services' => json_encode(['cleaning', 'sanitization', 'waste_management', 'infection_control']),
            ]
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert($department);
        }
    }
}