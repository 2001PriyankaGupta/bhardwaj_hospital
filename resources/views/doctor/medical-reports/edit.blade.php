@extends('doctor.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h4 class="page-title text-black mb-0">Edit Medical Report</h4>
            </div>

            <div class="col-md-6 text-end">
                <a href="{{ route('doctor.medical-reports.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('doctor.medical-reports.update', $record->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Patient:</strong>
                                                @if ($record->appointment && $record->appointment->patient)
                                                    {{ $record->appointment->patient->first_name ?? 'N/A' }}
                                                    {{ $record->appointment->patient->last_name ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Appointment Date:</strong>
                                                @if ($record->appointment && $record->appointment->appointment_date)
                                                    {{ $record->appointment->appointment_date->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_id" class="form-label">Report Title</label>
                                        <input type="text" class="form-control" id="report_title" name="report_title"
                                            placeholder="e.g., MRI Scan Report" value="{{ $record->report_title }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="record_date" class="form-label">Report Type</label>
                                        <input type="text" class="form-control" id="record_type" name="report_type"
                                            placeholder="e.g., General Checkup" value="{{ $record->report_type }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_id" class="form-label">Appointment</label>
                                        <input type="text" class="form-control bg-light"
                                            value="@if ($record->appointment && $record->appointment->patient) {{ $record->appointment->patient->first_name ?? 'N/A' }} {{ $record->appointment->patient->last_name ?? 'N/A' }} - {{ $record->appointment->appointment_date->format('d M Y') ?? 'N/A' }} @else N/A @endif"
                                            readonly>
                                        <input type="hidden" name="appointment_id" value="{{ $record->appointment_id }}">
                                        <small class="text-muted">Appointment cannot be changed</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="record_date" class="form-label">Record Date *</label>
                                        <input type="date" class="form-control" id="record_date" name="record_date"
                                            value="{{ old('record_date', \Carbon\Carbon::parse($record->record_date)->format('Y-m-d')) }}"
                                            required>
                                        @error('record_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="symptoms" class="form-label">Upload Report </label>
                                <input type="file" class="form-control" id="report_file" name="report_file"
                                    accept="image/*,application/pdf">
                                <small class="text-muted">Only image and PDF files are allowed. Maximum size: 2MB.</small>
                            </div>

                            <div class="mb-3">
                                <label for="report_preview" class="form-label">Report Preview</label>
                                <div class="border p-3" id="report_preview">
                                    @if ($record->report_file)
                                        @if (Str::endsWith($record->report_file, ['.jpg', '.jpeg', '.png', '.gif']))
                                            <img src="{{ asset('storage/' . $record->report_file) }}" alt="Report Preview"
                                                class="img-fluid">
                                        @elseif(Str::endsWith($record->report_file, ['.pdf']))
                                            <embed src="{{ asset('storage/' . $record->report_file) }}"
                                                type="application/pdf" width="100%" height="400px">
                                        @else
                                            <p>Preview not available for this file type.</p>
                                        @endif
                                    @else
                                        <p>No report file uploaded.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes...">{{ old('notes', $record->notes) }}</textarea>
                            </div>



                            <div class="text-end">
                                <a href="{{ route('doctor.medical-reports.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Medical Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Format JSON in test_reports textarea for better readability
                $('#test_reports').on('blur', function() {
                    try {
                        const jsonData = JSON.parse($(this).val());
                        $(this).val(JSON.stringify(jsonData, null, 2));
                    } catch (e) {
                        // If not valid JSON, keep as is
                    }
                });

                // Initialize date picker if needed
                $('#record_date').flatpickr({
                    dateFormat: "Y-m-d",
                    maxDate: "today"
                });
            });
        </script>
    @endpush
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endsection
