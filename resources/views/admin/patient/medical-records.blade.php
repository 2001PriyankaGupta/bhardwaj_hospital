@extends('admin.layouts.master')

@section('title', 'Medical Records - ' . $patient->first_name . ' ' . $patient->last_name)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .swal2-toast {
        font-size: 12px !important;
        padding: 6px 10px !important;
        min-width: auto !important;
        width: 220px !important;
        line-height: 1.3em !important;
    }

    .swal2-toast .swal2-icon {
        width: 24px !important;
        height: 24px !important;
        margin-right: 6px !important;
    }

    .swal2-toast .swal2-title {
        font-size: 13px !important;
    }
</style>

@section('content')
    <div class="row mt-4">
        <div class="d-flex justify-content-between align-items-center m-4 flex-wrap">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-orange fw-bold"> Medical Records: {{ $patient->first_name }}
                        {{ $patient->last_name }}</h1>

                </div>
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary btn-sm float-right"
                    style="    margin-right: 38px;">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                {{-- <div class="card-header">
                    <h3 class="card-title text-orange fw-bold">
                        Medical Records: {{ $patient->first_name }} {{ $patient->last_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Patient
                        </a>
                    </div>
                </div> --}}
                <div class="card-body">
                    <!-- Add Medical Record Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Add New Medical Record</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.patients.store-medical-record', $patient) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="record_title">Record Title </label>

                                                    <input type="text" class="form-control" id="record_title"
                                                        name="report_title" placeholder="example - Blood Test, X-Ray etc.">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="record_type">Report Type </label>

                                                    <input type="text" class="form-control" id="report_type"
                                                        name="report_type"
                                                        placeholder="example - Diagnosis, Prescription, Lab Report etc.">

                                                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="record_date">Record Date *</label>
                                                    <input type="date" class="form-control" id="record_date"
                                                        name="record_date" required value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <div class="form-group">
                                                    <label for="report_file">Upload Report</label>
                                                    <input type="file" class="form-control" id="report_file"
                                                        name="report_file" accept="image/*,application/pdf"
                                                        onchange="validateFileSize(this)">
                                                    <small class="text-muted">Only image and PDF files are allowed. Maximum
                                                        size: 2MB.</small>

                                                    <script>
                                                        function validateFileSize(input) {
                                                            const file = input.files[0];
                                                            if (file && file.size > 2 * 1024 * 1024) {
                                                                alert('File size exceeds 2MB. Please upload a smaller file.');
                                                                input.value = '';
                                                            }
                                                        }
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="notes">Notes</label>
                                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">Add Medical Record</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Records Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="medicalRecordsTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Title</th>
                                            <th>Type</th>

                                            <th>Notes</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        @foreach ($medicalRecords as $record)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($record->record_date)->format('d M Y') }}
                                                </td>
                                                <td>{{ $record->report_title }}</td>
                                                <td>
                                                    <span class="badge badge-info"
                                                        style="color: purple">{{ ucfirst($record->report_type) }}</span>
                                                </td>

                                                <td>{{ $record->notes ?? 'N/A' }}</td>
                                                <td>{{ $record->createdBy->name ?? 'System' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.patients.edit-medical-record', $record->id) }}"
                                                        class="btn btn-sm btn-warning">Edit</a>

                                                    <form
                                                        action="{{ route('admin.patients.delete-medical-record', $record->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $medicalRecords->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Record Modal -->
    <div class="modal fade" id="viewRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">


                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Medical Record Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" id="recordDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let baseUrl = "{{ config('app.url') }}";
        @if (session('success'))
            Swal.fire({
                toast: true,
                icon: 'success',
                title: "{{ session('success') }}",
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#f8f9fa',
                iconColor: '#28a745'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                toast: true,
                icon: 'error',
                title: "{{ session('error') }}",
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#f8f9fa',
                iconColor: '#dc3545'
            });
        @endif
        $(document).ready(function() {
            // Initialize DataTable
            $('#medicalRecordsTable').DataTable({
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                pageLength: 10
            });

            // View record details
            $('.view-record').click(function() {
                var record = $(this).data('record');
                var html = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Record Type:</strong>
                </div>
                <div class="col-md-6">
                    <span class="badge badge-info" style="color:blue;">${record.record_type}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Record Date:</strong>
                </div>
                <div class="col-md-6">
                    ${new Date(record.record_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Description:</strong>
                </div>
                <div class="col-md-6">
                    ${record.description}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Notes:</strong>
                </div>
                <div class="col-md-6">
                    ${record.notes || 'N/A'}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Created By:</strong>
                </div>
                <div class="col-md-6">
                    ${record.created_by_name || 'System'}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <strong>Created At:</strong>
                </div>
                <div class="col-md-6">
                    ${new Date(record.created_at).toLocaleString('en-GB')}
                </div>
            </div>
        `;
                $('#recordDetails').html(html);
                $('#viewRecordModal').modal('show');
            });
        });
    </script>
@endsection
