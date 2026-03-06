<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\Resource;
use App\Models\PatientNotify;
use App\Models\ChatConversation;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ChatAssignment;
use App\Models\QueueManagement;
use App\Models\DateSlot;
use App\Models\DoctorSchedule;
use App\Services\AgoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\LinkPreviewService;

class AppointmentController extends Controller
{

    public function generateToken(Request $request)
    {
        $appID = config('services.agora.key');
        $appCertificate = config('services.agora.secret');

        $channelName = $request->channel_name; // frontend se ayega
        $uid = $request->uid ?? 0; // 0 ka matlab auto uid
        $role = RtcTokenBuilder::RolePublisher;

        $expireTimeInSeconds = 3600; // 1 hour
        $currentTimestamp = now()->timestamp;
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appID,
            $appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpiredTs
        );

        return response()->json([
            'token' => $token,
            'channel_name' => $channelName,
            'uid' => $uid
        ]);
    }


    public function index(Request $request)
    {

        $view = $request->get('view', 'month');
        $date = $request->get('date', date('Y-m-d'));

        $user = Auth::user();


        $appointments = Appointment::with(['doctor', 'resource', 'patient', 'conversation'])
            ->when($user->user_type === 'doctor', function ($query) use ($user) {
                // doctor sirf apne appointments dekhe
                // return $query->where('doctor_id', $user->doctor_id);
                return $query->where('doctor_id', Doctor::where('user_id', $user->id)->first()->id);
                // agar users table me doctor_id nahi hai:
                // return $query->where('doctor_id', $user->id);
            })
            ->when($view == 'day', function ($query) use ($date) {
                return $query->where('appointment_date', $date);
            })
            ->when($view == 'week', function ($query) use ($date) {
                $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($date)));
                $endOfWeek   = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
                return $query->whereBetween('appointment_date', [$startOfWeek, $endOfWeek]);
            })
            ->when($view == 'month', function ($query) use ($date) {
                $startOfMonth = date('Y-m-01', strtotime($date));
                $endOfMonth   = date('Y-m-t', strtotime($date));
                return $query->whereBetween('appointment_date', [$startOfMonth, $endOfMonth]);
            })
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();
        $doctors   = Doctor::where('status', 'active')->get();
        $resources = Resource::where('is_available', true)->get();
        $patients  = Patient::where('is_active', 1)->get();

        $user_type = $user->user_type; // 'admin', 'doctor', 'staff'

        $viewPath = in_array($user_type, ['admin', 'doctor', 'staff'])
            ? $user_type . '.appointment.index'
            : 'admin.appointment.index';


        return view($viewPath, compact(
            'appointments',
            'doctors',
            'resources',
            'view',
            'date',
            'patients'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $userType = $user->user_type;

        $patients = Patient::all();
        $doctors = Doctor::where('status', 'active')->get();
        $resources = Resource::where('is_available', true)->get();
        
        $currentDoctor = null;
        if ($userType === 'doctor') {
            $currentDoctor = Doctor::where('user_id', $user->id)->first();
        }
        
        return view($userType . '.appointment.create', compact('doctors', 'resources', 'patients', 'currentDoctor'));
    }

    /**
     * Get available dates for a doctor
     */
    public function getDoctorDates(Request $request)
    {
        try {
            $doctorId = $request->query('doctor_id');

            if (!$doctorId) {
                return response()->json(['error' => 'Doctor ID is required'], 400);
            }

            // Get weekly schedule days (fallback)
            $weeklyDays = DoctorSchedule::where('doctor_id', $doctorId)
                ->where('is_available', true)
                ->pluck('day_of_week')
                ->map(fn($day) => strtolower($day))
                ->toArray();

            // Get specific DateSlot overrides (both available and potentially blocked)
            // We'll trust DateSlot existence as the primary source of truth for those dates
            $dateSlots = DateSlot::where('doctor_id', $doctorId)
                ->where('slot_date', '>=', now()->toDateString())
                ->where('slot_date', '<=', now()->addDays(60)->toDateString())
                ->get()
                ->keyBy('slot_date'); // Key by Y-m-d

            $availableDates = [];
            $startDate = now();
            $endDate = now()->addDays(60);

            $current = $startDate->copy();
            while ($current <= $endDate) {
                $dateStr = $current->toDateString();
                $dayName = strtolower($current->format('l'));
                $isAvailable = false;

                // Priority 1: Check DateSlot
                if ($dateSlots->has($dateStr)) {
                    $slot = $dateSlots->get($dateStr);
                    // If DateSlot exists, use its availability status
                    // Note: If DateSlot exists, it usually means it was generated/configured.
                    // If is_available is false, it's blocked.
                    if ($slot->is_available) {
                         $isAvailable = true;
                    }
                } 
                // Priority 2: Fallback to Weekly Schedule (if no DateSlot entry exists)
                // Assuming absence of DateSlot means "follow weekly pattern"
                else {
                    if (in_array($dayName, $weeklyDays)) {
                        $isAvailable = true;
                    }
                }

                if ($isAvailable) {
                    $availableDates[] = [
                        'date' => $dateStr,
                        'formatted' => $current->format('d M Y, l'),
                        'day' => $current->format('l')
                    ];
                }
                $current->addDay();
            }

            return response()->json(['dates' => $availableDates]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage(), 'message' => 'Failed to load dates'], 500);
        }
    }

    public function getDoctorSlots(Request $request)
    {
        try {
            $doctorId = $request->query('doctor_id');
            $appointmentDate = $request->query('date');
            $appointmentId = $request->query('appointment_id'); 

            if (!$doctorId || !$appointmentDate) {
                return response()->json(['error' => 'Doctor ID and date are required'], 400);
            }

            // check if date is today
            $isToday = \Carbon\Carbon::parse($appointmentDate)->isToday();
            $now = \Carbon\Carbon::now();

            // 1. Try to find specific DateSlot
            $dateSlot = DateSlot::where('doctor_id', $doctorId)
                ->where('slot_date', $appointmentDate)
                ->first();

            $slots = [];

            if ($dateSlot) {
                // Use the specific slots from DB
                if (!$dateSlot->is_available) {
                     return response()->json(['slots' => [], 'message' => 'Doctor not available on this date']);
                }

                $rawSlots = $dateSlot->time_slots ?? []; // Array of ['start', 'end', 'available', 'booked']

                foreach ($rawSlots as $rawSlot) {
                    $startStr = $rawSlot['start'];
                    $endStr = $rawSlot['end'];
                    
                     // Convert to Carbon for comparison
                    $startCarbon = \Carbon\Carbon::parse($appointmentDate . ' ' . $startStr);
                    // $endCarbon = \Carbon\Carbon::parse($appointmentDate . ' ' . $endStr);

                    // Filter past slots if today
                    if ($isToday && $startCarbon->lt($now)) {
                         continue; 
                    }

                    // Check real-time bookings (ignoring the 'booked' count in JSON if unreliable)
                    $bookingsCount = Appointment::where('doctor_id', $doctorId)
                        ->where('appointment_date', $appointmentDate)
                         ->where(function($query) use ($startStr, $endStr) {
                                // Overlap check: (StartA < EndB) and (EndA > StartB)
                                $query->where('start_time', '<', $endStr)
                                      ->where('end_time', '>', $startStr);
                         })
                        ->where('status', '!=', 'cancelled') // Important: ignore cancelled
                        ->when($appointmentId, function($q) use ($appointmentId) {
                            $q->where('id', '!=', $appointmentId);
                        })
                        ->count();

                    // Determine availability
                    // rawSlot['available'] is the capacity (max patients for this slot)
                    $capacity = isset($rawSlot['available']) ? (int)$rawSlot['available'] : 1;
                    $isAvailable = $bookingsCount < $capacity;

                    if ($isAvailable) {
                        $slots[] = [
                            'start' => $startStr,
                            'end' => $endStr,
                            'display' => \Carbon\Carbon::parse($startStr)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($endStr)->format('h:i A'),
                            'available' => $isAvailable
                        ];
                    }
                }

            } else {
                // 2. Fallback to Weekly Schedule Logic
                $dayOfWeek = strtolower(\Carbon\Carbon::parse($appointmentDate)->format('l'));
                $schedule = DoctorSchedule::where('doctor_id', $doctorId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_available', true)
                    ->first();

                if (!$schedule) {
                    return response()->json(['slots' => [], 'message' => 'Doctor not available on this day']);
                }

                $startTime = \Carbon\Carbon::parse($schedule->start_time);
                $endTime = \Carbon\Carbon::parse($schedule->end_time);
                $slotDuration = $schedule->slot_duration ?? 15;
                $current = $startTime->copy();

                while ($current < $endTime) {
                    $slotStartStr = $current->format('H:i');
                    
                    // Filter past slots
                     // Check if slot start time is in the past (for today)
                    $slotStartDateTime = \Carbon\Carbon::parse($appointmentDate . ' ' . $slotStartStr);

                    if ($isToday && $slotStartDateTime->lt($now)) {
                         $current->addMinutes($slotDuration);
                         continue;
                    }

                    $slotEnd = $current->copy()->addMinutes($slotDuration);
                    
                    if ($slotEnd <= $endTime) {
                         $slotEndStr = $slotEnd->format('H:i');
                         
                         // Check bookings
                         $bookingsCount = Appointment::where('doctor_id', $doctorId)
                            ->where('appointment_date', $appointmentDate)
                            ->where(function($query) use ($slotStartStr, $slotEndStr) {
                                $query->where('start_time', '<', $slotEndStr)
                                      ->where('end_time', '>', $slotStartStr);
                            })
                            ->where('status', '!=', 'cancelled')
                            ->when($appointmentId, function($q) use ($appointmentId) {
                                $q->where('id', '!=', $appointmentId);
                            })
                            ->count();
                        
                        $capacity = $schedule->max_patients ?? 1; 
                        
                        $isAvailable = $bookingsCount < ($schedule->max_patients ?? 1);

                        if ($isAvailable) {
                            $slots[] = [
                                'start' => $slotStartStr,
                                'end' => $slotEndStr,
                                'display' => $current->format('h:i A') . ' - ' . $slotEnd->format('h:i A'),
                                'available' => $isAvailable
                            ];
                        }
                    }
                    $current->addMinutes($slotDuration);
                }
            }

            return response()->json(['slots' => $slots]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage(), 'message' => 'Failed to load slots'], 500);
        }
    }

    // public function start($id)
    // {
    //     $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($id);
    //     $user = Auth::user();

    //     // 🔹 YAHAN CHANNEL NAME BANEGA
    //     $channelName = 'appointment_' . $appointment->id;

    //     $appID = config('services.agora.key');
    //     $appCertificate = config('services.agora.secret');

    //     if (!$appID || !$appCertificate) {
    //         return redirect()->back()->with('error', 'Video calling is not configured. Please contact administrator.');
    //     }

    //     $uid = $user->id; // doctor ka uid
    //     $role = 1;

    //     $expireTimeInSeconds = 3600;
    //     $privilegeExpiredTs = now()->timestamp + $expireTimeInSeconds;

    //     $token = RtcTokenBuilder::buildTokenWithUid(
    //         $appID,
    //         $appCertificate,
    //         $channelName,
    //         $uid,
    //         $role,
    //         $privilegeExpiredTs
    //     );

    //     // Notify patient that doctor has started the call (if patient has a device token)
    //     try {
    //         $patient = $appointment->patient;
    //         if ($patient && isset($patient->email)) {
    //             $patientUser = \App\Models\User::where('email', $patient->email)->first();
    //             if ($patientUser && $patientUser->device_token) {
    //                 $projectId = config('services.firebase.project_id');
    //                 $credentialsPath = public_path(config('services.firebase.credentials_path'));
    //                 $fcm = new \App\Services\FirebaseService($projectId, $credentialsPath);

    //                 $fcm->sendNotification([
    //                     $patientUser->device_token
    //                 ], [
    //                     'title' => 'Video Consultation started',
    //                     'body' => 'Your doctor has started the video consultation. Tap to join.',
    //                     'appointment_id' => $appointment->id,
    //                     'channel_name' => $channelName,
    //                     'type' => 'video_call'
    //                 ]);

    //                 // Persist notification for patient
    //                 \App\Models\Notification::create([
    //                     'user_id' => $patientUser->id,
    //                     'type' => 'video_call',
    //                     'title' => 'Video Consultation started',
    //                     'meta_data' => json_encode(['appointment_id' => $appointment->id, 'channel_name' => $channelName]),
    //                     'sender_id' => auth()->id(),
    //                 ]);
    //             }
    //         }
    //     } catch (\Throwable $e) {
    //         // don't block call due to notification failures
    //         logger()->error('Error sending call notification: ' . $e->getMessage());
    //     }

    //     return view('doctor.appointment.call', compact(
    //         'appointment',
    //         'channelName',
    //         'token',
    //         'uid',
    //         'appID'
    //     ));
    // }


    public function start($id)
    {
        $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($id);
        $user = Auth::user();

        // ✅ Only the assigned doctor can start the call
        // $doctorModel = Doctor::where('user_id', $user->id)->first();
        // if (! $doctorModel || $doctorModel->id != $appointment->doctor_id) {
        //     return redirect()->back()->with('error', 'Unauthorized to start this call.');
        // }

        // ✅ Ensure this is a video appointment
        // if ($appointment->type !== 'video') {
        //     return redirect()->back()->with('error', 'This appointment is not scheduled for a video call.');
        // }

        // ✅ Allow only during the appointment time slot (with a small buffer)
        // $bufferMinutes = 10; // allow a 10 minute buffer before/after
        // $start = \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->start_time);
        // $end = \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->end_time);
        // $startWindow = $start->copy()->subMinutes($bufferMinutes);
        // $endWindow = $end->copy()->addMinutes($bufferMinutes);
        // $now = now();

        // if ($now->lt($startWindow) || $now->gt($endWindow)) {
        //     return redirect()->back()->with('error', 'Video calls can only be started within the appointment time slot.');
        // }

        // 🔹 Create video call record AFTER validations
        $agoraService = new AgoraService();
        $videoCall = $agoraService->createVideoCall($appointment->id, $user->id);

        $channelName = $videoCall->channel_name;
        $token = $videoCall->token;

        // ✅ Yeh line add karna hai - Agora App ID get karo
        $appID = config('services.agora.app_id'); // Changed from 'key' to 'app_id'

        // Agar config 'app_id' nahi hai to 'key' try karo
        if (!$appID) {
            $appID = config('services.agora.key');
        }

        $appCertificate = config('services.agora.secret'); // Ya 'certificate'

        if (!$appID || !$appCertificate) {
            return redirect()->back()->with('error', 'Video calling is not configured. Please contact administrator.');
        }

        // 🔔 Notify patient via FCM (and always store a Notification record if the user exists)
        try {
            $patient = $appointment->patient;
            if ($patient && isset($patient->email)) {
                $patientUser = \App\Models\User::where('email', $patient->email)->first();

                $notificationMeta = [
                    'appointment_id' => $appointment->id,
                    'call_id' => $videoCall->id,
                    'channel_name' => $channelName
                ];

                if ($patientUser) {
                    // Send push if device token exists
                    if ($patientUser->device_token) {
                        $projectId = config('services.firebase.project_id');
                        $credentialsPath = public_path(config('services.firebase.credentials_path'));
                        $fcm = new \App\Services\FirebaseService($projectId, $credentialsPath);

                        $fcm->sendNotification([
                            $patientUser->device_token
                        ], [
                            'title' => 'Video Consultation Started',
                            'body' => 'Your doctor has started the video consultation. Tap to join.',
                            'appointment_id' => $appointment->id,
                            'call_id' => $videoCall->id,
                            'channel_name' => $channelName,
                            'type' => 'video_call_started'
                        ]);
                    }

                    // Store notification for in-app view even if push not sent
                    \App\Models\Notification::create([
                        'user_id' => $patientUser->id,
                        'type' => 'video_call_started',
                        'title' => 'Video Consultation Started',
                        'meta_data' => json_encode($notificationMeta),
                        'sender_id' => auth()->id(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Error sending call notification: ' . $e->getMessage());
        }

        $uid = $user->id; // Doctor ka UID

        // ✅ Compact mein $appID add karo
        return view('doctor.appointment.call', compact(
            'appointment',
            'channelName',
            'token',
            'videoCall',
            'appID' ,
            'uid'
        ));
    }


    /**
     * End a video call via web (doctor UI). Accepts POST { call_id } and marks call completed and notifies patient.
     */
    public function endCall(Request $request)
    {
        $request->validate([
            'call_id' => 'required|exists:video_calls,id'
        ]);

        $user = Auth::user();

        $callId = $request->call_id;
        $videoCall = \App\Models\VideoCall::find($callId);

        if (! $videoCall) {
            return response()->json(['status' => false, 'message' => 'Call not found'], 404);
        }

        $appointment = $videoCall->appointment;

        // Ensure the doctor owns this appointment
        $doctorModel = Doctor::where('user_id', $user->id)->first();
        if (! $doctorModel || $doctorModel->id !== $appointment->doctor_id) {
            return response()->json(['status' => false, 'message' => 'Access denied'], 403);
        }

        $startedAt = $videoCall->started_at;
        $endedAt = now();
        $duration = $startedAt ? $endedAt->diffInSeconds($startedAt) : 0;

        $videoCall->update([
            'status' => 'completed',
            'ended_at' => $endedAt,
            'duration' => $duration
        ]);

        // Notify patient via FCM and persist Notification
        try {
            $patientUser = $appointment->patient->user ?? null;
            if ($patientUser) {
                $projectId = config('services.firebase.project_id');
                $credentialsPath = public_path(config('services.firebase.credentials_path'));

                try {
                    if ($patientUser->device_token) {
                        $fcm = new \App\Services\FirebaseService($projectId, $credentialsPath);
                        $fcm->sendNotification([$patientUser->device_token], [
                            'title' => 'Video Consultation Ended',
                            'body' => 'Doctor ended the call',
                            'call_id' => $videoCall->id,
                            'appointment_id' => $videoCall->appointment_id,
                            'type' => 'video_call_ended'
                        ]);
                    }
                } catch (\Throwable $e) {
                    if (! empty($patientUser->device_token)) {
                        \App\Helpers\FirebaseNotification::send($patientUser->device_token, 'Video Consultation Ended', 'Doctor ended the call', ['call_id' => $videoCall->id, 'appointment_id' => $videoCall->appointment_id, 'type' => 'video_call_ended']);
                    }
                }

                \App\Models\Notification::create([
                    'user_id' => $patientUser->id,
                    'type' => 'video_call_ended',
                    'title' => 'Video Consultation Ended',
                    'meta_data' => json_encode(['call_id' => $videoCall->id, 'appointment_id' => $videoCall->appointment_id]),
                    'sender_id' => $user->id,
                ]);
            }
        } catch (\Throwable $e) {
            logger()->error('Error notifying patient on call end: ' . $e->getMessage());
        }

        return response()->json(['status' => true, 'message' => 'Call ended successfully']);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userType = $user->user_type;

        if ($userType === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if ($doctor) {
                $request->merge(['doctor_id' => $doctor->id]);
            }
        }

        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'patient_id' => 'nullable|exists:patients,id',
        ]);

        DB::beginTransaction();

        try {
            $appointment = new Appointment($request->all());

            if ($appointment->hasConflicts()) {
                return redirect()->back()
                    ->with('error', 'Time slot is not available. Please choose a different time.')
                    ->withInput();
            }

            $appointment->save();

            $appointment->queue_number = $appointment->generateQueueNumber();
            $appointment->save();

            // Create queue entry
            $this->createQueueFromAppointment($appointment);

            // Get patient_id from request
            $patientId = $request->patient_id;

            // Create chat conversation
            if ($patientId) {
                $doctorModel = Doctor::find($request->doctor_id);
                if ($doctorModel && $doctorModel->user_id) {
                    $conversationId = ChatConversation::generateConversationId($patientId);
                    $conversation = ChatConversation::create([
                        'conversation_id' => $conversationId,
                        'patient_id' => $patientId,
                        'user_id' => $doctorModel->user_id,
                        'doctor_id' => $doctorModel->id,
                        'appointment_id' => $appointment->id,
                        'status' => 'active',
                        'priority' => 'medium',
                        'last_message_at' => now(),
                    ]);

                    ChatAssignment::create([
                        'conversation_id' => $conversationId,
                        'assigned_to' => $doctorModel->user_id,
                        'assigned_by' => $patientId,
                        'assigned_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route($userType . '.appointments.index')
                ->with('success', 'Appointment scheduled successfully and added to queue.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error scheduling appointment: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Add this method to create queue from appointment
    private function createQueueFromAppointment($appointment)
    {
        // Calculate position
        $position = QueueManagement::where('doctor_id', $appointment->doctor_id)
            ->whereDate('created_at', today())
            ->where('status', 'waiting')
            ->count() + 1;

        // Calculate estimated wait time
        $doctor = Doctor::find($appointment->doctor_id);
        $consultationTime = 15; // default 15 minutes

        if ($doctor->average_consultation_time) {
            $time = explode(':', $doctor->average_consultation_time);
            $minutes = ($time[0] * 60) + $time[1] + ($time[2] / 60);
            $consultationTime = round($minutes);
        }

        $estimatedWaitTime = $position * $consultationTime;

        // Create queue entry
        $queue = QueueManagement::create([
            'queue_number' => $appointment->queue_number,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
            'queue_type' => 'normal', // default
            'reason_for_visit' => $appointment->notes,
            'check_in_time' => now(),
            'position' => $position,
            'estimated_wait_time' => $estimatedWaitTime,
            'status' => 'waiting'
        ]);

        // Calculate priority score
        $queue->priority_score = $queue->calculatePriority();
        $queue->save();

        return $queue;
    }



    public function show(Appointment $appointment)
    {
        $user = Auth::user();
        $userType = $user->user_type;
        // Load payments/invoices for admin view
        $appointment->load(['patient','doctor','resource','payments','invoices']);
        return view($userType . '.appointment.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $user = Auth::user();
        $userType = $user->user_type;

        $patients = Patient::all();
        $doctors = Doctor::where('status', 'active')->get();
        $resources = Resource::where('is_available', true)->get();
        
        $currentDoctor = null;
        if ($userType === 'doctor') {
            $currentDoctor = Doctor::where('user_id', $user->id)->first();
        }
        
        return view($userType . '.appointment.edit', compact('appointment', 'doctors', 'resources', 'patients', 'currentDoctor'));
    }

    public function update(Request $request, Appointment $appointment)
    {

        $user = Auth::user();
        $userType = $user->user_type;

        if ($userType === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if ($doctor) {
                $request->merge(['doctor_id' => $doctor->id]);
            }
        }

        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',

            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:scheduled,confirmed,cancelled,completed',
            'patient_id' => 'nullable|exists:patients,id',
        ]);

        DB::beginTransaction();

        try {
            $appointment->fill($request->all());

            // Conflict resolution (excluding current appointment)
            if ($appointment->hasConflicts()) {
                return redirect()->back()
                    ->with('error', 'Time slot is not available. Please choose a different time.')
                    ->withInput();
            }

            $appointment->save();
            if ($appointment->status === 'confirmed') {
                // Notify patient on appointment confirmation
                PatientNotify::create([
                    'patient_id' => $appointment->patient_id,
                    'title' => 'Appointment Confirmed',
                    'message' => 'Your appointment on ' . $appointment->appointment_date->format('d M Y') . ' with Dr. ' . ($appointment->doctor->name ?? 'N/A') . ' has been confirmed.',
                ]);
            }
            DB::commit();

            return redirect()->route($userType . '.appointments.index')
                ->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating appointment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();
        $userType = $user->user_type;

        $appointment->delete();
        return redirect()->route($userType . '.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    // AJAX methods for calendar
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
        ]);

        $slots = Appointment::getAvailableSlots($request->doctor_id, $request->date);
        return response()->json($slots);
    }

    public function getAvailableResources(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $resources = Resource::where('is_available', true)
            ->get()
            ->filter(function ($resource) use ($request) {
                return $resource->isAvailable($request->date, $request->start_time, $request->end_time);
            });

        return response()->json($resources);
    }

    // Quick status update
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,cancelled,completed'
        ]);

        $appointment->update(['status' => $request->status]);

        // Notify patient based on status
        $message = '';
        switch ($request->status) {
            case 'confirmed':
                $message = 'Your appointment on ' . $appointment->appointment_date->format('d M Y') . ' with Dr. ' . ($appointment->doctor->name ?? 'N/A') . ' has been confirmed.';
                break;
            case 'cancelled':
                $message = 'Your appointment on ' . $appointment->appointment_date->format('d M Y') . ' has been cancelled.';
                break;
            case 'completed':
                $message = 'Your appointment on ' . $appointment->appointment_date->format('d M Y') . ' has been completed.';
                break;
        }

        if ($message) {
            PatientNotify::create([
                'patient_id' => $appointment->patient_id,
                'title' => 'Appointment ' . ucfirst($request->status),
                'message' => $message,
            ]);
        }

        return response()->json(['success' => true]);
    }



    public function calendar()
    {
        $user = Auth::user();
        $userType = $user->user_type;

        $appointmentQuery = Appointment::with(['doctor', 'patient', 'resource']);

        if ($userType === 'doctor') {
            $appointmentQuery->where('doctor_id', $user->doctor_id);
        }

        $appointments = $appointmentQuery->get();
        $statsQuery = Appointment::query();

        if ($userType === 'doctor') {
            $statsQuery->where('doctor_id', $user->doctor_id);
        }

        $stats = [
            'scheduled' => (clone $statsQuery)->where('status', 'scheduled')->count(),
            'confirmed' => (clone $statsQuery)->where('status', 'confirmed')->count(),
            'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
        ];


        return view($userType . '.appointment.calendar', compact('appointments', 'stats'));
    }


    //  Create API

    public function getResources()
    {
        try {
            $resources = Resource::where('is_available', true)->get();

            return response()->json([
                'status' => true,
                'message' => 'Resources retrieved successfully',
                'data' => $resources,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve Resources',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllAppointments(Request $request)
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to access appointments.'
            ], 401);
        }

        try {
            // Try to find a Patient linked to the authenticated user
            $patient = Patient::where('user_id', $user->id)->first();

            $query = Appointment::with(['doctor', 'patient', 'resource'])
                ->orderBy('appointment_date', 'desc');

            if ($patient) {
                // Return only this patient's appointments
                $query->where('patient_id', $patient->id);
            } else {
                // Allow admins and staff to fetch all appointments; otherwise deny
                if (! (isset($user->user_type) && in_array($user->user_type, ['admin', 'staff']))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No patient record linked to authenticated user.'
                    ], 403);
                }
            }

            $appointments = $query->get();

            return response()->json([
                'success' => true,
                'data' => $appointments,
                'message' => $patient ? 'Patient appointments retrieved successfully' : 'Appointments retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAppointmentById($id)
    {
        try {
            $appointment = Appointment::with(['doctor', 'patient', 'resource'])->find($id);

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $appointment,
                'message' => 'Appointment retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createAppointment(Request $request)
    {
        // Normalize time format - convert H:i:s to H:i if needed (handles both 2:00:00 and 14:00:00)
        if ($request->has('start_time') && preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $request->start_time)) {
            // Extract H:i from H:i:s by removing the :ss part (everything after the last colon)
            $lastColonPos = strrpos($request->start_time, ':');
            $request->merge(['start_time' => substr($request->start_time, 0, $lastColonPos)]);
        }
        if ($request->has('end_time') && preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $request->end_time)) {
            // Extract H:i from H:i:s by removing the :ss part (everything after the last colon)
            $lastColonPos = strrpos($request->end_time, ':');
            $request->merge(['end_time' => substr($request->end_time, 0, $lastColonPos)]);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'resource_id' => 'nullable|exists:resources,id',
            'notes' => 'nullable|string',
            'type' => 'nullable|string|in:person,video',
            'patient_type' => 'nullable|string|in:new,old',
            'fee' => 'nullable|numeric',
            'payment_id' => 'nullable|exists:payments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        $authUser = null;
        try {
            $authUser = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to access appointments.'
            ], 401);
        }

        try {
            // Authenticated user (if any) - prefer JWT token (like ProfileController)
            // If caller is an authenticated user, try to map to a Patient record
            $authPatient = null;
            if ($authUser) {
                $authPatient = Patient::where('user_id', $authUser->id)->first();

                // If the authenticated user is a patient but has no Patient record, auto-create one
                if (! $authPatient && isset($authUser->user_type) && $authUser->user_type === 'patient') {
                    try {
                        $nameParts = explode(' ', trim($authUser->name ?? ''));
                        $first = $nameParts[0] ?? null;
                        $last = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;

                        $authPatient = Patient::create([
                            'user_id' => $authUser->id,
                            'first_name' => $first,
                            'last_name' => $last,
                            'email' => $authUser->email ?? null,
                            'phone' => $authUser->phone ?? null,
                            'is_active' => 1,
                        ]);
                    } catch (\Throwable $ex) {
                        // If auto-create fails, leave authPatient as null and require explicit patient info
                        logger()->warning('Auto-creating Patient failed for user id ' . ($authUser->id ?? 'unknown') . ': ' . $ex->getMessage());
                    }
                }
            }

            // If caller is NOT an authenticated patient (no linked Patient record), require patient_id or patient_name
            if (! $authPatient) {
                if (! $request->has('patient_id') && ! $request->patient_name) {
                    $msg = 'Patient information is required. Provide patient_id or patient_name, or authenticate as a patient.';
                    if ($authUser) {
                        $msg = 'Authenticated user has no linked patient record. Provide patient_id or patient_name, or create a patient record for this user.';
                    }
                    return response()->json([
                        'success' => false,
                        'message' => $msg
                    ], 422);
                }
            }

            // Resolve doctor by id
            $doctor = Doctor::find($request->doctor_id);

            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found'
                ], 404);
            }

            // Resolve patient: prefer authenticated patient record -> patient_id -> patient_name
            $patientId = null;
            $patientNameForResponse = null;

            if ($authPatient) {
                $patientId = $authPatient->id;
                $patientNameForResponse = ($authPatient->first_name ?? null) ? ($authPatient->first_name . ' ' . $authPatient->last_name) : ($authPatient->name ?? null);

                // Prevent authenticated patient users from creating appointments for another patient
                if ($request->has('patient_id') && $request->patient_id != $patientId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to create appointment for another patient'
                    ], 403);
                }
            } else {
                if ($request->has('patient_id')) {
                    $p = Patient::find($request->patient_id);
                    if (!$p) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Patient not found'
                        ], 404);
                    }
                    $patientId = $p->id;
                    $patientNameForResponse = ($p->first_name ?? null) ? ($p->first_name . ' ' . $p->last_name) : ($p->name ?? null);
                } elseif ($request->patient_name) {
                    $p = Patient::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $request->patient_name)
                        ->orWhere('name', $request->patient_name)
                        ->first();

                    if (!$p) {
                        // fallback: try to find a user and map to patient
                        $u = User::where('name', $request->patient_name)->first();
                        if ($u) {
                            $p = Patient::where('user_id', $u->id)->first();
                        }
                    }

                    if (!$p) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Patient not found'
                        ], 404);
                    }

                    $patientId = $p->id;
                    $patientNameForResponse = ($p->first_name ?? null) ? ($p->first_name . ' ' . $p->last_name) : ($p->name ?? null);
                }
            }

            // Get resource ID from id or name if provided
            $resourceId = null;
            if ($request->has('resource_id') && $request->resource_id) {
                $resource = Resource::find($request->resource_id);
                if (!$resource) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found'
                    ], 404);
                }
                $resourceId = $resource->id;
            } elseif ($request->resource_name) {
                $resource = Resource::where('name', $request->resource_name)->first();
                if (!$resource) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found'
                    ], 404);
                }
                $resourceId = $resource->id;
            }

            // Convert time format from H:i to H:i:s for database storage
            $startTime = $request->start_time . ':00';
            $endTime = $request->end_time . ':00';

            $appointmentData = [
                'doctor_id' => $doctor->id,
                'appointment_date' => $request->appointment_date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'patient_id' => $patientId,
                'patient_type' => $request->patient_type,
                'consultation_fee' => $request->fee,
                'resource_id' => $resourceId,
                'notes' => $request->notes,
                'type' => $request->type // Added type field
            ];

            $appointment = new Appointment($appointmentData);

            // Conflict resolution
            if ($appointment->hasConflicts()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Time slot is not available. Please choose a different time.'
                ], 409);
            }

            $appointment->save();

            // If payment_id provided, attach/link it
            if ($request->has('payment_id') && $request->payment_id) {
                $payment = Payment::find($request->payment_id);
                if (! $payment) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
                }

                // Ensure the payment belongs to the same patient (if available)
                if ($payment->patient_id && $patientId && $payment->patient_id != $patientId) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Payment does not belong to the patient'], 403);
                }

                // Prevent reusing a payment already linked to another appointment
                if ($payment->appointment_id && $payment->appointment_id != $appointment->id) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Payment already used for another appointment'], 409);
                }

                $payment->appointment_id = $appointment->id;

                if ($payment->status === 'completed') {
                    $appointment->status = 'scheduled';

                    // Try to link existing invoice to this appointment if any
                    $invoice = Invoice::where('patient_id', $payment->patient_id)
                        ->where('total_amount', $payment->amount)
                        ->where('created_at', '>=', $payment->created_at->subSeconds(5))
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($invoice) {
                        $invoice->appointment_id = $appointment->id;
                        $invoice->save();
                    } else {
                        // Create invoice if none is present
                        $invoice = Invoice::create([
                            'patient_id' => $payment->patient_id,
                            'total_amount' => $payment->amount,
                            'paid_amount' => $payment->amount,
                            'due_amount' => 0,
                            'status' => 'paid',
                            'invoice_date' => now()->toDateString(),
                            'due_date' => now()->toDateString(),
                            'notes' => 'Linked payment invoice',
                            'appointment_id' => $appointment->id,
                        ]);
                    }
                } else {
                    $appointment->status = 'pending';
                }

                $payment->save();
                $appointment->save();
            } else {
                // No payment provided -> mark scheduled
                $appointment->status = 'scheduled';
                $appointment->save();
            }

            DB::commit();

            // Load relationships for response
            $appointment->load(['doctor', 'patient', 'resource']);

            // Ensure patient name in response is set
            if (empty($patientNameForResponse) && $appointment->patient) {
                $patientNameForResponse = ($appointment->patient->first_name ?? null) ? ($appointment->patient->first_name . ' ' . $appointment->patient->last_name) : ($appointment->patient->name ?? null);
            }

            // Format times as H:i
            $startFormatted = $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('H:i') : null;
            $endFormatted = $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time)->format('H:i') : null;

            return response()->json([
                'success' => true,
                'message' => 'Appointment scheduled successfully',
                'data' => [
                    'id' => $appointment->id,
                    'doctor_name' => $doctor->first_name . ' ' . $doctor->last_name,
                    'appointment_date' => $appointment->appointment_date,
                    'start_time' => $startFormatted,
                    'end_time' => $endFormatted,
                    'patient_name' => $patientNameForResponse,
                    'resource_name' => $appointment->resource ? $appointment->resource->name : null,
                    'type' => $appointment->type, // Added in response
                    'notes' => $appointment->notes,
                    'payment' => isset($payment) ? [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency ?? null,
                        'status' => $payment->status ?? null,
                        'transaction_id' => $payment->transaction_id ?? null
                    ] : null,
                    'invoice' => isset($invoice) ? [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number ?? null,
                        'total_amount' => $invoice->total_amount ?? null
                    ] : null
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error scheduling appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAppointment(Request $request, $id)
    {
        if ($request->has('start_time') && preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $request->start_time)) {
        
            $lastColonPos = strrpos($request->start_time, ':');
            $request->merge(['start_time' => substr($request->start_time, 0, $lastColonPos)]);
        }
        if ($request->has('end_time') && preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $request->end_time)) {
      
            $lastColonPos = strrpos($request->end_time, ':');
            $request->merge(['end_time' => substr($request->end_time, 0, $lastColonPos)]);
        }

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'sometimes|required|exists:doctors,id',
            'appointment_date' => 'sometimes|required|date|after_or_equal:today',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'patient_id' => 'nullable|exists:patients,id',
            'resource_id' => 'nullable|exists:resources,id',
            'notes' => 'nullable|string',
            'type' => 'nullable|string|in:person,video' // Added type field
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $authUser = null;
            try {
                $authUser = JWTAuth::parseToken()->authenticate();
            } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
                $authUser = auth('api')->user() ?? Auth::user() ?? null;
            } catch (\Exception $e) {
                $authUser = auth('api')->user() ?? Auth::user() ?? null;
            }

            $authPatient = null;
            if ($authUser) {
                $authPatient = Patient::where('user_id', $authUser->id)->first();

                if (! $authPatient && isset($authUser->user_type) && $authUser->user_type === 'patient') {
                    try {
                        $fallbackName = $authUser->name ?? $authUser->email ?? '';
                        $nameParts = explode(' ', trim($fallbackName));
                        $first = $nameParts[0] ?? null;
                        $last = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;

                        $authPatient = Patient::create([
                            'user_id' => $authUser->id,
                            'first_name' => $first,
                            'last_name' => $last,
                            'email' => $authUser->email ?? null,
                            'phone' => $authUser->phone ?? null,
                            'is_active' => 1,
                        ]);
                    } catch (\Throwable $ex) {
                        logger()->warning('Auto-creating Patient failed for user id ' . ($authUser->id ?? 'unknown') . ': ' . $ex->getMessage());
                    }
                }
            }

            $appointment = Appointment::find($id);

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            // If authenticated as a patient, prevent updating someone else's appointment
            if ($authPatient && $appointment->patient_id && $appointment->patient_id != $authPatient->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this appointment'
                ], 403);
            }

            if ($request->has('doctor_id')) {
                $doctor = Doctor::find($request->doctor_id);
                if (!$doctor) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Doctor not found'
                    ], 404);
                }
                $appointment->doctor_id = $doctor->id;
            }

            if ($request->has('patient_id')) {
                // allow null to clear
                if ($request->patient_id) {
                    if ($authPatient && $request->patient_id != $authPatient->id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized to assign appointment to another patient'
                        ], 403);
                    }

                    $p = Patient::find($request->patient_id);
                    if (!$p) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Patient not found'
                        ], 404);
                    }
                    $appointment->patient_id = $p->id;
                } else {
                    if (! ($authUser && isset($authUser->user_type) && in_array($authUser->user_type, ['admin', 'staff', 'doctor']))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized to clear appointment patient'
                        ], 403);
                    }
                    $appointment->patient_id = null;
                }
            } elseif ($request->has('patient_name')) {
                if ($request->patient_name) {
                    $p = Patient::where(DB::raw("CONCAT(first_name, ' ', last_name)"), $request->patient_name)
                        ->orWhere('name', $request->patient_name)
                        ->first();

                    if (!$p) {
                        $u = User::where('name', $request->patient_name)->first();
                        if ($u) {
                            $p = Patient::where('user_id', $u->id)->first();
                        }
                    }

                    if (!$p) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Patient not found'
                        ], 404);
                    }

                    if ($authPatient && $p->id != $authPatient->id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized to assign appointment to another patient'
                        ], 403);
                    }

                    $appointment->patient_id = $p->id;
                } else {
                    // clearing via empty name - restricted
                    if (! ($authUser && isset($authUser->user_type) && in_array($authUser->user_type, ['admin', 'staff', 'doctor']))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized to clear appointment patient'
                        ], 403);
                    }
                    $appointment->patient_id = null;
                }
            }

            if ($request->has('resource_id')) {
                if ($request->resource_id) {
                    $r = Resource::find($request->resource_id);
                    if (!$r) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Resource not found'
                        ], 404);
                    }
                    $appointment->resource_id = $r->id;
                } else {
                    $appointment->resource_id = null;
                }
            } elseif ($request->has('resource_name')) {
                if ($request->resource_name) {
                    $r = Resource::where('name', $request->resource_name)->first();
                    if (!$r) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Resource not found'
                        ], 404);
                    }
                    $appointment->resource_id = $r->id;
                } else {
                    $appointment->resource_id = null;
                }
            }

            if ($request->has('appointment_date')) {
                $appointment->appointment_date = $request->appointment_date;
            }
            if ($request->has('start_time')) {
                $appointment->start_time = $request->start_time . ':00';
            }
            if ($request->has('end_time')) {
                $appointment->end_time = $request->end_time . ':00';
            }
            if ($request->has('notes')) {
                $appointment->notes = $request->notes;
            }
            if ($request->has('type')) {
                $appointment->type = $request->type; // Added type update
            }

            if (\Carbon\Carbon::parse($appointment->appointment_date)->isToday()) {
                $dateStr = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                $newStartTime = \Carbon\Carbon::parse($dateStr . ' ' . $appointment->start_time);
                
                $origDateStr = \Carbon\Carbon::parse($appointment->getOriginal('appointment_date'))->format('Y-m-d');
                $originalStartTime = \Carbon\Carbon::parse($origDateStr . ' ' . $appointment->getOriginal('start_time'));
                
                if ($newStartTime->lt(now()) && $newStartTime->ne($originalStartTime)) {
                     $pmStartTime = $newStartTime->copy()->addHours(12);
                     if ($pmStartTime->gt(now()) && $pmStartTime->isToday()) {
                          $appointment->start_time = $pmStartTime->format('H:i:s');
                          
                          if ($appointment->end_time) {
                              $currentEndTime = \Carbon\Carbon::parse($dateStr . ' ' . $appointment->end_time);
                              if ($currentEndTime->lte($pmStartTime)) {
                                 
                                  $appointment->end_time = $currentEndTime->addHours(12)->format('H:i:s');
                              }
                          }
                     } else {
                         return response()->json([
                            'success' => false,
                            'message' => 'Cannot update appointment to a past time. (Received: ' . $newStartTime->format('H:i') . ', Current: ' . now()->format('H:i') . ')'
                        ], 422);
                     }
                }
            }

            if ($appointment->hasConflicts()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Time slot is not available. Please choose a different time.'
                ], 409);
            }

            $appointment->save();
            DB::commit();
            $appointment->load(['doctor', 'patient', 'resource']);

            $startFormatted = $appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format('H:i') : null;
            $endFormatted = $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time)->format('H:i') : null;

            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully',
                'data' => [
                    'id' => $appointment->id,
                    'doctor_name' => $appointment->doctor ? $appointment->doctor->first_name . ' ' . $appointment->doctor->last_name : null,
                    'appointment_date' => $appointment->appointment_date,
                    'start_time' => $startFormatted,
                    'end_time' => $endFormatted,
                    'patient_name' => $appointment->patient ? (($appointment->patient->first_name ?? null) ? ($appointment->patient->first_name . ' ' . $appointment->patient->last_name) : ($appointment->patient->name ?? null)) : null,
                    'resource_name' => $appointment->resource ? $appointment->resource->name : null,
                    'type' => $appointment->type, // Added in response
                    'notes' => $appointment->notes,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAppointment($id)
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            $authUser = auth('api')->user() ?? Auth::user() ?? null;
        } catch (\Exception $e) {
            $authUser = auth('api')->user() ?? Auth::user() ?? null;
        }

        // Map to a Patient record if applicable
        $authPatient = null;
        if ($authUser) {
            $authPatient = Patient::where('user_id', $authUser->id)->first();
        }

        DB::beginTransaction();

        try {
            $appointment = Appointment::find($id);

            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }

            // If authenticated as a patient, prevent deleting someone else's appointment
            if ($authPatient && $appointment->patient_id && $appointment->patient_id != $authPatient->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this appointment'
                ], 403);
            }

            // Handle related chat conversations (delete all to ensure cleanup)
            $conversations = \App\Models\ChatConversation::where('appointment_id', $appointment->id)->get();
            foreach ($conversations as $conversation) {
                // Delete chat assignments
                \App\Models\ChatAssignment::where('conversation_id', $conversation->conversation_id)->delete();
                // Delete chat messages
                \App\Models\ChatMessage::where('conversation_id', $conversation->conversation_id)->delete();
                // Delete conversation
                $conversation->delete();
            }

            $appointment->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function chat($id)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);

        // Try to find an existing conversation for this appointment.
        // If none, try to find an active conversation for the same patient to reuse it.
        $conversation = \App\Models\ChatConversation::where('appointment_id', $appointment->id)->first();

        if (! $conversation && $appointment->patient_id) {
            $conversation = \App\Models\ChatConversation::where('patient_id', $appointment->patient_id)
                ->where('status', 'active')
                ->orderBy('last_message_at', 'desc')
                ->first();
        }

        if (! $conversation) {
            // Create new conversation
            $conversation = \App\Models\ChatConversation::create([
                'conversation_id' => \App\Models\ChatConversation::generateConversationId($appointment->patient_id),
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'status' => 'active',
                'priority' => 'medium',
                'last_message_at' => now()
            ]);

            // Send initial message (system messages have no sender_id)
            \App\Models\ChatMessage::create([
                'conversation_id' => $conversation->conversation_id,
                'sender_type' => 'system',
                'sender_id' => null,
                'message_type' => 'text',
                'message' => "Chat started for appointment on " . $appointment->appointment_date . " at " . $appointment->start_time,
                'delivered_at' => now()
            ]);
        }

        // Ensure the doctor is assigned if not already (avoid duplicate active assignments)
        $existingAssignment = \App\Models\ChatAssignment::where('conversation_id', $conversation->conversation_id)
            ->where('assigned_to', Auth::id())
            ->whereNull('unassigned_at')
            ->first();

        if (! $existingAssignment) {
            \App\Models\ChatAssignment::create([
                'conversation_id' => $conversation->conversation_id,
                'assigned_to' => Auth::id(),
                'assigned_by' => Auth::id(),
            ]);
        } else {
            // touch assigned_at to now to keep it active
            $existingAssignment->update(['assigned_at' => now()]);
        }

        // Make sure conversation meta is updated
        $conversation->update(['last_message_at' => now(), 'status' => 'active']);

        // Redirect to chat page with conversation
        return redirect()->route('doctor.chat.index', ['c' => $conversation->conversation_id]);
    }
}
