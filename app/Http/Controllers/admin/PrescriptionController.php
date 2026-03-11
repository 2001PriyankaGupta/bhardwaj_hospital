<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\PatientMedicalRecord;
use App\Models\PatientNotify;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PrescriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $doctorId = auth()->user()->doctor_id ?? null;
        $prescriptions = Prescription::with(['patient', 'medicalRecord'])
            ->where('doctor_id', $doctorId)
            ->orderBy('prescription_date', 'desc')
            ->get();

        return view($user->user_type.'.prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $doctorId = auth()->user()->doctor_id ?? null;
        $selectedAppointmentId = $request->appointment_id;

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->with('patient')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view($user->user_type.'.prescriptions.create', compact('appointments', 'selectedAppointmentId'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Entering Prescription Store', ['data' => $request->all()]);
            
            $user = Auth::user();
            $appointmentId = $request->appointment_id;
            
            if (!$appointmentId) {
                return response()->json(['success' => false, 'message' => 'Appointment ID is required.'], 422);
            }

            $appointment = Appointment::findOrFail($appointmentId);
            
            // Create or find associated medical record
            $record = PatientMedicalRecord::where('appointment_id', $appointment->id)->first();
            
            if (!$record) {
                // Ensure the record is created if missing since prescriptions table requires it
                $record = PatientMedicalRecord::create([
                    'appointment_id'  => $appointment->id,
                    'patient_id'      => $appointment->patient_id,
                    'doctor_id'       => $appointment->doctor_id,
                    'record_date'     => now(),
                    'report_title'    => 'Consultation Visit',
                    'report_type'     => 'Digital',
                    'notes'           => 'Auto-created during prescription generation.'
                ]);
            }

            $prescription = Prescription::create([
                'medical_record_id' => $record->id,
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'medication_details' => $request->medication_details,
                'instructions' => $request->instructions,
                'follow_up_advice' => $request->follow_up_advice,
                'prescription_date' => now(),
                'valid_until' => $request->valid_until,
                'is_active' => true,
            ]);

            $patient = \App\Models\Patient::find($appointment->patient_id);
            if ($patient && $patient->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $patient->user_id,
                    'type' => 'prescription',
                    'title' => 'New Prescription Created',
                    'meta_data' => [
                        'message' => 'A new prescription has been created for your appointment on ' . ($appointment->appointment_date ? $appointment->appointment_date->format('d M Y') : now()->format('d M Y')),
                        'appointment_id' => $appointment->id,
                        'prescription_id' => $prescription->id
                    ],
                ]);
            }

            \Log::info('Prescription saved successfully', ['id' => $prescription->id]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prescription created successfully.',
                    'data' => $prescription
                ]);
            }

            return redirect()->route($user->user_type.'.prescriptions.index')
                ->with('success', 'Prescription created successfully.');

        } catch (\Exception $e) {
            \Log::error('Prescription Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            
            throw $e;
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $prescription = Prescription::with(['patient', 'doctor', 'medicalRecord'])
            ->where('doctor_id', auth()->user()->doctor_id ?? null)
            ->findOrFail($id);

        return view($user->user_type.'.prescriptions.show', compact('prescription'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $prescription = Prescription::where('doctor_id', auth()->user()->doctor_id ?? null)
            ->findOrFail($id);

        return view($user->user_type.'.prescriptions.edit', compact('prescription'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $prescription = Prescription::where('doctor_id', auth()->user()->doctor_id ?? null)
            ->findOrFail($id);

        $request->validate([
            'medication_details' => 'required|array',
            'medication_details.*.medicine' => 'required|string',
            'medication_details.*.dosage' => 'required|string',
            'medication_details.*.frequency' => 'required|string',
            'medication_details.*.duration' => 'required|string',
            'instructions' => 'nullable|string',
            'follow_up_advice' => 'nullable|string',
            'valid_until' => 'nullable|date|after:today',
        ]);

        $prescription->update([
            'medication_details' => $request->medication_details,
            'instructions' => $request->instructions,
            'follow_up_advice' => $request->follow_up_advice,
            'valid_until' => $request->valid_until,
            'is_active' => $request->has('is_active'),
        ]);

        if ($prescription->patient && $prescription->patient->user_id) {
            \App\Models\Notification::create([
                'user_id' => $prescription->patient->user_id,
                'type' => 'prescription',
                'title' => 'Prescription Updated',
                'meta_data' => [
                    'message' => 'Prescription for ' . ($prescription->patient->first_name ?? 'N/A') . ' ' . ($prescription->patient->last_name ?? 'N/A') . ' has been updated.',
                    'prescription_id' => $prescription->id
                ],
            ]);
        }

        return redirect()->route($user->user_type.'.prescriptions.index')
            ->with('success', 'Prescription updated successfully.');
    }

    public function destroy($id)
    {
        $prescription = Prescription::where('doctor_id', auth()->user()->doctor_id ?? null)
            ->findOrFail($id);

        $prescription->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Prescription deleted successfully.');
    }

    // Download PDF Prescription
    public function downloadPrescription($id)
    {
        $user = Auth::user();
        $prescription = Prescription::with(['patient', 'doctor', 'medicalRecord'])
            ->where('doctor_id', auth()->user()->doctor_id ?? null)
            ->findOrFail($id);

        $pdf = Pdf::loadView($user->user_type.'.prescriptions.pdf', compact('prescription'));

        return $pdf->download('prescription-'.$prescription->id.'-'.$prescription->patient->first_name.'-'.$prescription->patient->last_name.'.pdf');
    }

    // Print Prescription
    public function printPrescription($id)
    {
        $user = Auth::user();
        $prescription = Prescription::with(['patient', 'doctor', 'medicalRecord'])
            ->where('doctor_id',auth()->user()->doctor_id ?? null)
            ->findOrFail($id);

        return view($user->user_type.'.prescriptions.print', compact('prescription'));
    }
}