<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PatientMedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        // Dummy medical conditions
        $conditions = [
            'Hypertension',
            'Diabetes Type 2',
            'Asthma',
            'Migraine',
            'Arthritis',
            'Anxiety Disorder',
            'Allergic Rhinitis',
            'Gastroesophageal Reflux Disease',
            'Hyperthyroidism',
            'Chronic Back Pain'
        ];
        
        // Dummy symptoms
        $symptomsList = [
            'Fever, Headache, Fatigue',
            'Cough, Shortness of breath, Chest pain',
            'Nausea, Vomiting, Dizziness',
            'Joint pain, Swelling, Stiffness',
            'Abdominal pain, Bloating, Indigestion',
            'Rash, Itching, Redness',
            'Sore throat, Runny nose, Congestion',
            'Muscle weakness, Tingling, Numbness',
            'Palpitations, Sweating, Tremors',
            'Back pain, Limited mobility, Muscle spasms'
        ];
        
        // Dummy treatments
        $treatments = [
            'Prescribed antibiotics for 7 days. Rest and hydration advised.',
            'Anti-inflammatory medication. Physical therapy recommended.',
            'Inhaler prescribed. Avoid triggers like dust and pollen.',
            'Pain management with NSAIDs. Follow-up in 2 weeks.',
            'Diet modification and antacids. Elevate head during sleep.',
            'Antihistamines prescribed. Avoid known allergens.',
            'Beta-blockers started. Monitor blood pressure weekly.',
            'Insulin therapy initiated. Dietary counseling provided.',
            'Antidepressants prescribed. Counseling sessions scheduled.',
            'Thyroid medication adjusted. Regular monitoring needed.'
        ];
        
        // Dummy notes
        $notes = [
            'Patient responded well to treatment. Vital signs stable.',
            'Advised to follow up if symptoms worsen.',
            'Patient needs to quit smoking for better recovery.',
            'Recommended lifestyle modifications and exercise.',
            'Family history of similar condition noted.',
            'Patient allergic to penicillin.',
            'Referred to specialist for further evaluation.',
            'Patient compliant with medication regimen.',
            'Warned about potential side effects.',
            'Scheduled for lab tests next month.'
        ];
        
        // Test reports dummy data
        $testReports = [
            [
                ['test_name' => 'Complete Blood Count', 'result' => 'Normal', 'date' => Carbon::now()->subDays(5)->toDateString()],
                ['test_name' => 'Urine Analysis', 'result' => 'Normal', 'date' => Carbon::now()->subDays(5)->toDateString()]
            ],
            [
                ['test_name' => 'Blood Sugar Fasting', 'result' => '110 mg/dL', 'date' => Carbon::now()->subDays(3)->toDateString()],
                ['test_name' => 'HbA1c', 'result' => '6.2%', 'date' => Carbon::now()->subDays(3)->toDateString()]
            ],
            [
                ['test_name' => 'Lipid Profile', 'result' => 'Cholesterol: 180 mg/dL', 'date' => Carbon::now()->subDays(2)->toDateString()],
                ['test_name' => 'Liver Function Test', 'result' => 'Normal', 'date' => Carbon::now()->subDays(2)->toDateString()]
            ],
            [
                ['test_name' => 'Thyroid Stimulating Hormone', 'result' => '2.5 mIU/L', 'date' => Carbon::now()->subDays(1)->toDateString()]
            ],
            [
                ['test_name' => 'ECG', 'result' => 'Normal sinus rhythm', 'date' => Carbon::now()->toDateString()],
                ['test_name' => 'Chest X-ray', 'result' => 'No abnormalities detected', 'date' => Carbon::now()->toDateString()]
            ]
        ];
        
        // Generate 3 records for patient_id = 5 and appointment_id = 8
        for ($i = 1; $i <= 3; $i++) {
            $conditionIndex = rand(0, count($conditions) - 1);
            $symptomIndex = rand(0, count($symptomsList) - 1);
            $treatmentIndex = rand(0, count($treatments) - 1);
            $noteIndex = rand(0, count($notes) - 1);
            $testIndex = rand(0, count($testReports) - 1);
            
            $recordDate = Carbon::now()->subDays(rand(1, 30));
            
            DB::table('patient_medical_records')->insert([
                'appointment_id' => 8,
                'patient_id' => 5,
                'doctor_id' => rand(1, 10), // Assuming you have 10 doctors
                'symptoms' => $symptomsList[$symptomIndex],
                'diagnosis' => $conditions[$conditionIndex],
                'treatment_plan' => $treatments[$treatmentIndex],
                'notes' => $notes[$noteIndex],
                'height' => rand(150, 185) . ' cm',
                'weight' => rand(50, 90) . ' kg',
                'blood_pressure' => rand(110, 140) . '/' . rand(70, 90) . ' mmHg',
                'temperature' => rand(36.5, 38.5) . '°C',
                'test_reports' => json_encode($testReports[$testIndex]),
                'record_date' => $recordDate->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info("Medical Record $i created for Patient ID 5, Appointment ID 8");
        }
        
        $this->command->info('✅ 3 Patient Medical Records created successfully!');
    }
}