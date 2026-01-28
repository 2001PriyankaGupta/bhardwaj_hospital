<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index($id)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $staff = Staff::findOrFail($id);
        
        $shifts = Shift::where('staff_id', $id)
                    ->orderBy('start_date', 'desc')
                    ->get();
        
        return view($userType.'.shift.index', compact('staff', 'shifts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:staff,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'shift_type' => 'required|in:morning,evening,night',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if staff already has shift on start date
        $existingShift = Shift::where('staff_id', $request->staff_id)
                            ->where('start_date', $request->start_date)
                            ->first();

        if ($existingShift) { 
            return response()->json([
                'errors' => ['start_date' => ['This staff already has a shift on the start date']]
            ], 422);
        }

        Shift::create($request->all());

        return response()->json(['success' => 'Shift scheduled successfully!']);
    }

    public function update(Request $request, Shift $shift)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'shift_type' => 'required|in:morning,evening,night',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check for duplicate shift (excluding current shift)
        $existingShift = Shift::where('staff_id', $shift->staff_id)
                            ->where('start_date', $request->start_date)
                            ->where('id', '!=', $shift->id)
                            ->first();

        if ($existingShift) {
            return response()->json([
                'errors' => ['start_date' => ['This staff already has a shift on the start date']]
            ], 422);
        }

        $shift->update($request->all());

        return response()->json(['success' => 'Shift updated successfully!']);
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return response()->json(['success' => 'Shift deleted successfully!']);
    }
}