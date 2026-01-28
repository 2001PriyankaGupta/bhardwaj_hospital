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
        $doctorId = auth()->id();
        $prescriptions = Prescription::with(['patient', 'medicalRecord'])
            ->where('doctor_id', $doctorId)
            ->orderBy('prescription_date', 'desc')
            ->get();

        return view($user->user_type.'.prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        $user = Auth::user();
        $doctorId = auth()->id();
        $records = PatientMedicalRecord::where('doctor_id', $doctorId)
            ->whereDoesntHave('prescription')
            ->with('patient')
            ->get();

        return view($user->user_type.'.prescriptions.create', compact('records'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        

        $record = PatientMedicalRecord::findOrFail($request->medical_record_id);

        $prescription = Prescription::create([
            'medical_record_id' => $request->medical_record_id,
            'appointment_id' => $record->appointment_id,
            'patient_id' => $record->patient_id,
            'doctor_id' => $record->doctor_id,
            'medication_details' => $request->medication_details,
            'instructions' => $request->instructions,
            'follow_up_advice' => $request->follow_up_advice,
            'prescription_date' => now(),
            'valid_until' => $request->valid_until,
        ]);

        PatientNotify::create([
            'patient_id' => $record ? $record->patient_id : null,
            'title' => 'New Prescription Created',
            'message' => 'Prescription for ' . ($record->patient->name ?? 'N/A') . ' has been created.',
        ]);

        return redirect()->route($user->user_type.'.prescriptions.index')
            ->with('success', 'Prescription created successfully.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $prescription = Prescription::with(['patient', 'doctor', 'medicalRecord'])
            ->where('doctor_id', auth()->id())
            ->findOrFail($id);

        return view($user->user_type.'.prescriptions.show', compact('prescription'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $prescription = Prescription::where('doctor_id', auth()->id())
            ->findOrFail($id);

        return view($user->user_type.'.prescriptions.edit', compact('prescription'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $prescription = Prescription::where('doctor_id', auth()->id())
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

        PatientNotify::create([
            'patient_id' => $prescription->patient_id,
            'title' => 'Prescription Updated',
            'message' => 'Prescription for ' . ($prescription->patient->name ?? 'N/A') . ' has been updated.',
        ]);

        return redirect()->route($user->user_type.'.prescriptions.index')
            ->with('success', 'Prescription updated successfully.');
    }

    public function destroy($id)
    {
        $prescription = Prescription::where('doctor_id', auth()->id())
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
            ->where('doctor_id', auth()->id())
            ->findOrFail($id);

        $pdf = Pdf::loadView($user->user_type.'.prescriptions.pdf', compact('prescription'));

        return $pdf->download('prescription-'.$prescription->id.'-'.$prescription->patient->name.'.pdf');
    }

    // Print Prescription
    public function printPrescription($id)
    {
        $user = Auth::user();
        $prescription = Prescription::with(['patient', 'doctor', 'medicalRecord'])
            ->where('doctor_id', auth()->id())
            ->findOrFail($id);

        return view($user->user_type.'.prescriptions.print', compact('prescription'));
    }
}
