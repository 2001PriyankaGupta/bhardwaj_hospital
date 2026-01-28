<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bed;
use App\Models\Room;
use Carbon\Carbon;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->command->info('No rooms found. Please run RoomSeeder first.');
            return;
        }

        $created = 0;

        foreach ($rooms as $room) {
            // Skip if beds already exist for this room to avoid duplicates
            if (Bed::where('room_id', $room->id)->exists()) {
                continue;
            }

            $bedCount = (int) ($room->bed_count ?? 1);
            $currentOccupancy = (int) ($room->current_occupancy ?? 0);

            for ($i = 1; $i <= $bedCount; $i++) {
                // Bed number like ROOM-1-B01
                $bedNumber = $room->room_number . '-B' . str_pad($i, 2, '0', STR_PAD_LEFT);

                if ($i <= $currentOccupancy) {
                    $status = 'occupied';
                    $lastOccupancy = Carbon::now()->subDays(rand(1, 30));
                    $nextAvailability = Carbon::now()->addDays(rand(1, 14));
                } else {
                    // Occasionally mark some beds as maintenance or reserved (ensure only allowed statuses are used)
                    $rand = rand(1, 100);
                    if ($rand <= 5) {
                        $status = 'maintenance';
                        $lastOccupancy = null;
                        $nextAvailability = Carbon::now()->addDays(rand(3, 30));
                    } elseif ($rand <= 10) {
                        $status = 'reserved';
                        $lastOccupancy = null;
                        $nextAvailability = Carbon::now()->addDays(rand(1, 7));
                    } else {
                        $status = 'available';
                        $lastOccupancy = null;
                        $nextAvailability = null;
                    }

                // Defensive: ensure status is one of the allowed values (keeps seeder safe against future schema changes)
                $allowedStatuses = ['available', 'occupied', 'maintenance', 'cleaning', 'reserved'];
                if (! in_array($status, $allowedStatuses, true)) {
                    $status = 'available';
                }
                }

                Bed::create([
                    'room_id' => $room->id,
                    'bed_number' => $bedNumber,
                    'status' => $status,
                    'last_occupancy_date' => $lastOccupancy,
                    'next_availability_date' => $nextAvailability,
                    'notes' => null,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $created++;
            }
        }

        $this->command->info("Beds seeded: {$created}");
    }
}
