@extends('doctor.layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@section('content')
    <div class="container-fluid mt-4">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h4 class="page-title text-black mb-0">Create Medical Report</h4>
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
                        <form action="{{ route('doctor.medical-reports.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_id" class="form-label">Select Appointment *</label>
                                        <select class="form-control " id="appointment_id" name="appointment_id" required>
                                            <option value="">Select Appointment</option>
                                            @foreach ($appointments as $appointment)
                                                <option value="{{ $appointment->id }}">
                                                    {{ $appointment->patient->first_name }}
                                                    {{ $appointment->patient->last_name }} -
                                                    {{ $appointment->appointment_date->format('d M Y') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="record_date" class="form-label">Record Date </label>
                                        <input type="date" class="form-control" id="record_date" name="record_date"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="appointment_id" class="form-label">Report Title</label>
                                        <input type="text" class="form-control" id="report_title" name="report_title"
                                            placeholder="e.g., MRI Scan Report">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="record_date" class="form-label">Report Type</label>
                                        <input type="text" class="form-control" id="record_type" name="report_type"
                                            placeholder="e.g., General Checkup">
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
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                            </div>



                            <div class="text-end">
                                <a href="{{ route('doctor.medical-reports.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Medical Record</button>
                            </div>
                        </form>
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
    <script>
        document.getElementById('report_file').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    alert('File size exceeds 2MB. Please upload a smaller file.');
                    event.target.value = ""; // Clear the file input
                }
            }
        });
    </script>
@endsection
