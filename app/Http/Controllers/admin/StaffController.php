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
use App\Models\Department;
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
        
        $dashboardData = [
            'staff' => $staff,
            'myPatients' => 15,
            'todayAppointments' => 8,
            'pendingMedications' => 5,
            'emergencyCases' => 2
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
}