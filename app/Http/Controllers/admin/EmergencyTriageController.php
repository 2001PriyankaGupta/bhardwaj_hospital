<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyTriage;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;

class EmergencyTriageController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user = Auth::user();
    //     $userType = $user->user_type;
    //       $query = EmergencyTriage::with(['creator', 'staff']);

    //     // Search functionality
    //     if ($request->has('search') && $request->search != '') {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('case_number', 'like', "%{$search}%")
    //               ->orWhere('patient_name', 'like', "%{$search}%")
    //               ->orWhere('symptoms', 'like', "%{$search}%");
    //         });
    //     }

    //     // Filter by triage level
    //     if ($request->has('triage_level') && $request->triage_level != '') {
    //         $query->where('triage_level', $request->triage_level);
    //     }

    //     // Filter by status
    //     if ($request->has('status') && $request->status != '') {
    //         $query->where('status', $request->status);
    //     }

    //     $cases = $query->orderBy('priority_score', 'desc')
    //                   ->orderBy('arrival_time', 'asc')
    //                   ->paginate(20);

    //     $triageLevels = EmergencyTriage::getTriageLevels();
    //     $statusOptions = EmergencyTriage::getStatusOptions();

    //     // Statistics for dashboard
    //     $stats = [
    //         'total_cases' => EmergencyTriage::count(),
    //         'pending_cases' => EmergencyTriage::where('status', 'pending')->count(),
    //         'red_cases' => EmergencyTriage::where('triage_level', 'Red')->where('status', '!=', 'completed')->count(),
    //         'yellow_cases' => EmergencyTriage::where('triage_level', 'Yellow')->where('status', '!=', 'completed')->count(),
    //     ];

    //      $staff = Staff::with('departmentRelation')->latest()->get();

    //     return view($userType.'.emergency.index', compact('cases', 'triageLevels', 'statusOptions', 'stats','staff'));
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $query = EmergencyTriage::with(['creator', 'staff']);

        if ($userType === 'doctor') {
            $query->where('doctor_id', $user->doctor_id);
        } elseif ($userType === 'staff') {
            $query->where('assigned_staff', $user->staff_id); // सिर्फ assigned cases
        }


        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                    ->orWhere('patient_name', 'like', "%{$search}%")
                    ->orWhere('symptoms', 'like', "%{$search}%");
            });
        }

        if ($request->has('triage_level') && $request->triage_level != '') {
            $query->where('triage_level', $request->triage_level);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $cases = $query->orderBy('priority_score', 'desc')
                    ->orderBy('arrival_time', 'asc')
                    ->paginate(20);

        $triageLevels = EmergencyTriage::getTriageLevels();
        $statusOptions = EmergencyTriage::getStatusOptions();

        // Stats calculation
        if ($userType === 'staff') {
            $stats = [
                'total_cases' => EmergencyTriage::where('assigned_staff', $user->staff_id)->count(),
                'pending_cases' => EmergencyTriage::where('assigned_staff', $user->staff_id)
                    ->where('status', 'pending')->count(),
                'red_cases' => EmergencyTriage::where('assigned_staff', $user->staff_id)
                    ->where('triage_level', 'Red')
                    ->where('status', '!=', 'completed')->count(),
                'yellow_cases' => EmergencyTriage::where('assigned_staff', $user->staff_id)
                    ->where('triage_level', 'Yellow')
                    ->where('status', '!=', 'completed')->count(),
            ];
        } elseif ($userType === 'doctor') {
            $stats = [
                'total_cases' => EmergencyTriage::where('doctor_id', $user->doctor_id)->count(),
                'pending_cases' => EmergencyTriage::where('doctor_id', $user->doctor_id)
                    ->where('status', 'pending')->count(),
                'red_cases' => EmergencyTriage::where('doctor_id', $user->doctor_id)
                    ->where('triage_level', 'Red')
                    ->where('status', '!=', 'completed')->count(),
                'yellow_cases' => EmergencyTriage::where('doctor_id', $user->doctor_id)
                    ->where('triage_level', 'Yellow')
                    ->where('status', '!=', 'completed')->count(),
            ];
        } else {
            // Admin stats
            $stats = [
                'total_cases' => EmergencyTriage::count(),
                'pending_cases' => EmergencyTriage::where('status', 'pending')->count(),
                'red_cases' => EmergencyTriage::where('triage_level', 'Red')
                    ->where('status', '!=', 'completed')->count(),
                'yellow_cases' => EmergencyTriage::where('triage_level', 'Yellow')
                    ->where('status', '!=', 'completed')->count(),
            ];
        }

        $staff = Staff::with('departmentRelation')->latest()->get();

        return view($userType.'.emergency.index', compact('cases', 'triageLevels', 'statusOptions', 'stats', 'staff'));
    }

    public function create()
    {
        $user = Auth::user();
        $doctor = Doctor::all();
        $triageLevels = EmergencyTriage::getTriageLevels();
        return view($user->user_type.'.emergency.create', compact('triageLevels', 'doctor'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:150',
            'gender' => 'required|string|in:Male,Female,Other',
            'symptoms' => 'required|string',
            'triage_level' => 'required|string|in:Red,Yellow,Green,Blue',
            'notes' => 'nullable|string',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        // Calculate priority score based on triage level
        $priorityScores = [
            'Red' => 100,
            'Yellow' => 75,
            'Green' => 50,
            'Blue' => 25,
        ];

        $emergencyTriage = EmergencyTriage::create([
            'patient_name' => $request->patient_name,
            'doctor_id' => $request->doctor_id,
            'age' => $request->age,
            'gender' => $request->gender,
            'symptoms' => $request->symptoms,
            'triage_level' => $request->triage_level,
            'priority_score' => $priorityScores[$request->triage_level],
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route($user->user_type.'.emergency.index')
                         ->with('success', 'Emergency case created successfully!');
    }

    public function show(EmergencyTriage $emergency)
    {
         $user = Auth::user();
        $userType = $user->user_type;
        return view($userType.'.emergency.show', compact('emergency'));
    }

    public function edit(EmergencyTriage $emergency)
    {
        $userType = Auth::user()->user_type;
        $doctor = Doctor::all();
        $triageLevels = EmergencyTriage::getTriageLevels();
        $statusOptions = EmergencyTriage::getStatusOptions();
        // Provide staff list for assignment dropdown
        $staff = Staff::latest()->get();
        return view($userType.'.emergency.edit', compact('emergency', 'triageLevels', 'statusOptions', 'doctor', 'staff'));
    }

    public function update(Request $request, EmergencyTriage $emergency)
    {
       $userType = Auth::user()->user_type;
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0|max:150',
            'gender' => 'required|string|in:Male,Female,Other',
            'symptoms' => 'required|string',
            'triage_level' => 'required|string|in:Red,Yellow,Green,Blue',
            'assigned_staff' => 'nullable|exists:staff,id',
            'status' => 'required|string|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        // Update treatment time if status changed to completed
        $updateData = $request->only([
            'patient_name', 'age', 'gender', 'symptoms', 'triage_level',
            'assigned_staff', 'status', 'notes', 'doctor_id'
        ]);

        if ($request->status == 'completed' && $emergency->status != 'completed') {
            $updateData['treatment_time'] = now();
        }

        $emergency->update($updateData);

        return redirect()->route($userType.'.emergency.index')
                         ->with('success', 'Emergency case updated successfully!');
    }

    public function destroy(EmergencyTriage $emergency)
    {
        $userType = Auth::user()->user_type;
        $emergency->delete();

        return redirect()->route($userType.'.emergency.index')
                         ->with('success', 'Emergency case deleted successfully!');
    }

    public function assignStaff(Request $request, EmergencyTriage $emergency)
    {
        $request->validate([
            'assigned_staff' => 'required|string|max:255',
        ]);

        $emergency->update([
            'assigned_staff' => $request->assigned_staff,
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Staff assigned successfully!');
    }

    public function updateStatus(Request $request, EmergencyTriage $emergency)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status == 'completed') {
            $updateData['treatment_time'] = now();
        }

        $emergency->update($updateData);

        return back()->with('success', 'Status updated successfully!');
    }


    // get emergency api

    public function getAllemergency(Request $request)
    {
        try {
            $cases = EmergencyTriage::all();

            return response()->json([
                'success' => true,
                'message' => 'Emergency cases retrieved successfully',
                'data' => $cases,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve emergency cases',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getemergencyById($id)
    {
        try {
            $emergency = EmergencyTriage::with(['creator', 'staff'])->find($id);

            if (!$emergency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emergency case not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Emergency case retrieved successfully',
                'data' => $emergency,

            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve emergency case',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createemergency(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'patient_name' => 'required|string|max:255',
                'age' => 'required|integer|min:0|max:150',
                'gender' => 'required|string|in:Male,Female,Other',
                'symptoms' => 'required|string',
                'triage_level' => 'required|string|in:Red,Yellow,Green,Blue',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Calculate priority score based on triage level
            $priorityScores = [
                'Red' => 100,
                'Yellow' => 75,
                'Green' => 50,
                'Blue' => 25,
            ];

            $emergencyTriage = EmergencyTriage::create([
                'patient_name' => $request->patient_name,
                'age' => $request->age,
                'gender' => $request->gender,
                'symptoms' => $request->symptoms,
                'triage_level' => $request->triage_level,
                'priority_score' => $priorityScores[$request->triage_level],
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'arrival_time' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Emergency case created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create emergency case',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateemergency(Request $request, $id)
    {
        try {
            $emergency = EmergencyTriage::find($id);

            if (!$emergency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emergency case not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'patient_name' => 'required|string|max:255',
                'age' => 'required|integer|min:0|max:150',
                'gender' => 'required|string|in:Male,Female,Other',
                'symptoms' => 'required|string',
                'triage_level' => 'required|string|in:Red,Yellow,Green,Blue',
                'status' => 'required|string|in:pending,in_progress,completed',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = $request->only([
                'patient_name', 'age', 'gender', 'symptoms', 'triage_level',
                'status', 'notes'
            ]);

            if ($request->status == 'completed' && $emergency->status != 'completed') {
                $updateData['treatment_time'] = now();
            }

            // Update priority score if triage level changed
            if ($request->has('triage_level') && $request->triage_level != $emergency->triage_level) {
                $priorityScores = [
                    'Red' => 100,
                    'Yellow' => 75,
                    'Green' => 50,
                    'Blue' => 25,
                ];
                $updateData['priority_score'] = $priorityScores[$request->triage_level];
            }

            $emergency->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Emergency case updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update emergency case',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteemergency($id)
    {
        try {
            $emergency = EmergencyTriage::find($id);

            if (!$emergency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emergency case not found'
                ], 404);
            }

            $emergency->delete();

            return response()->json([
                'success' => true,
                'message' => 'Emergency case deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete emergency case',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTriageLevels()
    {
        try {
            $triageLevels = EmergencyTriage::getTriageLevels();

            return response()->json([
                'success' => true,
                'data' => $triageLevels,
                'message' => 'Triage levels retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve triage levels',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatusOptions()
    {
        try {
            $statusOptions = EmergencyTriage::getStatusOptions();

            return response()->json([
                'success' => true,
                'data' => $statusOptions,
                'message' => 'Status options retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve status options',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
