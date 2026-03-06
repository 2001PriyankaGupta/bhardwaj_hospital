<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\DoctorSchedule;
use App\Models\LeaveApplication;
use App\Models\DoctorPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\DoctorWelcomeMail;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseService;
use App\Models\Notification;
use App\Notifications\CallStartedNotification;

class DoctorController extends Controller
{

   protected $fcm;

    public function __construct()
    {
        // Make Firebase optional and defensive: prefer config, fall back to env, and ensure credentials exist
        $this->fcm = null;

        $projectId = config('services.firebase.project_id') ?? env('FIREBASE_PROJECT_ID');
        $credentialsConfig = config('services.firebase.credentials_path') ?? env('FIREBASE_CREDENTIALS_PATH');
        $credentialsPath = $credentialsConfig ? public_path($credentialsConfig) : null;

        if ($projectId && $credentialsPath && file_exists($credentialsPath)) {
            try {
                $this->fcm = new FirebaseService($projectId, $credentialsPath);
            } catch (\Throwable $e) {
                logger()->warning('FirebaseService init failed: ' . $e->getMessage());
                $this->fcm = null;
            }
        } else {
            logger()->warning('Firebase not configured: missing service account JSON (FIREBASE_CREDENTIALS_PATH) or FIREBASE_PROJECT_ID. Falling back to server key if available (FIREBASE_SERVER_KEY), otherwise Firebase notifications are disabled.');
        }
    }
    
    public function dashboard()
    {
        $user = Auth::user();
        $doctor = $user->doctor; // Get the Doctor model

        // Get today's appointments
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->with('patient.user')
            ->orderBy('start_time')
            ->get();

        // Get upcoming appointments (next 3 days)
        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', '>', today())
            ->whereDate('appointment_date', '<=', today()->addDays(3))
            ->with('patient.user')
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        // Get appointment statistics
        $totalToday = $todayAppointments->count();
        $completedToday = $todayAppointments->where('status', 'completed')->count();
        $pendingToday = $totalToday - $completedToday;

        // Get total active patients (unique patients with appointments)
        $activePatients = $doctor->appointments()->distinct('patient_id')->count('patient_id');

        // Get pending prescriptions (assuming prescriptions without valid_until or something)
        $pendingPrescriptions = $doctor->prescriptions()->where('is_active', true)->count();

        // Get emergency cases
        $emergencyCases = \App\Models\EmergencyTriage::where('status', 'active')->count();

        return view('doctor.index', compact('doctor', 'todayAppointments', 'upcomingAppointments', 'totalToday', 'completedToday', 'pendingToday', 'activePatients', 'pendingPrescriptions', 'emergencyCases'));
    }

    public function notifyPatient(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        $appointment  = Appointment::findOrFail($request->appointment_id);
        $patientUser  = $appointment->patient->user;
        $doctorUser   = auth()->user(); // doctor

        $pushResult = null;
        if ($this->fcm) {
            $pushResult = $this->fcm->sendNotification(
                [$patientUser->device_token],
                [
                    'title' => 'Video Consultation Started',
                    'body'  => 'Doctor has started your call. Tap to join.',
                    'appointment_id' => $appointment->id,
                    'type' => 'video_call'
                ]
            );
        } else {
            // Fallback to helper that uses server key (if set via FIREBASE_SERVER_KEY)
            $pushResult = \App\Helpers\FirebaseNotification::send(
                $patientUser->device_token,
                'Video Consultation Started',
                'Doctor has started your call. Tap to join.',
                ['appointment_id' => $appointment->id, 'type' => 'video_call']
            );
        }

        $notificationSent = $pushResult === true;
        if (!$notificationSent) {
            logger()->warning('Push notification not sent: Firebase credentials missing or FCM error.', ['patient_id' => $patientUser->id, 'appointment_id' => $appointment->id, 'result' => $pushResult]);
        }

        Notification::create([
            'user_id'   => $patientUser->id,
            'type'      => 'video_call',
            'title'     => 'Video Consultation Started',
            'meta_data' => json_encode([
                'appointment_id' => $appointment->id,
                'doctor_id'      => $doctorUser->id,
                'message'        => 'Doctor has started your call'
            ]),
            'sender_id' => $doctorUser->id
        ]);

        return response()->json([
            'status'  => true,
            'notification_sent' => $notificationSent,
            'message' => $notificationSent ? 'Notification sent & saved successfully' : 'Notification saved, but push not sent (Firebase not configured or error).'
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $userType = $user->user_type;

        $query = Doctor::with(['specialty']);

        if ($userType === 'doctor') {
            $query->where('id', $user->doctor_id);
        }

        // 🔍 Search filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('license_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('specialty')) {
            $query->where('specialty_id', $request->specialty);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $doctors = $query->latest()->get();
        $specialties = Specialty::where('is_active', true)->get();

        return view($userType . '.doctor.index', compact('doctors', 'specialties'));
    }


    public function create()
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $specialties = Specialty::where('is_active', true)->get();
        return view($userType.'.doctor.create', compact('specialties'));
    }


    public function store(Request $request)
    {
        $loginuser = Auth::user();
        $loginuserType = $loginuser->user_type;
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|unique:doctors',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:doctors',
            'specialty_id' => 'required|exists:specialties,id',
            'qualifications' => 'required|string',
            'consultation_fee' => 'required|numeric|min:0',
            'new_patient_fee' => 'required|numeric|min:0',
            'old_patient_fee' => 'required|numeric|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:2000',
            'emergency_contact_number' => 'nullable|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Log validation failures for debugging (do not include passwords)
            $input = $request->except(['password', 'password_confirmation']);
            logger()->info('Doctor creation validation failed', ['errors' => $validator->errors()->all(), 'input' => $input]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Log basic info about the attempt (do NOT log passwords)
        try {
            logger()->info('Admin attempting to create doctor', [
                'email' => $request->email ?? null,
                'license_number' => $request->license_number ?? null,
                'created_by' => $loginuser->id ?? null,
            ]);
        } catch (\Throwable $t) {
            // Non-fatal: do not break creation flow if logging fails
        }

        DB::beginTransaction();

        try {
            $data = $request->except('profile_image', 'password_confirmation');

            // Hash the password
            $hashedPassword = Hash::make($request->password);

            // Handle profile image
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('doctors', 'public');
            }

            // 1. First create user
            $userData = [
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'user_type' => 'doctor',
                'email_verified_at' => now(),
                'phone' => $request->phone,
                // Users table requires gender (not nullable) - set a sensible default when not provided
                'gender' => $request->gender ?? 'other',
                'age' => $request->age,
                'bio' => $request->bio,
                'address' => $request->address,
                'emergency_contact_number' => $request->emergency_contact_number,
                'alternate_contact_number' => $request->alternate_contact_number,
                'basic_medical_history' => null,
                'profile_picture' => $profileImagePath,
                'is_admin' => false,
                'status' => 'active',
            ];

            $user = User::create($userData);

            $doctorData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'phone' => $request->phone,
                'license_number' => $request->license_number,
                'specialty_id' => $request->specialty_id,
                'qualifications' => $request->qualifications,
                'consultation_fee' => $request->consultation_fee,
                'new_patient_fee' => $request->new_patient_fee,
                'old_patient_fee' => $request->old_patient_fee,
                'profile_image' => $profileImagePath,
                'user_id' => $user->id,
            ];

            $doctor = Doctor::create($doctorData);

            // Update user with doctor_id
            $user->update(['doctor_id' => $doctor->id]);

            DB::commit();

            // Send welcome mail (non-fatal). If mail fails, log error but don't rollback DB.
            try {
                Mail::to($doctor->email)->send(new DoctorWelcomeMail($doctor, $request->password));
            } catch (\Throwable $e) {
                logger()->error('Failed to send doctor welcome email: ' . $e->getMessage());
            }

            return redirect()->route($loginuserType.'.doctors.index')
                ->with('success', 'Doctor created successfully. Welcome email sent (if mail succeeded).');

        } catch (\Throwable $e) {
            DB::rollBack();
            // Log the error with full trace for debugging
            logger()->error('Doctor creation failed: ' . $e->getMessage(), ['exception' => $e]);

            // Show detailed error message when running in debug mode to help troubleshooting
            $friendly = 'An unexpected error occurred while creating the doctor. Please check the application logs or contact the system administrator.';
            $msg = config('app.debug') ? ('Error creating doctor: ' . $e->getMessage()) : $friendly;

            return redirect()->back()
                ->with('error', $msg)
                ->withInput();
        }
    }

    public function update(Request $request, Doctor $doctor)
    {
        $authUser = Auth::user();
        $userType = $authUser->user_type;

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user->id . '|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:doctors,license_number,' . $doctor->id,
            'specialty_id' => 'required|exists:specialties,id',
            'qualifications' => 'required|string',
            'consultation_fee' => 'required|numeric|min:0',
            'new_patient_fee' => 'required|numeric|min:0',
            'old_patient_fee' => 'required|numeric|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'address' => 'nullable|string|max:500',
            'emergency_contact_number' => 'nullable|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
        ];

        // Password is optional on update; validate only when provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $data = $request->except('profile_image');

            $profileImagePath = $doctor->profile_image;
            if ($request->hasFile('profile_image')) {
                if ($doctor->profile_image) {
                    Storage::disk('public')->delete($doctor->profile_image);
                }
                $profileImagePath = $request->file('profile_image')->store('doctors', 'public');
            }

            $user = $doctor->user;
            $passwordChanged = false;
            $hashedPassword = null;
            if ($request->filled('password')) {
                $hashedPassword = Hash::make($request->password);
                $passwordChanged = true;
            }

            if ($user) {
                $userData = [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    // Keep existing gender if not provided to avoid DB NOT NULL constraint violations
                    'gender' => $request->gender ?? $user->gender ?? 'other',
                    'age' => $request->age,
                    'address' => $request->address,
                    'emergency_contact_number' => $request->emergency_contact_number,
                    'alternate_contact_number' => $request->alternate_contact_number,
                    'profile_picture' => $profileImagePath,
                    
                ];

                if ($passwordChanged) {
                    $userData['password'] = $hashedPassword;
                }

                $user->update($userData);
            }

            // 2. Then update doctor with linked user_id
            $doctorData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'license_number' => $request->license_number,
                'specialty_id' => $request->specialty_id,
                'qualifications' => $request->qualifications,
                'consultation_fee' => $request->consultation_fee,
                'new_patient_fee' => $request->new_patient_fee,
                'old_patient_fee' => $request->old_patient_fee,
                'profile_image' => $profileImagePath,
                'bio' => $request->bio,
            ];

            if ($passwordChanged) {
                $doctorData['password'] = $hashedPassword;
            }

            $doctor->update($doctorData);

            // If password changed, optionally send a notification email with the new password
            if ($passwordChanged) {
                try {
                    Mail::to($doctor->email)->send(new DoctorWelcomeMail($doctor, $request->password));
                } catch (\Exception $e) {
                    logger()->error('Failed to send updated credentials to doctor: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route($userType.'.doctors.index')
                ->with('success', 'Doctor updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating doctor: ' . $e->getMessage())
                ->withInput();
        }
    }



    public function createLeave(Doctor $doctor)
    {
        $user = Auth::user();
        return view($user->user_type . '.doctor.leave-create', compact('doctor'));
    }


    public function storeLeave(Request $request, Doctor $doctor)
    {
        // Validate the request
        $validated = $request->validate([
            'leave_type' => 'required|in:sick,casual,emergency,vacation',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            // Calculate duration
            $startDate = new \DateTime($validated['start_date']);
            $endDate = new \DateTime($validated['end_date']);
            $duration = $endDate->diff($startDate)->days + 1;

            // Check for overlapping leaves
            $existingLeave = LeaveApplication::where('doctor_id', $doctor->id)
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
                return back()
                    ->withInput()
                    ->withErrors(['start_date' => 'You already have a leave application for this period.']);
            }

            // Create leave application
            $leave = LeaveApplication::create([
                'doctor_id' => $doctor->id,
                'leave_type' => $validated['leave_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration' => $duration,
                'reason' => $validated['reason'],
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Notify all admins
            $admins = \App\Models\User::where('user_type', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'leave_application',
                    'title' => 'Doctor Leave Applied',
                    'meta_data' => json_encode([
                        'leave_id' => $leave->id,
                        'doctor_id' => $doctor->id,
                        'doctor_name' => $doctor->first_name . ' ' . $doctor->last_name,
                        'leave_type' => $leave->leave_type,
                        'start_date' => $leave->start_date->format('Y-m-d'),
                        'end_date' => $leave->end_date->format('Y-m-d'),
                        'message' => "{$doctor->first_name} has applied for {$leave->leave_type} leave."
                    ]),
                    'sender_id' => $doctor->user->id
                ]);
            }

            return redirect()
                ->route('doctor.doctors.leaves', $doctor)
                ->with('success', 'Leave application submitted successfully! It will be reviewed soon.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit leave application. Please try again.']);
        }
    }

    public function show(Doctor $doctor)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $doctor->load(['specialty',  'leaveApplications', 'performances']);

        // Performance stats for last 30 days
        $performanceStats = $doctor->getPerformanceStats(now()->subDays(30), now());

        return view($userType.'.doctor.show', compact('doctor', 'performanceStats'));
    }

    public function edit(Doctor $doctor)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $specialties = Specialty::where('is_active', true)->get();
        return view($userType.'.doctor.edit', compact('doctor', 'specialties'));
    }
    // DoctorController में add करें
    public function showLeave(Doctor $doctor, LeaveApplication $leave)
    {
        // Authorization check (optional)
        if ($leave->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized');
        }

        return view('doctor.doctor.showLeave', [
            'doctor' => $doctor,
            'leave' => $leave
        ]);
    }

    // Allow doctor to edit a pending leave
    public function editLeave(Doctor $doctor, LeaveApplication $leave)
    {
        $user = Auth::user();
        if ($leave->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized');
        }
        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leaves can be edited.');
        }
        return view($user->user_type . '.doctor.leave-edit', compact('doctor', 'leave'));
    }

    public function updateLeave(Request $request, Doctor $doctor, LeaveApplication $leave)
    {
        if ($leave->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized');
        }
        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leaves can be edited.');
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:sick,casual,emergency,vacation',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        // Check for overlapping leaves excluding this leave
        $startDate = new \DateTime($validated['start_date']);
        $endDate = new \DateTime($validated['end_date']);

        $existingLeave = LeaveApplication::where('doctor_id', $doctor->id)
            ->where('id', '!=', $leave->id)
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
            return back()
                ->withInput()
                ->withErrors(['start_date' => 'You already have a leave application for this period.']);
        }

        $duration = $endDate->diff($startDate)->days + 1;

        $leave->update([
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration' => $duration,
            'reason' => $validated['reason'],
        ]);

        return redirect()
            ->route('doctor.doctors.leaves', $doctor)
            ->with('success', 'Leave application updated successfully.');
    }


    public function destroy(Doctor $doctor)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            // Delete profile image
            if ($doctor->profile_image) {
                Storage::disk('public')->delete($doctor->profile_image);
            }

            $doctor->delete();

            DB::commit();

            return redirect()->route($user->user_type.'.doctors.index')
                ->with('success', 'Doctor deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting doctor: ' . $e->getMessage());
        }
    }

    // Schedule Management Methods
    public function schedules(Doctor $doctor)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $schedules = $doctor->schedules()->orderBy('day_of_week')->get();
        return view($userType.'.doctor.schedules', compact('doctor', 'schedules'));
    }

    public function storeSchedule(Request $request, Doctor $doctor)
    {
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($daysOfWeek as $day) {
            // Check if data exists for this day
            if ($request->has("day_of_week.{$day}")) {

                // Validate data for this day
                $validator = Validator::make($request->all(), [
                    "start_time.{$day}" => 'required|date_format:H:i',
                    "end_time.{$day}" => 'required|date_format:H:i|after:start_time.{$day}',
                    "slot_duration.{$day}" => 'required|integer|min:5|max:60',
                    "max_patients.{$day}" => 'required|integer|min:1',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $isAvailable = $request->has("available.{$day}") && $request->input("available.{$day}") == 1;

                // Additional time validation
                if ($request->input("start_time.{$day}") >= $request->input("end_time.{$day}")) {
                    return redirect()->back()
                        ->with('error', "End time must be after start time for {$day}.")
                        ->withInput();
                }

                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day
                    ],
                    [
                        'start_time' => $request->input("start_time.{$day}"),
                        'end_time' => $request->input("end_time.{$day}"),
                        'slot_duration' => $request->input("slot_duration.{$day}"),
                        'max_patients' => $request->input("max_patients.{$day}"),
                        'is_available' => $isAvailable
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Schedule updated successfully.');
    }

    // Leave Management Methods
    public function leaves(Doctor $doctor)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $leaves = $doctor->leaveApplications()->latest()->paginate(10);
        return view($userType.'.doctor.leaves', compact('doctor', 'leaves'));
    }

    // Performance Tracking Methods
    public function performance(Doctor $doctor, Request $request)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Performance data
        $performances = $doctor->performances()
            ->whereBetween('performance_date', [$startDate, $endDate])
            ->orderBy('performance_date', 'desc')
            ->get();

        // Performance statistics
        $performanceStats = $doctor->getPerformanceStats($startDate, $endDate);

        // Monthly summary for charts
        $monthlySummary = $doctor->performances()
            ->selectRaw('YEAR(performance_date) as year, MONTH(performance_date) as month,
                        SUM(total_appointments) as total_appts,
                        SUM(completed_appointments) as completed_appts,
                        SUM(cancelled_appointments) as cancelled_appts,
                        AVG(average_rating) as avg_rating,
                        SUM(revenue_generated) as total_revenue')
            ->whereBetween('performance_date', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view($userType.'.doctor.performance', compact(
            'doctor',
            'performances',
            'performanceStats',
            'monthlySummary',
            'startDate',
            'endDate'
        ));
    }

    public function updateLeaveStatus(Request $request, LeaveApplication $leave)
    {

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remarks' => 'required_if:status,rejected|string|max:500'
        ]);

        try {
            $leave->update([
                'status' => $request->status,
                'admin_remarks' => $request->admin_remarks,
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Leave application ' . $request->status . ' successfully.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating leave status: ' . $e->getMessage()], 500);
        }
    }

    public function destroyLeave(LeaveApplication $leave)
    {
        try {
            $leave->delete();
            return redirect()->back()->with('success', 'Leave application deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting leave application: ' . $e->getMessage());
        }
    }

    // Api functionalty is here

    public function getSpecialties()
    {
        try {
            $specialties = Specialty::where('is_active', true)->get();

            return response()->json([
                'status' => true,
                'message' => 'Specialties retrieved successfully',
                'data' => $specialties,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve specialties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // get doctor

    public function getDoctor(Request $request)
    {
        try {
            $query = Doctor::with('specialty');

            // Search by name
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by specialty
            if ($request->has('specialty_id')) {
                $query->where('specialty_id', $request->get('specialty_id'));
            }

            // Filter by status (if you have status field)
            if ($request->has('is_active')) {
                $query->where('is_active', $request->get('is_active'));
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $doctors = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Doctors retrieved successfully',
                'data' => $doctors
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDoctorById($id)
    {
        try {
            $doctor = Doctor::with('specialty')->find($id);

            if (!$doctor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Doctor not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Doctor retrieved successfully',
                'data' => $doctor
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|unique:doctors',
            'specialty_name' => 'required|string|max:255',
            'qualifications' => 'required|string',
            'consultation_fee' => 'required|numeric|min:0',
            'new_patient_fee' => 'required|numeric|min:0',
            'old_patient_fee' => 'required|numeric|min:0',
            'bio' => 'nullable|string', // New bio field
            'experience' => 'required|integer|min:0', // New experience field (in years)
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Specialty check karein ya create karein
            $specialty = Specialty::where('name', $request->specialty_name)->first();

            if (!$specialty) {
                return response()->json([
                    'status' => false,
                    'message' => 'Specialty not found. Please provide a valid specialty name.',
                    'available_specialties' => Specialty::where('is_active', true)->pluck('name')
                ], 404);
            }

            $data = $request->except('profile_image', 'specialty_name');
            $data['specialty_id'] = $specialty->id; // ID set karein

            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image')->store('doctors', 'public');
            }

            $doctor = Doctor::create($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Doctor created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error creating doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update doctor
    public function updateDoctor(Request $request, $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:doctors,email,' . $id,
            'phone' => 'sometimes|required|string|max:20',
            'license_number' => 'sometimes|required|string|unique:doctors,license_number,' . $id,
            'specialty_name' => 'sometimes|required|string|max:255',
            'qualifications' => 'sometimes|required|string',
            'consultation_fee' => 'sometimes|required|numeric|min:0',
            'new_patient_fee' => 'sometimes|required|numeric|min:0',
            'old_patient_fee' => 'sometimes|required|numeric|min:0',
            'bio' => 'nullable|string',
            'experience' => 'sometimes|required|integer|min:0',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Specialty check karein agar specialty_name update ho rahi hai
            if ($request->has('specialty_name')) {
                $specialty = Specialty::where('name', $request->specialty_name)->first();

                if (!$specialty) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Specialty not found. Please provide a valid specialty name.',
                        'available_specialties' => Specialty::where('is_active', true)->pluck('name')
                    ], 404);
                }
                $doctor->specialty_id = $specialty->id;
            }

            $data = $request->except('profile_image', 'specialty_name');

            // Profile image update karein
            if ($request->hasFile('profile_image')) {
                // Purani image delete karein agar exist karti hai
                if ($doctor->profile_image) {
                    Storage::disk('public')->delete($doctor->profile_image);
                }
                $data['profile_image'] = $request->file('profile_image')->store('doctors', 'public');
            }

            // Doctor update karein
            $doctor->update($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Doctor updated successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error updating doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete doctor
    public function destroyDoctor($id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Profile image delete karein agar exist karti hai
            if ($doctor->profile_image) {
                Storage::disk('public')->delete($doctor->profile_image);
            }

            $doctor->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Doctor deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error deleting doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function findDoctorsOld(Request $request)
    {
        try {
            $query = Doctor::with(['specialty', 'schedules']);

            // === Search Parameters ===

            // 1. Search by name/email - IMPROVED VERSION
            if ($request->has('search')) {
                $search = trim($request->get('search'));
                $searchTerms = explode(' ', $search); // Split by space

                $query->where(function($q) use ($search, $searchTerms) {
                    // Search email
                    $q->orWhere('email', 'like', "%{$search}%");

                    // Search full name together
                    $q->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    $q->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$search}%"]);

                    // Search first_name and last_name separately
                    $q->orWhere('first_name', 'like', "%{$search}%");
                    $q->orWhere('last_name', 'like', "%{$search}%");

                    // If multiple words in search, try different combinations
                    if (count($searchTerms) > 1) {
                        $q->orWhere(function($q2) use ($searchTerms) {
                            $q2->where('first_name', 'like', "%{$searchTerms[0]}%")
                            ->where('last_name', 'like', "%{$searchTerms[1]}%");
                        });

                        $q->orWhere(function($q2) use ($searchTerms) {
                            $q2->where('first_name', 'like', "%{$searchTerms[1]}%")
                            ->where('last_name', 'like', "%{$searchTerms[0]}%");
                        });
                    }

                    // Search by specialty name
                    $q->orWhereHas('specialty', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
                });
            }

            // 2. NEW: Search only by first name
            if ($request->has('first_name')) {
                $query->where('first_name', 'like', "%{$request->first_name}%");
            }

            // 3. NEW: Search only by last name
            if ($request->has('last_name')) {
                $query->where('last_name', 'like', "%{$request->last_name}%");
            }

            // 4. NEW: Search by full name (exact format)
            if ($request->has('full_name')) {
                $fullName = trim($request->full_name);
                $query->where(function($q) use ($fullName) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$fullName}%"]);
                    $q->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$fullName}%"]);
                });
            }

            // 5. Filter by specialty id
            if ($request->has('specialty_id')) {
                $query->where('specialty_id', $request->get('specialty_id'));
            }

            // 6. Filter by specialty name (separate parameter)
            if ($request->has('specialty_name')) {
                $query->whereHas('specialty', function($q) use ($request) {
                    $q->where('name', 'like', "%{$request->specialty_name}%");
                });
            }

            // 7. Filter by consultation fee range
            if ($request->has('min_fee')) {
                $query->where('consultation_fee', '>=', $request->get('min_fee'));
            }

            if ($request->has('max_fee')) {
                $query->where('consultation_fee', '<=', $request->get('max_fee'));
            }

            // 8. Filter by experience
            if ($request->has('min_experience')) {
                $query->where('experience', '>=', $request->get('min_experience'));
            }

            if ($request->has('max_experience')) {
                $query->where('experience', '<=', $request->get('max_experience'));
            }

            // 9. Filter by availability date
            if ($request->has('availability_date')) {
                $query->whereHas('schedules', function($q) use ($request) {
                    $q->where('day', strtolower(date('l', strtotime($request->availability_date))))
                    ->where('is_available', true);
                });
            }

            // 10. Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->get('is_active'));
            }

            // 11. Filter by rating
            if ($request->has('min_rating')) {
                $query->whereHas('reviews', function($q) use ($request) {
                    $q->selectRaw('doctor_id, AVG(rating) as avg_rating')
                    ->groupBy('doctor_id')
                    ->having('avg_rating', '>=', $request->min_rating);
                });
            }

            // === Sorting ===
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $validSortColumns = ['first_name', 'last_name', 'consultation_fee', 'experience', 'created_at'];
            if (in_array($sortBy, $validSortColumns)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // === Pagination ===
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            $doctors = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform data to show specialty name instead of full object
            $transformedDoctors = $doctors->getCollection()->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'first_name' => $doctor->first_name,
                    'last_name' => $doctor->last_name,
                    'full_name' => $doctor->first_name . ' ' . $doctor->last_name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'experience' => $doctor->experience,
                    'consultation_fee' => $doctor->consultation_fee,
                    'is_active' => $doctor->is_active,
                    'specialty' => $doctor->specialty ? $doctor->specialty->name : null,
                    'specialty_id' => $doctor->specialty_id,
                    'created_at' => $doctor->created_at,
                    'updated_at' => $doctor->updated_at,
                    // Add other fields as needed
                ];
            });

            // Create custom pagination response
            $response = [
                'status' => true,
                'message' => 'Doctors retrieved successfully',
                'data' => [
                    'doctors' => $transformedDoctors,
                    'pagination' => [
                        'current_page' => $doctors->currentPage(),
                        'per_page' => $doctors->perPage(),
                        'total' => $doctors->total(),
                        'last_page' => $doctors->lastPage(),
                        'from' => $doctors->firstItem(),
                        'to' => $doctors->lastItem()
                    ]
                ]
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function findDoctors(Request $request)
    {
        try {
            $query = Doctor::with(['specialty']);

            // 1. General search (name/email)
            if ($request->has('search')) {
                $search = trim($request->get('search'));
                $searchTerms = explode(' ', $search);

                $query->where(function($q) use ($search, $searchTerms) {
                    // Email
                    $q->orWhere('email', 'like', "%{$search}%");

                    // Full name combinations
                    $q->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    $q->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$search}%"]);

                    // First name / Last name separately
                    $q->orWhere('first_name', 'like', "%{$search}%");
                    $q->orWhere('last_name', 'like', "%{$search}%");

                    // Multiple words search
                    if (count($searchTerms) > 1) {
                        $q->orWhere(function($q2) use ($searchTerms) {
                            $q2->where('first_name', 'like', "%{$searchTerms[0]}%")
                            ->where('last_name', 'like', "%{$searchTerms[1]}%");
                        });

                        $q->orWhere(function($q2) use ($searchTerms) {
                            $q2->where('first_name', 'like', "%{$searchTerms[1]}%")
                            ->where('last_name', 'like', "%{$searchTerms[0]}%");
                        });
                    }
                });
            }

            // 2. Search only by first name
            if ($request->has('first_name')) {
                $query->where('first_name', 'like', "%{$request->first_name}%");
            }

            // 3. Search only by last name
            if ($request->has('last_name')) {
                $query->where('last_name', 'like', "%{$request->last_name}%");
            }

            // 4. Search by full name
            if ($request->has('full_name')) {
                $fullName = trim($request->full_name);
                $query->where(function($q) use ($fullName) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$fullName}%"]);
                    $q->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$fullName}%"]);
                });
            }

            // 5. Filter by specialty id
            if ($request->has('specialty_id')) {
                $query->where('specialty_id', $request->get('specialty_id'));
            }

            // 6. Filter by consultation fee range
            if ($request->has('min_fee')) {
                $query->where('consultation_fee', '>=', $request->get('min_fee'));
            }
            if ($request->has('max_fee')) {
                $query->where('consultation_fee', '<=', $request->get('max_fee'));
            }

            // 7. Filter by experience
            if ($request->has('min_experience')) {
                $query->where('experience', '>=', $request->get('min_experience'));
            }
            if ($request->has('max_experience')) {
                $query->where('experience', '<=', $request->get('max_experience'));
            }

            // 8. Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->get('is_active'));
            }

            // === Sorting ===
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $validSortColumns = ['first_name', 'last_name', 'consultation_fee', 'experience', 'created_at'];

            if (in_array($sortBy, $validSortColumns)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // === Pagination ===
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            $doctors = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform data
            $transformedDoctors = $doctors->getCollection()->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'first_name' => $doctor->first_name,
                    'last_name' => $doctor->last_name,
                    'full_name' => $doctor->first_name . ' ' . $doctor->last_name,
                    'email' => $doctor->email,
                    'phone' => $doctor->phone,
                    'experience' => $doctor->experience,
                    'consultation_fee' => $doctor->consultation_fee,
                    'is_active' => $doctor->is_active,
                    'specialty' => $doctor->specialty ? $doctor->specialty->name : null,
                    'specialty_id' => $doctor->specialty_id,
                    'created_at' => $doctor->created_at,
                    'updated_at' => $doctor->updated_at,
                ];
            });

            $response = [
                'status' => true,
                'message' => 'Doctors retrieved successfully',
                'data' => [
                    'doctors' => $transformedDoctors,
                ]
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve doctors',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
