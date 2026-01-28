<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PatientMedicalRecord;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\PatientNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PatientRecordController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $doctorId = Auth::id();
        $records = PatientMedicalRecord::with(['patient', 'appointment'])
            ->where('doctor_id', $doctorId)
            ->orderBy('record_date', 'desc')
            ->get();

        return view($user->user_type.'.medical-reports.index', compact('records'));
    }

    public function create()
    {
        $user = Auth::user();
        $doctorId = Auth::id();
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->with('patient')
            ->get();

        return view($user->user_type.'.medical-reports.create', compact('appointments'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        

        $appointment = $request->appointment_id ? Appointment::findOrFail($request->appointment_id) : null;

            $filePath = null;
        if ($request->hasFile('report_file')) {
           
            $file = $request->file('report_file');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension(); // Generate unique file name
            $filePath = $file->storeAs('reports', $fileName, 'public'); // Store file
        }

        $record = PatientMedicalRecord::create([
            'appointment_id' => $request->appointment_id,
            'patient_id' => $appointment ? $appointment->patient_id : null,
            'doctor_id' => $user->doctor_id,
            'notes' => $request->notes,
            'report_file' => $filePath, // Save full file path
            'record_date' => now(),
            'report_title' => $request->report_title,
            'report_type' => $request->report_type,
        ]);

        // Notify patient on record creation
        PatientNotify::create([
            'patient_id' => $appointment ? $appointment->patient_id : null,
            'title' => $request->report_title,
            'message' => $request->report_type,
        ]);

        return redirect()->route($user->user_type.'.medical-reports.index')
            ->with('success', 'Medical record created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $record = PatientMedicalRecord::where('doctor_id', $user->id)
            ->findOrFail($id);

        if ($request->hasFile('report_file')) {
            $filePath = $request->file('report_file')->store('reports', 'public');
            $record->update(['report_file' => $filePath]);
        }

        $record->update([
            'notes' => $request->notes,
            'report_title' => $request->report_title,
            'report_type' => $request->report_type,
        ]);

        // Notify patient on record update
        PatientNotify::create([
            'patient_id' => $record->patient_id,
            'title' => $request->report_title,
            'message' => $request->report_type,
        ]);

        return redirect()->route($user->user_type.'.medical-reports.index')
            ->with('success', 'Medical record updated successfully.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $record = PatientMedicalRecord::with(['patient', 'doctor', 'appointment', 'prescription'])
            ->where('doctor_id', auth()->id())
            ->findOrFail($id);

        return view($user->user_type.'.medical-reports.show', compact('record'));
    }

    public function edit($id)
    {
        
        $user = Auth::user();
          $doctorId = Auth::id();
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->with('patient')
            ->get();
        $record = PatientMedicalRecord::where('doctor_id', $user->id)
            ->findOrFail($id);

        return view($user->user_type.'.medical-reports.edit', compact('record', 'appointments'));
    }

    

    public function destroy($id)
    {
        $user = Auth::user();
        $record = PatientMedicalRecord::where('doctor_id', auth()->id())
            ->findOrFail($id);

        $record->delete();

        return redirect()->route($user->user_type.'.medical-reports.index')
            ->with('success', 'Medical record deleted successfully.');
    }

    // Download PDF Report
    public function downloadReport($id)
    {
        $user = Auth::user();
        $record = PatientMedicalRecord::with(['patient', 'doctor', 'appointment', 'prescription'])
            ->where('doctor_id', auth()->id())
            ->findOrFail($id);

        $pdf = Pdf::loadView($user->user_type.'.medical-reports.pdf', compact('record'));

        return $pdf->download('medical-report-'.$record->id.'-'.$record->patient->name.'.pdf');
    }

    // Print Report
    public function printReport($id)
    {
        $user = Auth::user();
        $record = PatientMedicalRecord::with(['patient', 'doctor', 'appointment', 'prescription'])
            ->where('doctor_id', auth()->id())
            ->findOrFail($id);

        return view($user->user_type.'.medical-reports.pdf', compact('record'));
    }

    public function getPatientAppointments($patientId)
    {
        $appointments = Appointment::where('patient_id', $patientId)
            ->where('doctor_id', auth()->id())
            ->where('status', 'completed')
            ->get();

        return response()->json($appointments);
    }

 

    // public function getReports(Request $request)
    // {
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate();
    //     } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Please login first to access medical reports.'
    //         ], 401);
    //     }
        
    //     $query = PatientMedicalRecord::with(['patient', 'appointment'])
    //         ->whereHas('patient', function($q) use ($user) {
    //             $q->where('user_id', $user->id);
    //         });

    //     $records = $query->get();

    //     $formattedRecords = $records->map(function ($record) {
    //         return [
    //             'id' => $record->id,
    //             'report_title' => $record->report_title,
    //             'report_type' => $record->report_type,
    //             'diagnosis' => $record->diagnosis,
    //             'record_date' => $record->record_date,
    //             'created_at' => $record->created_at,
    //             'patient' => $record->patient ? [
    //                 'id' => $record->patient->id,
    //                 'name' => $record->patient->first_name . ' ' . $record->patient->last_name,
    //             ] : null,
    //             'appointment' => $record->appointment ? [
    //                 'id' => $record->appointment->id,
    //                 'date' => $record->appointment->appointment_date,
    //             ] : null,
    //         ];
    //     });
        
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Medical reports retrieved successfully.',
    //         'data' => $formattedRecords,
    //         'meta' => [
    //             'current_page' => $records->currentPage(),
    //             'last_page' => $records->lastPage(),
    //             'per_page' => $records->perPage(),
    //             'total' => $records->total(),
    //             'from' => $records->firstItem(),
    //             'to' => $records->lastItem(),
    //         ]
    //     ]);
    // }

    public function getReports(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to access medical reports.'
            ], 401);
        }

        $records = PatientMedicalRecord::where('patient_id', $user->patient_id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Medical reports retrieved successfully.',
            'data' => $records
        ]);
    }

    public function getReportById($id)
    {
        try {
            $report = PatientMedicalRecord::find($id);
            
            if (!$report) {
                return response()->json([
                    'status' => false,
                    'message' => 'Medical report not found'
                ], 404);
            }
            
            // Now load relationships safely
            $report->load([
                'patient' => function($query) {
                    $query->select('id', 'first_name', 'last_name', 'gender', 'phone', 'address');
                },
                'appointment' => function($query) {
                    $query->select('id', 'appointment_date', 'status');
                },
                'doctor' => function($query) {
                    $query->select('id', 'first_name', 'last_name');
                }
            ]);
           
            
            $responseData = [
                'id' => $report->id,
                'report_title' => $report->report_title ?? 'N/A',
                'report_type' => $report->report_type ?? 'N/A',
                'symptoms' => $report->symptoms ?? 'N/A',
                'diagnosis' => $report->diagnosis ?? 'N/A',
                'treatment_plan' => $report->treatment_plan ?? 'N/A',
                'notes' => $report->notes ?? 'N/A',
                'vitals' => [
                    'height' => $report->height ?? 'N/A',
                    'weight' => $report->weight ?? 'N/A',
                    'blood_pressure' => $report->blood_pressure ?? 'N/A',
                    'temperature' => $report->temperature ?? 'N/A'
                ],
                
                'patient' => $report->patient ? [
                    'id' => $report->patient->id,
                    'name' => ($report->patient->first_name ?? '') . ' ' . ($report->patient->last_name ?? ''),
                    'gender' => $report->patient->gender ?? 'N/A',
                    'phone' => $report->patient->phone ?? 'N/A',
                    'address' => $report->patient->address ?? 'N/A',
                ] : null,
                
                'doctor' => $report->doctor ? [
                    'id' => $report->doctor->id,
                    'name' => ($report->doctor->first_name ?? '') . ' ' . ($report->doctor->last_name ?? ''),
                ] : null,
                
                'appointment' => $report->appointment ? [
                    'id' => $report->appointment->id,
                    'date' => $report->appointment->appointment_date ?? 'N/A',
                    'status' => $report->appointment->status ?? 'N/A'
                ] : null,
            ];

            
            return response()->json([
                'status' => true,
                'message' => 'Medical report retrieved successfully',
                'data' => $responseData
            ]);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error in getReportById: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while retrieving the medical report',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function downloadReportPdf($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException | TokenInvalidException | JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first to download medical reports.'
            ], 401);
        }

        try {
            $record = PatientMedicalRecord::with(['patient', 'doctor', 'appointment', 'prescription'])
                ->findOrFail($id);
            
            
            
            // View selection based on user type
            $viewName = 'doctor.medical-reports.pdf'; // Default
            if (in_array($user->user_type, ['patient', 'admin'])) {
                $viewName = $user->user_type . '.medical-reports.pdf';
            }
            
            // Check if view exists, fallback to default
            if (!view()->exists($viewName)) {
                $viewName = 'medical-reports.pdf'; // Fallback view
            }

            $data = [
                'record' => $record,
                'title' => $record->report_title ?: 'Medical Report',
                'date' => now()->format('d/m/Y')
            ];

            $pdf = Pdf::loadView($viewName, $data);
            
            // Filename generation
            $patientName = $record->patient ? 
                (isset($record->patient->name) ? $record->patient->name : 
                $record->patient->first_name . ' ' . $record->patient->last_name) : 
                'patient';
                
            $filename = 'medical-report-' . $record->id . '-' . 
                    Str::slug($patientName) . '-' . date('Y-m-d') . '.pdf';

            return $pdf->download($filename);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medical record not found.'
            ], 404);
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access this medical report.'
            ], 403);
            
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage(), [
                'record_id' => $id,
                'user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}