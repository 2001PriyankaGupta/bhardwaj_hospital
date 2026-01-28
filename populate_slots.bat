#!/bin/bash
# Populate date_slots table for doctors

# This script uses php artisan tinker to populate test data
php artisan tinker << 'EOF'
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$doctors = Doctor::where('status', 'active')->limit(5)->get();

foreach ($doctors as $doctor) {
    $startDate = now()->addDay();
    
    for ($i = 0; $i < 30; $i++) {
        $date = $startDate->copy()->addDays($i);
        
        if ($date->isWeekend()) {
            continue;
        }

        DB::table('date_slots')->insertOrIgnore([
            'doctor_id' => $doctor->id,
            'slot_date' => $date->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'slot_duration' => 30,
            'max_patients' => 20,
            'booked_slots' => 0,
            'is_available' => true,
            'time_slots' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

echo "Date slots created successfully!";
EOF
