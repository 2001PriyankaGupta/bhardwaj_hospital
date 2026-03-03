<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\Patient;
use App\Models\ActivityLog;
use App\Models\Schedule;
use App\Models\Medication;
use App\Models\Notification;
use App\Models\Appointment;
use App\Models\EmergencyTriage;
use App\Models\Department;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffCredentialsMail; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{


   public function dashboard()
    {
        $staff = Auth::user();

        // Fetch today's appointments with related patient/resource/department
        $todayAppointmentsList = Appointment::with(['patient', 'resource', 'department'])
            ->whereDate('appointment_date', today())
            ->orderBy('start_time')
            ->get();

        $appointmentCount = $todayAppointmentsList->count();

        $emergencyCases = EmergencyTriage::whereDate('created_at', today())->count();

        $dashboardData = [
            'staff' => $staff,
            'myPatients' => 15, // keep existing placeholder; adjust if you have a patients relation
            'todayAppointments' => $appointmentCount,
            'todayAppointmentsList' => $todayAppointmentsList,
            'pendingMedications' => 5,
            'emergencyCases' => $emergencyCases,
        ];

        return view('staff.index', $dashboardData);
    }
    
    public function index()
    {
        $user = Auth::user();
        $departments = Department::active()->get();
        $staff = Staff::with('departmentRelation')->latest()->get();
        return view($user->user_type.'.staff.index', compact('staff','departments'));
    }
    public function edit($id)
    {
        try {
            $staff = Staff::with(['departmentRelation', 'user'])->findOrFail($id);
            return response()->json($staff);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Staff not found'], 404);
        }
    }
   

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:staff,email|unique:users,email',
    //         'password' => 'required|string|min:8|confirmed',
    //         'phone' => 'nullable|string|max:20',
    //         'position' => 'required|string|max:255',
    //         'department_id' => 'required|exists:departments,id',
    //         'joining_date' => 'required|date',
    //         'staff_status' => 'required|in:active,inactive',
    //         'address' => 'nullable|string',
    //         // Add these fields for user table
    //         'gender' => 'nullable|in:male,female,other',
    //         'age' => 'nullable|integer|min:1|max:120',
    //         'emergency_contact_number' => 'nullable|string|max:20',
    //         'alternate_contact_number' => 'nullable|string|max:20',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $staffData = $request->all();
    //         $staffData['password'] = Hash::make($request->password);
            
    //         // 1. Create staff
    //         $staff = Staff::create($staffData);

    //         // 2. Create corresponding user entry
    //         $userData = [
    //             'name' => $staff->name,
    //             'email' => $staff->email,
    //             'password' => $staffData['password'],
    //             'user_type' => 'staff',
    //             'staff_id' => $staff->id,
    //             'email_verified_at' => now(),
                
    //             // Copy fields from request
    //             'phone' => $request->phone,
    //             'gender' => $request->gender,
    //             'age' => $request->age,
    //             'address' => $request->address,
    //             'emergency_contact_number' => $request->emergency_contact_number,
    //             'alternate_contact_number' => $request->alternate_contact_number,
    //             'basic_medical_history' => null,
    //             'profile_picture' => null, // Staff might not have profile picture
    //             'is_admin' => false,
    //             'role_id' => 2,
    //             'status' => 'active',
    //         ];

    //         $user = User::create($userData);

    //         // Send email with credentials
    //         try {
    //             Mail::to($request->email)->send(new StaffCredentialsMail($request->email, $request->password));
    //         } catch (\Exception $e) {
    //             Log::error('Failed to send staff credentials email: ' . $e->getMessage());
    //         }

    //         DB::commit();

    //         return response()->json(['success' => 'Staff added successfully! Credentials sent via email.']);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['error' => 'Error creating staff: ' . $e->getMessage()], 500);
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'joining_date' => 'required|date',
            'staff_status' => 'required|in:active,inactive',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'emergency_contact_number' => 'nullable|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $hashedPassword = Hash::make($request->password);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'user_type' => 'staff',
                'email_verified_at' => now(),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'age' => $request->age,
                'address' => $request->address,
                'emergency_contact_number' => $request->emergency_contact_number,
                'alternate_contact_number' => $request->alternate_contact_number,
                'basic_medical_history' => null,
                'profile_picture' => null,
                'is_admin' => false,
                'role_id' => 2,
                'status' => 'active',
            ]);

            $staff = Staff::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'phone' => $request->phone,
                'position' => $request->position,
                'department_id' => $request->department_id,
                'joining_date' => $request->joining_date,
                'staff_status' => $request->staff_status,
                'address' => $request->address,
                'user_id' => $user->id, 
            ]);

            $user->update([
                'staff_id' => $staff->id,
            ]);

            try {
                Mail::to($request->email)
                    ->send(new StaffCredentialsMail($request->email, $request->password));
            } catch (\Exception $e) {
                Log::error('Email failed: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => 'Staff added successfully! Credentials sent via email.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error creating staff: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        // User via user_id (NEW LOGIC)
        $user = User::find($staff->user_id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'joining_date' => 'required|date',
            'staff_status' => 'required|in:active,inactive',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'emergency_contact_number' => 'nullable|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
        ];

        if ($user) {
            $rules['email'] .= '|unique:users,email,' . $user->id;
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            /** 1️⃣ Update USER first */
            if ($user) {
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'age' => $request->age,
                    'address' => $request->address,
                    'emergency_contact_number' => $request->emergency_contact_number,
                    'alternate_contact_number' => $request->alternate_contact_number,
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);
            }

            /** 2️⃣ Update STAFF */
            $staffData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'position' => $request->position,
                'department_id' => $request->department_id,
                'joining_date' => $request->joining_date,
                'staff_status' => $request->staff_status,
                'address' => $request->address,
                'user_id' => $user ? $user->id : null, // 🔥 match user_id
            ];

            if ($request->filled('password')) {
                $staffData['password'] = Hash::make($request->password);
            }

            $staff->update($staffData);

            DB::commit();

            return response()->json([
                'success' => 'Staff & User updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error updating staff: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Staff $staff)
    {
        return response()->json($staff);
    }

    

     public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $staff = Staff::findOrFail($id);
            
            // Delete corresponding user
            User::where('staff_id', $id)->delete();
            
            // Delete staff
            $staff->delete();
            
            DB::commit();
            
            return response()->json(['success' => 'Staff deleted successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error deleting staff: ' . $e->getMessage()], 500);
        }
    }
    // In StaffController.php
    public function getShifts($id)
    {
        $staff = Staff::findOrFail($id);
        $shifts = $staff->upcomingShifts()->get();
        return response()->json($shifts);
    }

    // Leave Management for Staff
    public function leaves()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        $leaves = $staff->leaveApplications()->latest()->paginate(10);
        return view('staff.leaves.index', compact('staff', 'leaves'));
    }

    public function createLeave()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        return view('staff.leaves.create', compact('staff'));
    }

    public function storeLeave(Request $request)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'leave_type' => 'required|in:sick,casual,emergency,vacation',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $startDate = new \DateTime($validated['start_date']);
        $endDate = new \DateTime($validated['end_date']);
        $duration = $endDate->diff($startDate)->days + 1;

        // Check for overlapping leaves
        $existingLeave = LeaveApplication::where('staff_id', $staff->id)
            ->where('status', '!=', 'rejected')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        if ($existingLeave) {
            return back()->withInput()->withErrors(['start_date' => 'You already have a leave application for this period.']);
        }

        $leave = LeaveApplication::create([
            'staff_id' => $staff->id,
            'applicant_type' => 'staff',
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Notify admins
        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'leave_application',
                'title' => 'Staff Leave Applied',
                'meta_data' => [
                    'leave_id' => $leave->id,
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->name,
                    'leave_type' => $leave->leave_type,
                    'start_date' => $leave->start_date->format('Y-m-d'),
                    'end_date' => $leave->end_date->format('Y-m-d'),
                    'message' => "{$staff->name} (Staff) has applied for {$leave->leave_type} leave."
                ],
                'sender_id' => $user->id
            ]);
        }

        return redirect()->route('staff.leaves.index')->with('success', 'Leave application submitted successfully!');
    }

    public function editLeave(LeaveApplication $leave)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();

        if ($leave->staff_id !== $staff->id) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leaves can be edited.');
        }

        return view('staff.leaves.edit', compact('staff', 'leave'));
    }

    public function updateLeave(Request $request, LeaveApplication $leave)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();

        if ($leave->staff_id !== $staff->id) {
            abort(403);
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:sick,casual,emergency,vacation',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $leave->update($validated);

        return redirect()->route('staff.leaves.index')->with('success', 'Leave application updated successfully!');
    }

    public function destroyLeave(LeaveApplication $leave)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();

        if ($leave->staff_id !== $staff->id) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leaves can be deleted.');
        }

        $leave->delete();
        return redirect()->back()->with('success', 'Leave application deleted successfully.');
    }

    // Admin side for staff leaves
    public function adminStaffLeaves(Request $request)
    {
        $query = LeaveApplication::with(['staff.departmentRelation'])
            ->where('applicant_type', 'staff');

        if ($request->has('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        $leaves = $query->latest()->get(); // Remove pagination if using DataTables as per user request earlier

        $filtered_staff = $request->has('staff_id') ? Staff::find($request->staff_id) : null;

        return view('admin.staff.leaves', compact('leaves', 'filtered_staff'));
    }

    public function updateStaffLeaveStatus(Request $request, LeaveApplication $leave)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remarks' => 'nullable|string|max:500'
        ]);

        $leave->update([
            'status' => $request->status,
            'admin_remarks' => $request->admin_remarks,
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }
}