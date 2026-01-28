<?php

namespace App\Console\Commands;

use App\Models\EmergencyTriage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateEmergencyReport extends Command
{
    protected $signature = 'emergency:report {--date= : Specific date (Y-m-d)}';
    protected $description = 'Generate daily emergency triage report';

    public function handle()
    {
        $date = $this->option('date') ?: now()->format('Y-m-d');

        $cases = EmergencyTriage::whereDate('created_at', $date)->get();

        if ($cases->isEmpty()) {
            $this->error("No emergency cases found for date: {$date}");
            return;
        }

        $reportData = [
            'report_date' => $date,
            'total_cases' => $cases->count(),
            'triage_breakdown' => $cases->groupBy('triage_level')->map->count(),
            'status_breakdown' => $cases->groupBy('status')->map->count(),
            'cases' => $cases->map(function ($case) {
                return [
                    'case_number' => $case->case_number,
                    'patient_name' => $case->patient_name,
                    'triage_level' => $case->triage_level,
                    'status' => $case->status,
                    'assigned_staff' => $case->assigned_staff,
                    'arrival_time' => $case->arrival_time->format('H:i:s'),
                ];
            })->toArray(),
        ];

        // Save report to storage
        $filename = "emergency-report-{$date}.json";
        Storage::put("reports/{$filename}", json_encode($reportData, JSON_PRETTY_PRINT));

        $this->info("Emergency report generated successfully!");
        $this->info("Total cases: {$reportData['total_cases']}");
        $this->info("File saved: storage/app/reports/{$filename}");

        return Command::SUCCESS;
    }
}