<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\QueueManagement;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $queues = QueueManagement::with(['patient', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view($user->user_type.'.queue.index', compact('queues'));
    }

    // Create new queue entry
    public function create()
    {
        $user = Auth::user();
        $patients = Patient::where('is_active', true)->get();
        $doctors = Doctor::where('status', 'active')->get();

        return view($user->user_type.'.queue.create', compact('patients', 'doctors'));
    }

   public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'queue_type' => 'required|in:normal,emergency,follow_up',
            'reason_for_visit' => 'nullable|string|max:500',
            'is_priority' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();

            $queueNumber = $this->generateQueueNumber($request->doctor_id);

            // Calculate position
            $position = QueueManagement::where('doctor_id', $request->doctor_id)
                ->where('status', 'waiting')
                ->count() + 1;

            // Calculate estimated wait time
            $doctor = Doctor::find($request->doctor_id);

            // Convert time string to minutes
            if ($doctor->average_consultation_time) {
                $time = explode(':', $doctor->average_consultation_time);
                $minutes = ($time[0] * 60) + $time[1] + ($time[2] / 60);
                $consultationTime = round($minutes);
            } else {
                $consultationTime = 15; // default 15 minutes
            }

            $estimatedWaitTime = $position * $consultationTime;

            $queue = QueueManagement::create([
                'queue_number' => $queueNumber,
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'queue_type' => $request->queue_type,
                'reason_for_visit' => $request->reason_for_visit,
                'check_in_time' => now(),
                'is_priority' => $request->is_priority ?? false,
                'position' => $position,
                'estimated_wait_time' => $estimatedWaitTime,
                'priority_score' => 0 // Will be calculated later
            ]);

            // Load patient for priority calculation
            $queue->load('patient');

            // Calculate priority score
            $queue->priority_score = $queue->calculatePriority();
            $queue->save();

            DB::commit();

            return redirect()->route($user->user_type.'.queue.index')
                ->with('success', 'Patient added to queue successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Queue creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add patient to queue. Please try again.');
        }
    }

    // Show queue details
    public function show(QueueManagement $queue)
    {
        $user = Auth::user();
        $queue->load(['patient', 'doctor']);
        return view($user->user_type.'.queue.show', compact('queue'));
    }

    // Edit queue
    public function edit(QueueManagement $queue)
    {
            $user = Auth::user();
        $patients = Patient::where('is_active', true)->get();
        $doctors = Doctor::where('status', 'active')->get();

        return view($user->user_type.'.queue.edit', compact('queue', 'patients', 'doctors'));
    }

    // Update queue
    public function update(Request $request, QueueManagement $queue)
    {
        $user = Auth::user();
        $request->validate([
            'status' => 'required|in:waiting,in_progress,completed,cancelled,no_show',
            'current_room' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'vital_signs' => 'nullable|array'
        ]);

        $queue->update($request->all());

        return redirect()->route($user->user_type.'.queue.show', $queue)
            ->with('success', 'Queue updated successfully!');
    }

    // Delete queue
    public function destroy(QueueManagement $queue)
    {
        $user = Auth::user();
        $queue->delete();

        return redirect()->route($user->user_type.'.queue.index')
            ->with('success', 'Queue entry deleted successfully!');
    }

    // Live dashboard
    // public function dashboard()
    // {
    //     $user = Auth::user();
    //     $todayQueues = QueueManagement::with(['patient', 'doctor'])
    //         ->whereDate('created_at', today())
    //         ->active()
    //         ->orderBy('priority_score', 'desc')
    //         ->orderBy('position')
    //         ->get()
    //         ->groupBy('doctor_id');

    //     $doctors = Doctor::where('status', 'active')->get();

    //     $stats = [
    //         'total_waiting' => QueueManagement::today()->waiting()->count(),
    //         'total_in_progress' => QueueManagement::today()->inProgress()->count(),
    //         'total_completed' => QueueManagement::today()->where('status', 'completed')->count(),
    //         'avg_wait_time' => QueueManagement::today()->avg('estimated_wait_time'),
    //     ];

    //     return view($user->user_type.'.queue.dashboard', compact('todayQueues', 'doctors', 'stats'));
    // }

    public function dashboard()
    {
        
        $user = Auth::user();
        
        $todayAppointments = Appointment::with(['patient', 'doctor', 'queue'])
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('doctor_id');

        $doctors = Doctor::where('status', 'active')->get();

        // Get stats
        $stats = [
            'total_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'completed' => Appointment::whereDate('appointment_date', today())
                ->where('status', 'completed')->count(),
            'waiting' => Appointment::whereDate('appointment_date', today())
                ->whereIn('status', ['scheduled', 'confirmed'])->count(),
            'cancelled' => Appointment::whereDate('appointment_date', today())
                ->where('status', 'cancelled')->count(),
        ];

        return view($user->user_type . '.queue.appointment_dashboard', 
            compact('todayAppointments', 'doctors', 'stats'));
    }

    // Call next patient
    public function callNext(Request $request, $doctorId)
    {
        $nextQueue = QueueManagement::where('doctor_id', $doctorId)
            ->waiting()
            ->orderBy('priority_score', 'desc')
            ->orderBy('position')
            ->first();

        if ($nextQueue) {
            $nextQueue->markAsCalled();

            return response()->json([
                'success' => true,
                'queue' => $nextQueue->load('patient'),
                'message' => 'Next patient called'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No patients in queue'
        ]);
    }

    // Complete current consultation
    public function complete(QueueManagement $queue)
    {
        $user = Auth::user();
        $queue->complete();

        // Update positions for remaining patients
        QueueManagement::where('doctor_id', $queue->doctor_id)
            ->where('status', 'waiting')
            ->where('position', '>', $queue->position)
            ->decrement('position');

        return redirect()->route($user->user_type.'.queue.dashboard')
            ->with('success', 'Consultation marked as completed!');
    }

    // Get queues for specific doctor (AJAX)
    public function getDoctorQueues($doctorId)
    {
        $queues = QueueManagement::with('patient')
            ->where('doctor_id', $doctorId)
            ->active()
            ->orderBy('priority_score', 'desc')
            ->orderBy('position')
            ->get();

        return response()->json($queues);
    }

    // Generate unique queue number
    private function generateQueueNumber($doctorId)
{
    $doctor = Doctor::find($doctorId);

    if (!$doctor) {
        throw new \Exception('Doctor not found');
    }

    // Get doctor initials
    $doctorCode = strtoupper(
        substr($doctor->first_name, 0, 1) .
        substr($doctor->last_name, 0, 1)
    );

    $dateCode = date('Ymd');

    // Count today's queues for this doctor
    $sequence = QueueManagement::where('doctor_id', $doctorId)
        ->whereDate('created_at', today())
        ->count() + 1;

    return "Q{$doctorCode}{$dateCode}" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
}


    

    // Update appointment status from queue
    public function updateAppointmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,cancelled,completed'
        ]);

        $appointment = Appointment::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $appointment->status = $request->status;
            $appointment->save();

            // If completed, also update queue
            if ($request->status == 'completed' && $appointment->queue) {
                $appointment->queue->update(['status' => 'completed']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment status updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get appointments for specific doctor
    public function getDoctorAppointments($doctorId)
    {
        $appointments = Appointment::with(['patient', 'queue'])
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('start_time')
            ->get();

        return response()->json($appointments);
    }
}
