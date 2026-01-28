<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DateSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function schedules(Doctor $doctor)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        
        // Current month ke slots fetch karenge
        $currentMonth = Carbon::now()->format('Y-m');
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
        $validator = Validator::make($request->all(), [
            'slot_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|in:15,30,45,60',
            'max_patients' => 'required|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if slot already exists
        $existingSlot = DateSlot::where('doctor_id', $doctor->id)
            ->where('slot_date', $request->slot_date)
            ->where('start_time', $request->start_time)
            ->first();

        if ($existingSlot) {
            return redirect()->back()
                ->with('error', 'Slot for this date and time already exists.')
                ->withInput();
        }

        // Generate time slots
        $timeSlots = $this->generateTimeSlots(
            $request->start_time,
            $request->end_time,
            $request->slot_duration,
            $request->max_patients
        );

        // Create new slot
        $dateSlot = DateSlot::create([
            'doctor_id' => $doctor->id,
            'slot_date' => $request->slot_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'slot_duration' => $request->slot_duration,
            'max_patients' => $request->max_patients,
            'booked_slots' => 0,
            'is_available' => true,
            'time_slots' => $timeSlots,
        ]);

        return redirect()->back()->with('success', 'Date slot added successfully.');
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