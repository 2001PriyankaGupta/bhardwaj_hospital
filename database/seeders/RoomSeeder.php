<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = RoomType::all();

        if ($roomTypes->isEmpty()) {
            $this->command->info('No room types found. Please run RoomTypeSeeder first.');
            return;
        }

        $created = 0;

        foreach ($roomTypes as $type) {
            // create up to available_rooms (fallback to 5, and cap to 20 to avoid excessive seeding)
            $count = (int) ($type->available_rooms ?? 5);
            $count = max(1, min($count, 20));

            for ($i = 1; $i <= $count; $i++) {
                $floor = rand(1, 6);
                $short = strtoupper(preg_replace('/\s+/', '', $type->name));
                $short = substr($short, 0, 3);
                $roomNumber = $short . '-' . $floor . str_pad($i, 2, '0', STR_PAD_LEFT);

                $bedCount = (int) ($type->max_capacity ?? 1);
                $currentOccupancy = min($bedCount, rand(0, $bedCount));

                $additionalAmenities = [];
                if (stripos($type->name, 'private') !== false || stripos($type->name, 'deluxe') !== false) {
                    $additionalAmenities = ['Room Service', 'Mini Fridge'];
                }

                Room::create([
                    'room_number' => $roomNumber,
                    'room_type_id' => $type->id,
                    'floor_number' => (string) $floor,
                    'ward_name' => $type->name . ' Ward',
                    'status' => $currentOccupancy >= $bedCount ? 'occupied' : 'available',
                    'bed_count' => $bedCount,
                    'current_occupancy' => $currentOccupancy,
                    'additional_amenities' => $additionalAmenities,
                    'notes' => null,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $created++;
            }
        }

        $this->command->info("Rooms seeded: {$created}");
    }
}
