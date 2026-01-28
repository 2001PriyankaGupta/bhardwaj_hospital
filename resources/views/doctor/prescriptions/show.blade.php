@extends('doctor.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .table-borderless th {
        font-weight: 600;
        color: #495057;
    }

    .table-borderless td {
        color: #6c757d;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .card {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }

    .header-title {
        color: #343a40;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
</style>

@section('content')
    <div class="container-fluid mt-4">

        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="page-title text-black">Prescription #{{ $prescription->id }}</h4>

                        </div>

                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <a href="{{ route('doctor.prescriptions.print', $prescription->id) }}"
                                    class="btn btn-secondary" target="_blank">
                                    <i class="fas fa-print"></i> Print
                                </a>

                                <a href="{{ route('doctor.prescriptions.download', $prescription->id) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>


                                <a href="{{ route('doctor.prescriptions.index') }}" class="btn btn-secondary">
                                    Back to list
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescription Content -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title mb-4">Prescription Details</h4>

                        <div class="row mb-4">
                            <!-- Left -->
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Patient Name:</th>
                                        <td>
                                            {{ $prescription->patient->first_name ?? 'N/A' }}
                                            {{ $prescription->patient->last_name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Prescription Date:</th>
                                        <td>
                                            {{ optional($prescription->prescription_date)->format('d M Y') ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Doctor:</th>
                                        <td>
                                            Dr. {{ $prescription->doctor->first_name ?? 'N/A' }}
                                            {{ $prescription->doctor->last_name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Right -->
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Appointment ID:</th>
                                        <td>{{ $prescription->appointment_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Patient Age:</th>
                                        <td>{{ $prescription->patient->age ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Gender:</th>
                                        <td>{{ ucfirst($prescription->patient->gender ?? 'N/A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>



                        <!-- Medicines -->
                        <div class="mb-4">
                            <h5>Medicines</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Medicine Name</th>
                                            <th>Dosage</th>
                                            <th>Frequency</th>
                                            <th>Duration</th>
                                            <th>Instructions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Check if medicines relationship exists and is not null
                                            $medicines = $prescription->medicines ?? collect();

                                            // Handle medication_details field - it might already be an array
                                            $medicationDetails = [];
                                            if ($prescription->medication_details) {
                                                // Check if it's already an array
    if (is_array($prescription->medication_details)) {
        $medicationDetails = $prescription->medication_details;
    }
    // Check if it's JSON string
                                                elseif (is_string($prescription->medication_details)) {
                                                    $decoded = json_decode($prescription->medication_details, true);
                                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                        $medicationDetails = $decoded;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if ($medicines && $medicines->count() > 0)
                                            @foreach ($medicines as $index => $medicine)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $medicine->name ?? 'N/A' }}</td>
                                                    <td>{{ $medicine->dosage ?? 'N/A' }}</td>
                                                    <td>{{ $medicine->frequency ?? 'N/A' }}</td>
                                                    <td>{{ $medicine->duration ?? 'N/A' }}</td>
                                                    <td>{{ $medicine->instructions ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @elseif(!empty($medicationDetails) && is_array($medicationDetails))
                                            @foreach ($medicationDetails as $index => $medicine)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $medicine['medicine'] ?? ($medicine['name'] ?? 'N/A') }}</td>
                                                    <td>{{ $medicine['dosage'] ?? 'N/A' }}</td>
                                                    <td>{{ $medicine['frequency'] ?? 'N/A' }}</td>
                                                    <td>{{ $medicine['duration'] ?? 'N/A' }}</td>
                                                    <td>{{ $medicine['instructions'] ?? ($medicine['remarks'] ?? '-') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    No medicines added
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Instructions -->
                        @if ($prescription->instructions)
                            <div class="mb-4">
                                <h5>Instructions</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($prescription->instructions)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Follow Up Advice -->
                        @if ($prescription->follow_up_advice)
                            <div class="mb-4">
                                <h5>Follow-up Advice</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($prescription->follow_up_advice)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Notes -->
                        @if ($prescription->notes)
                            <div>
                                <h5>Additional Notes</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($prescription->notes)) !!}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Right Side Summary -->
            <div class="col-lg-4">


                <!-- Summary Card -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="header-title">Prescription Summary</h5>

                        <ul class="list-unstyled mt-3">
                            @php
                                $medicineCount = 0;
                                if ($prescription->medicines && $prescription->medicines->count() > 0) {
                                    $medicineCount = $prescription->medicines->count();
                                } elseif ($prescription->medication_details) {
                                    // Handle both array and JSON string
                                    if (is_array($prescription->medication_details)) {
                                        $medicineCount = count($prescription->medication_details);
                                    } elseif (is_string($prescription->medication_details)) {
                                        $meds = json_decode($prescription->medication_details, true);
                                        $medicineCount = is_array($meds) ? count($meds) : 0;
                                    }
                                }
                            @endphp

                            <li class="mb-2"><strong>Total Medicines:</strong> {{ $medicineCount }}</li>
                            <li class="mb-2"><strong>Status:</strong>
                                <span class="badge bg-success">Active</span>
                            </li>
                            <li class="mb-2"><strong>Created At:</strong>
                                {{ $prescription->created_at->format('d M Y, h:i A') }}
                            </li>
                            @if ($prescription->medical_record_id)
                                <li class="mb-2">
                                    <strong>Medical Record:</strong>
                                    <a href="{{ route('doctor.medical-reports.show', $prescription->medical_record_id) }}"
                                        class="text-primary">
                                        View Report
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="header-title">Quick Info</h5>
                        <table class="table table-sm table-borderless mt-3">
                            <tr>
                                <th>Patient ID:</th>
                                <td>{{ $prescription->patient_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $prescription->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @if ($prescription->next_follow_up_date)
                                <tr>
                                    <th>Follow-up Date:</th>
                                    <td>{{ \Carbon\Carbon::parse($prescription->next_follow_up_date)->format('d M Y') }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endsection
