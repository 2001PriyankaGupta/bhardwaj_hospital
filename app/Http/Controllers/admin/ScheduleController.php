<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DateSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function schedules(Doctor $doctor)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        
        // Fetch weekly schedules
        $schedules = \App\Models\DoctorSchedule::where('doctor_id', $doctor->id)->get();
            
        return view($userType.'.doctor.schedules', compact('doctor', 'schedules'));
    }

    public function dateManagement(Doctor $doctor)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        
        // Fetch date slots for current month
        $dateSlots = DateSlot::where('doctor_id', $doctor->id)
            ->where('slot_date', '>=', Carbon::now()->startOfMonth())
            ->where('slot_date', '<=', Carbon::now()->endOfMonth())
            ->orderBy('slot_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
            
        return view($userType.'.doctor.date_schedules', compact('doctor', 'dateSlots'));
    }

    public function storeSchedule(Request $request, Doctor $doctor)
    {
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        // Start and end dates for bulk generation (default to 1 month)
        $startDate = $request->input('bulk_start_date') ? Carbon::parse($request->input('bulk_start_date')) : Carbon::today();
        $endDate = $request->input('bulk_end_date') ? Carbon::parse($request->input('bulk_end_date')) : Carbon::today()->addDays(30);

        DB::beginTransaction();
        try {
            foreach ($daysOfWeek as $day) {
                if ($request->has("day_of_week.{$day}")) {
                    $isAvailable = $request->has("available.{$day}") && $request->input("available.{$day}") == 1;
                    
                    // Fallback to defaults if inputs are missing (happens when disabled in frontend)
                    $startTime = $request->input("start_time.{$day}") ?? '09:00';
                    $endTime = $request->input("end_time.{$day}") ?? '17:00';
                    $slotDuration = $request->input("slot_duration.{$day}") ?? 30;
                    $maxPatients = $request->input("max_patients.{$day}") ?? 10;

                    // Save weekly pattern
                    $weeklySchedule = \App\Models\DoctorSchedule::updateOrCreate(
                        ['doctor_id' => $doctor->id, 'day_of_week' => $day],
                        [
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'slot_duration' => $slotDuration,
                            'max_patients' => $maxPatients,
                            'is_available' => $isAvailable
                        ]
                    );

                    // If generating for the month
                    if ($isAvailable) {
                        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                            if (strtolower($date->format('l')) === $day) {
                                // Check if slot already exists for this date
                                $existingSlot = DateSlot::where('doctor_id', $doctor->id)
                                    ->where('slot_date', $date->format('Y-m-d'))
                                    ->first();

                                if (!$existingSlot) {
                                    $timeSlots = $this->generateTimeSlots($startTime, $endTime, $slotDuration, $maxPatients);

                                    DateSlot::create([
                                        'doctor_id' => $doctor->id,
                                        'slot_date' => $date->format('Y-m-d'),
                                        'start_time' => $startTime,
                                        'end_time' => $endTime,
                                        'slot_duration' => $slotDuration,
                                        'max_patients' => $maxPatients,
                                        'booked_slots' => 0,
                                        'is_available' => true,
                                        'time_slots' => $timeSlots,
                                    ]);
                                } else {
                                    // If slot exists but has 0 bookings, we update it to match new pattern
                                    if ($existingSlot->booked_slots == 0) {
                                        $timeSlots = $this->generateTimeSlots($startTime, $endTime, $slotDuration, $maxPatients);
                                        $existingSlot->update([
                                            'start_time' => $startTime,
                                            'end_time' => $endTime,
                                            'slot_duration' => $slotDuration,
                                            'max_patients' => $maxPatients,
                                            'is_available' => true,
                                            'time_slots' => $timeSlots,
                                        ]);
                                    }
                                }
                            }
                        }
                    } else {
                        // If marked as NOT available (e.g. Sunday Off), clear existing slots for this day in range
                        // We only remove if there are no bookings to avoid breaking existing appointments
                        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                            if (strtolower($date->format('l')) === $day) {
                                DateSlot::where('doctor_id', $doctor->id)
                                    ->where('slot_date', $date->format('Y-m-d'))
                                    ->where('booked_slots', 0)
                                    ->delete();
                            }
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Weekly schedule saved and monthly slots generated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving schedule: ' . $e->getMessage());
        }
    }

    public function updateSchedule(Request $request, int $id)
    {
        try {
            $dateSlot = DateSlot::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'slot_duration' => 'required|integer|in:15,30,45,60',
                'max_patients' => 'required|integer|min:1|max:50',
                'is_available' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            // Generate updated time slots
            $timeSlots = $this->generateTimeSlots(
                $request->start_time,
                $request->end_time,
                $request->slot_duration,
                $request->max_patients
            );

            // Check if we can reduce max_patients below booked slots
            if ($request->max_patients < $dateSlot->booked_slots) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reduce max patients below currently booked slots ('.$dateSlot->booked_slots.' booked)'
                ], 400);
            }

            // Update slot
            $dateSlot->update([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'slot_duration' => $request->slot_duration,
                'max_patients' => $request->max_patients,
                'is_available' => $request->has('is_available') ? $request->is_available : true,
                'time_slots' => $timeSlots,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Slot updated successfully.',
                
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slot not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update slot: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSchedule(int $id)
    {
        try {
            $dateSlot = DateSlot::findOrFail($id);
            
            if ($dateSlot->booked_slots > 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot delete slot with booked appointments.'
                ], 400);
            }

            $dateSlot->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Slot deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete slot: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkCreate(Request $request, Doctor $doctor)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|in:15,30,45,60',
            'max_patients' => 'required|integer|min:1|max:50',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $createdCount = 0;

        // Loop through each date
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dayName = strtolower($date->format('l'));
            
            // Check if this day is selected
            if (in_array($dayName, $request->days_of_week)) {
                // Check if slot already exists
                $existingSlot = DateSlot::where('doctor_id', $doctor->id)
                    ->where('slot_date', $date->format('Y-m-d'))
                    ->where('start_time', $request->start_time)
                    ->first();

                if (!$existingSlot) {
                    // Generate time slots
                    $timeSlots = $this->generateTimeSlots(
                        $request->start_time,
                        $request->end_time,
                        $request->slot_duration,
                        $request->max_patients
                    );

                    DateSlot::create([
                        'doctor_id' => $doctor->id,
                        'slot_date' => $date->format('Y-m-d'),
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'slot_duration' => $request->slot_duration,
                        'max_patients' => $request->max_patients,
                        'booked_slots' => 0,
                        'is_available' => true,
                        'time_slots' => $timeSlots,
                    ]);

                    $createdCount++;
                }
            }
        }

        return redirect()->back()->with('success', "{$createdCount} slots created successfully.");
    }

    
    private function generateTimeSlots($startTime, $endTime, $duration, $maxPatients)
    {
        $slots = [];
        $start = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);
        $current = $start->copy();
        
        // Convert duration to integer
        $duration = (int) $duration;
        $maxPatients = (int) $maxPatients;

        while ($current->lt($end)) {
            $slotEnd = $current->copy()->addMinutes($duration);
            
            if ($slotEnd->lte($end)) {
                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'available' => $maxPatients,
                    'booked' => 0,
                ];
            }
            
            $current->addMinutes($duration);
        }

        return $slots;
    }

    
    public function getSlotsByMonth(Request $request, Doctor $doctor)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        $dateSlots = DateSlot::where('doctor_id', $doctor->id)
            ->where('slot_date', '>=', $startDate)
            ->where('slot_date', '<=', $endDate)
            ->orderBy('slot_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->groupBy('slot_date');

        return response()->json($dateSlots);
    }
    // ScheduleController.php में
    public function editSchedule(DateSlot $dateSlot)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        
        // Check authorization
        if ($userType === 'doctor') {
            if ($dateSlot->doctor_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        
        // Date को proper format में convert करें
        $slotDate = $dateSlot->slot_date;
        
        // Ensure it's in YYYY-MM-DD format
        if ($slotDate instanceof \Carbon\Carbon) {
            $slotDate = $slotDate->format('Y-m-d');
        } else if (is_string($slotDate)) {
            // If it's string, try to parse and format
            try {
                $slotDate = \Carbon\Carbon::parse($slotDate)->format('Y-m-d');
            } catch (\Exception $e) {
                // Keep original if parsing fails
            }
        }
        
        return response()->json([
            'slot_date' => $slotDate, // Formatted date
            'start_time' => $dateSlot->start_time,
            'end_time' => $dateSlot->end_time,
            'slot_duration' => $dateSlot->slot_duration,
            'max_patients' => $dateSlot->max_patients,
            'is_available' => (bool) $dateSlot->is_available, // Ensure boolean
            'booked_slots' => $dateSlot->booked_slots,
        ]);
    }
}