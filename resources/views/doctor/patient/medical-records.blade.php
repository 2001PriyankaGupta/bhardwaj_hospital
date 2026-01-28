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
                                        method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="record_type">Record Type *</label>
                                                    <select class="form-control" id="record_type" name="record_type"
                                                        required>
                                                        <option value="">Select Type</option>
                                                        <option value="diagnosis">Diagnosis</option>
                                                        <option value="prescription">Prescription</option>
                                                        <option value="lab_report">Lab Report</option>
                                                        <option value="consultation">Consultation</option>
                                                        <option value="procedure">Procedure</option>
                                                        <option value="follow_up">Follow Up</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="record_date">Record Date *</label>
                                                    <input type="date" class="form-control" id="record_date"
                                                        name="record_date" required value="{{ date('Y-m-d') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="description">Description *</label>
                                                    <input type="text" class="form-control" id="description"
                                                        name="description" required placeholder="Enter record description">
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
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Notes</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($medicalRecords as $record)
                                            <tr>
                                                <td>{{ $record->record_date->format('d M Y') }}</td>
                                                <td>
                                                    <span class="badge badge-info"
                                                        style="color: purple">{{ ucfirst($record->record_type) }}</span>
                                                </td>
                                                <td>{{ $record->description }}</td>
                                                <td>{{ $record->notes ?? 'N/A' }}</td>
                                                <td>{{ $record->createdBy->name ?? 'System' }}</td>
                                                <td>
                                                    <button class="btn btn-info btn-sm view-record"
                                                        data-record="{{ $record }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
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
    <script>
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
