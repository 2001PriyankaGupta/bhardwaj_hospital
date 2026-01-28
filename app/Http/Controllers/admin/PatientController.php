<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\PatientMedicalRecord;
use App\Models\CommunicationLog;
use App\Models\Appointment;
use App\Models\PatientNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class PatientController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $patients = Patient::withCount(['appointments' => function($query) {
            $query->where('patient_id', '!=', null);
        }])->latest()->get();

        $stats = [
            'totalPatients' => Patient::count(),
            'activePatients' => Patient::where('is_active', true)->count(),
            'todaysAppointments' => Appointment::whereDate('appointment_date', today())->count(),
            'totalMedicalRecords' => PatientMedicalRecord::count(),
        ];

        return view($user->user_type.'.patient.index', array_merge(['patients' => $patients], $stats));
    }

    // Create patient
    public function create()
    {
        $user = Auth::user();
        return view($user->user_type.'.patient.create');
    }

    // Store patient
    public function store(Request $request)
    {
        // Current logged in user (admin)
        $loggedInUser = Auth::user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email|unique:patients,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:120',
            'emergency_contact_number' => 'nullable|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
            'basic_medical_history' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Generate patient ID
            $patientId = Patient::generatePatientId();

            // 1. First create user for patient
            $userData = [
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make('default_password'), // Default password, can be changed later
                'user_type' => 'patient',
                'email_verified_at' => now(),
                'phone' => $request->phone,
                'gender' => $request->gender,
                'age' => $request->age,
                'address' => $request->address,
                'emergency_contact_number' => $request->emergency_contact_number,
                'alternate_contact_number' => $request->alternate_contact_number,
                'basic_medical_history' => $request->basic_medical_history,
                'profile_picture' => null,
                'is_admin' => false,
                'status' => 'active',
            ];

            $newUser = User::create($userData);

            // 2. Create patient record
            $patientData = [
                'patient_id' => $patientId,
                'user_id' => $newUser->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'medical_history' => $request->basic_medical_history,
                'is_active' => true,
            ];

            $patient = Patient::create($patientData);

            // 3. Update user with patient_id
            $newUser->update(['patient_id' => $patient->id]);

            DB::commit();

            // Use loggedInUser's user_type instead of newUser's user_type
            return redirect()->route($loggedInUser->user_type . '.patients.index')
                ->with('success', 'Patient created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            // If duplicate entry error for patient_id, regenerate ID and try again
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'patient_id')) {
                try {
                    DB::beginTransaction();
                    
                    $patientId = Patient::generatePatientId();
                    $patientData['patient_id'] = $patientId;
                    $patient = Patient::create($patientData);
                    $newUser->update(['patient_id' => $patient->id]);
                    
                    DB::commit();

                    return redirect()->route($loggedInUser->user_type . '.patients.index')
                        ->with('success', 'Patient created successfully.');
                } catch (\Exception $retryException) {
                    DB::rollBack();
                    
                    return redirect()->back()
                        ->with('error', 'Error creating patient: ' . $retryException->getMessage())
                        ->withInput();
                }
            }

            return redirect()->back()
                ->with('error', 'Error creating patient: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Edit function
    public function edit(Patient $patient)
    {
         $loggedInUser = Auth::user();
        
        return view($loggedInUser->user_type.'.patient.edit', compact('patient'));
    }

    // Update function
    public function update(Request $request, Patient $patient)
    {
       $loggedInUser = Auth::user();
        
        // Validate the request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $patient->user_id . '|unique:patients,email,' . $patient->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:120',
            'emergency_contact_number' => 'nullable|string|max:20',
            'alternate_contact_number' => 'nullable|string|max:20',
            'basic_medical_history' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Update patient record
            $patientData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'medical_history' => $request->basic_medical_history,
            ];

            $patient->update($patientData);

            // Update user record
            $userData = [
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'age' => $request->age,
                'address' => $request->address,
                'emergency_contact_number' => $request->emergency_contact_number,
                'alternate_contact_number' => $request->alternate_contact_number,
                'basic_medical_history' => $request->basic_medical_history,
            ];

            if ($patient->user) {
                $patient->user->update($userData);
            }

            DB::commit();

            return redirect()->route($loggedInUser->user_type.'.patients.index')
                ->with('success', 'Patient updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error updating patient: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Show patient details and basic analytics
    public function show(Patient $patient)
    {
        $user = Auth::user();
        $patient->load(['medicalRecords', 'communicationLogs', 'appointments']);

        // Patient analytics
        $analytics = [
            'appointment_stats' => $patient->getAppointmentStats(),
            'recent_medical_records' => $patient->medicalRecords()->latest()->take(5)->get(),
            'recent_communications' => $patient->communicationLogs()->latest()->take(5)->get(),
        ];

        return view($user->user_type.'.patient.show', compact('patient', 'analytics'));
    }

    // Medical records management
    public function medicalRecords(Patient $patient)
    {
        $user = Auth::user();
        $medicalRecords = $patient->medicalRecords()->latest()->paginate(10);
        return view($user->user_type.'.patient.medical-records', compact('patient', 'medicalRecords'));
    }

    public function storeMedicalRecord(Request $request, Patient $patient)
    {
        $loggedInUser = Auth::user();
        $filePath = null;

        if ($request->hasFile('report_file')) {
            $file = $request->file('report_file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension(); // Generate unique file name
            $filePath = $file->storeAs('reports', $fileName, 'public'); // Store file
        }

        $record = PatientMedicalRecord::create([
            'patient_id' => $patient->id, // Use $patient->id directly
            'notes' => $request->notes,
            'report_file' => $filePath, // Save full file path
            'record_date' => $request->record_date ?? now(), // Allow custom record_date
            'report_title' => $request->report_title,
            'report_type' => $request->report_type,
        ]);

        // Notify patient on record creation
        PatientNotify::create([
            'patient_id' => $patient->id, // Use $patient->id directly
            'title' => $request->report_title,
            'message' => 'A new medical record has been added.', // Updated message
        ]);

        return back()->with('success', 'Medical record added successfully.');
    }
    

    // Appointment history
    public function appointmentHistory(Patient $patient)
    {
        $user = Auth::user();
        $appointments = $patient->appointments()
            ->with([
                'doctor',
                'doctor.department', // Nested eager loading
                'department'
            ])
            ->latest()
            ->paginate(10);

        return view($user->user_type.'.patient.appointment-history', compact('patient', 'appointments'));
    }

    // Communication logs
    public function communicationLogs(Patient $patient)
    {
            $user = Auth::user();
        $communications = $patient->communicationLogs()->latest()->paginate(10);
        return view($user->user_type.'.patient.communication-logs', compact('patient', 'communications'));
    }

    public function storeCommunicationLog(Request $request, Patient $patient)
    {
        $request->validate([
            'communication_type' => 'required|in:email,sms,call,in_person',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $patient->communicationLogs()->create([
            'communication_type' => $request->communication_type,
            'subject' => $request->subject,
            'message' => $request->message,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Communication log added successfully.');
    }

    // Patient analytics
    public function analytics(Patient $patient)
    {
        $user = Auth::user();
        $analytics = [
            'total_appointments' => $patient->appointments()->count(),
            'completed_appointments' => $patient->appointments()->where('status', 'completed')->count(),
            'upcoming_appointments' => $patient->appointments()->where('status', 'scheduled')->count(),
            'medical_records_count' => $patient->medicalRecords()->count(),
            'communications_count' => $patient->communicationLogs()->count(),
            'appointment_trends' => $this->getAppointmentTrends($patient),
        ];

        return view($user->user_type.'.patient.analytics', compact('patient', 'analytics'));
    }

    private function getAppointmentTrends(Patient $patient)
    {
        return $patient->appointments()
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function destroy(Patient $patient)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();

            // Check if patient has appointments
            $appointmentCount = Appointment::where('patient_id', $patient->id)->count();
            $medicalRecordCount = MedicalRecord::where('patient_id', $patient->id)->count();

            // Optional: Add validation to prevent deletion if records exist
            if ($appointmentCount > 0 || $medicalRecordCount > 0) {
                return redirect()->back()->with('error',
                    'Cannot delete patient. Patient has ' .
                    ($appointmentCount > 0 ? $appointmentCount . ' appointment(s) ' : '') .
                    ($medicalRecordCount > 0 ? $medicalRecordCount . ' medical record(s)' : '') .
                    '. Please delete related records first.');
            }

            // Delete patient
            $patient->delete();

            DB::commit();

            return redirect()->route($user->user_type.'.patients.index')
                ->with('success', 'Patient deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error deleting patient: ' . $e->getMessage());
        }
    }

    // Alternative method: Force delete with related records (USE CAREFULLY)
    public function forceDestroy(Patient $patient)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();

            Appointment::where('patient_id', $patient->id)->delete();

            MedicalRecord::where('patient_id', $patient->id)->delete();

            $patient->delete();

            DB::commit();

            return redirect()->route($user->user_type.'.patients.index')
                ->with('success', 'Patient and all related records deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error deleting patient: ' . $e->getMessage());
        }
    }

    // Controller method for editing
    public function editMedicalRecord(PatientMedicalRecord $record)
    {
        $loggedInUser = Auth::user();
        
        // Ensure the record exists and belongs to the patient
        if (!$record) {
            return redirect()->back()->with('error', 'Medical record not found.');
        }
        
        // Get the patient from the record
        $patient = $record->patient;
        
        return view($loggedInUser->user_type . '.patient.edit-medical-record', compact('record', 'patient'));
    }

    // Controller method for updating
    public function updateMedicalRecord(Request $request, $id)
    {
        $loggedInUser = Auth::user();
        $record = PatientMedicalRecord::findOrFail($id);
       
        $request->validate([
            'report_title' => 'required|string|max:255',
            'report_type' => 'required|string|max:255',
            'record_date' => 'required|date',
            'notes' => 'nullable|string',
            'report_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);
        
        $filePath = $record->report_file; // Keep existing file path

        if ($request->hasFile('report_file')) {
            $file = $request->file('report_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('reports', $fileName, 'public');
            
            // Delete old file if exists
            if ($record->report_file && Storage::disk('public')->exists($record->report_file)) {
                Storage::disk('public')->delete($record->report_file);
            }
        }

        $record->update([
            'report_title' => $request->report_title,
            'report_type' => $request->report_type,
            'record_date' => $request->record_date,
            'notes' => $request->notes,
            'report_file' => $filePath,
        ]);

        PatientNotify::create([
            'patient_id' => $record->patient_id,
            'title' => 'Medical Record Updated: ' . $request->report_title,
            'message' => 'Your medical record has been updated.',
        ]);

        return redirect()->route($loggedInUser->user_type . '.patients.medical-records', $record->patient_id)
            ->with('success', 'Medical record updated successfully.');
    }

    public function deleteMedicalRecord($id)
    {
        $loggedInUser = Auth::user();
        $record = PatientMedicalRecord::findOrFail($id);
        try {
            $patientId = $record->patient_id;
            
            if ($record->report_file) {
                $filePath = $record->report_file;
                
                if (!empty($filePath) && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            
            $record->delete();
            
            $patient = Patient::find($patientId);
            
            if ($patient) {
                return redirect()->route('admin.patients.medical-records', $patient)
                    ->with('success', 'Medical record deleted successfully.');
            } else {
                return redirect()->route($loggedInUser->user_type . '.patients.index')
                    ->with('success', 'Medical record deleted successfully.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error deleting medical record: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete medical record. Please try again.');
        }
    }
}
