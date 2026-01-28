@extends('doctor.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .page-title-box {
        padding-bottom: 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
        background-color: lightgrey !important;

    }

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
</style>

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="page-title text-black">Medical Report #{{ $record->id }}</h4>

                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <a href="{{ route('doctor.medical-reports.index') }}" class="btn btn-secondary">
                                    Back to List
                                </a>
                                <a href="{{ route('doctor.medical-reports.edit', $record->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <a href="{{ route('doctor.medical-reports.download', $record->id) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Patient Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Patient Name:</th>
                                        <td>{{ $record->patient->first_name ?? 'N/A' }}
                                            {{ $record->patient->last_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Record Date:</th>
                                        <td>{{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->format('d M Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Doctor:</th>
                                        <td>Dr. {{ $record->doctor->first_name ?? 'N/A' }}
                                            {{ $record->doctor->last_name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="40%">Height:</th>
                                        <td>{{ $record->height ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Weight:</th>
                                        <td>{{ $record->weight ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Blood Pressure:</th>
                                        <td>{{ $record->blood_pressure ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Temperature:</th>
                                        <td>{{ $record->temperature ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <h4 class="header-title mb-3">Medical Details</h4>
                        <div class="mb-4">
                            <h5>Report Details</h5>
                            <div class="p-3 bg-light rounded">
                                <strong>Title:</strong> {{ $record->report_title ?? 'N/A' }}<br>
                                <strong>Type:</strong> {{ $record->report_type ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-stethoscope me-2 text-primary"></i>Symptoms</h5>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($record->symptoms)) !!}
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5><i class="fas fa-diagnoses me-2 text-success"></i>Diagnosis</h5>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($record->diagnosis)) !!}
                            </div>
                        </div>

                        @if ($record->treatment_plan)
                            <div class="mb-4">
                                <h5><i class="fas fa-clipboard-list me-2 text-info"></i>Treatment Plan</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($record->treatment_plan)) !!}
                                </div>
                            </div>
                        @endif

                        @if ($record->notes)
                            <div class="mb-4">
                                <h5><i class="fas fa-notes-medical me-2 text-warning"></i>Additional Notes</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($record->notes)) !!}
                                </div>
                            </div>
                        @endif

                        @if ($record->test_reports && is_array($record->test_reports) && count($record->test_reports) > 0)
                            <div class="mb-4">
                                <h5><i class="fas fa-flask me-2 text-danger"></i>Test Reports</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Test Name</th>
                                                <th>Result</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($record->test_reports as $test)
                                                <tr>
                                                    <td>{{ $test['test'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if (isset($test['result']))
                                                            <span
                                                                class="badge bg-{{ $test['result'] == 'Normal' ? 'success' : ($test['result'] == 'Abnormal' ? 'danger' : 'info') }}">
                                                                {{ $test['result'] }}
                                                            </span>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $test['remarks'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Prescription Card -->
                @if ($record->prescription)
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="fas fa-prescription me-2 text-primary"></i>Prescription
                            </h4>

                            <div class="mb-3">
                                <h6>Medication</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Medicine</th>
                                                <th>Dosage</th>
                                                <th>Frequency</th>
                                                <th>Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $medication = json_decode(
                                                    $record->prescription->medication_details,
                                                    true,
                                                );
                                            @endphp
                                            @if (is_array($medication))
                                                @foreach ($medication as $medicine)
                                                    <tr>
                                                        <td>{{ $medicine['medicine'] ?? 'N/A' }}</td>
                                                        <td>{{ $medicine['dosage'] ?? 'N/A' }}</td>
                                                        <td>{{ $medicine['frequency'] ?? 'N/A' }}</td>
                                                        <td>{{ $medicine['duration'] ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if ($record->prescription->instructions)
                                <div class="mb-3">
                                    <h6>Instructions</h6>
                                    <div class="p-2 bg-light rounded">
                                        {!! nl2br(e($record->prescription->instructions)) !!}
                                    </div>
                                </div>
                            @endif

                            @if ($record->prescription->follow_up_advice)
                                <div class="mb-3">
                                    <h6>Follow-up Advice</h6>
                                    <div class="p-2 bg-light rounded">
                                        {!! nl2br(e($record->prescription->follow_up_advice)) !!}
                                    </div>
                                </div>
                            @endif

                            <div class="text-center">
                                <a href="{{ route('doctor.prescriptions.show', $record->prescription->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i> View Full Prescription
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-prescription fa-3x text-muted mb-3"></i>
                            <h5>No Prescription</h5>
                            <p class="text-muted">Create a prescription for this patient</p>
                            <a href="{{ route('doctor.prescriptions.create') }}?record_id={{ $record->id }}"
                                class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create Prescription
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Actions Card -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Actions</h4>
                        <div class="d-grid gap-2">

                            <a href="{{ route('doctor.medical-reports.edit', $record->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i> Edit Report
                            </a>



                            <a href="{{ route('doctor.medical-reports.print', $record->id) }}"
                                class="btn btn-outline-secondary" target="_blank">
                                <i class="fas fa-print me-2"></i> Print Report
                            </a>

                            <form action="{{ route('doctor.medical-reports.destroy', $record->id) }}" method="POST"
                                class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this medical report? This action cannot be undone.')">
                                    <i class="fas fa-trash me-2"></i> Delete Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Report Info</h4>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Created:</th>
                                <td>{{ $record->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Updated:</th>
                                <td>{{ $record->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-success">Completed</span>
                                </td>
                            </tr>
                            @if ($record->appointment)
                                <tr>
                                    <th>Appointment:</th>
                                    <td>{{ $record->appointment->appointment_date ? \Carbon\Carbon::parse($record->appointment->appointment_date)->format('d M Y') : 'N/A' }}
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
