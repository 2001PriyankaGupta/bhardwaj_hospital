@extends('admin.layouts.master')

@section('title', 'Patient Management')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
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

    .text-orange {
        color: #fd7e14 !important;
    }

    .info-box {
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        border-radius: 0.25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: 0.5rem;
        position: relative;
    }

    .info-box .info-box-icon {
        border-radius: 0.25rem;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
    }

    .info-box .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.8;
        flex: 1;
        padding: 0 10px;
    }

    .info-box .info-box-number {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .bg-gradient-info {
        background: linear-gradient(45deg, #17a2b8, #6cb2eb) !important;
        color: white;
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #28a745, #51cf66) !important;
        color: white;
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #ffc107, #ffd351) !important;
        color: white;
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #5a99ee) !important;
        color: white;
    }

    .action-buttons .btn {
        margin-left: 5px;
    }

    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .badge {
        font-size: 0.75em;
        font-weight: 600;
    }
</style>

@section('content')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center m-4 flex-wrap">
                    <div class="d-flex align-items-center">
                        <div>
                            <h1 class="h3 mb-0 text-orange fw-bold">Patients Database</h1>
                            <p class="text-muted mb-0">Manage all patient records and information</p>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Patient
                        </a>
                    </div>
                </div>



                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table  table-hover w-100" id="patientsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Date of Birth</th>
                                    <th>Appointments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @foreach ($patients as $index => $patient)
                                    <tr>
                                        <td>{{ $count++ }}</td>

                                        <td>
                                            <strong class="text-primary">{{ $patient->patient_id }}</strong>
                                        </td>
                                        <td>

                                            <div class="fw-bold">{{ $patient->first_name }}
                                                {{ $patient->last_name }}</div>
                                            <div class="text-muted small">{{ ucfirst($patient->gender) ?? 'N/A' }}

                                        </td>
                                        <td>
                                            @if ($patient->phone)
                                                {{ $patient->phone }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($patient->email)
                                                {{ $patient->email }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($patient->date_of_birth)
                                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d M Y') }}
                                                <br>
                                                <small
                                                    class="text-muted">({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }}
                                                    years)</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span class="badge badge-info badge-pill"
                                                    style="font-size: 0.9rem;color: #fd7e14">
                                                    {{ $patient->appointments_count }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $patient->is_active ? 'success' : 'danger' }}"
                                                style="color: #28a745">
                                                {{ $patient->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.patients.show', $patient) }}"
                                                    class="btn btn-info btn-sm" title="View Details"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.patients.edit', $patient) }}"
                                                    class="btn btn-secondary btn-sm" title="Edit"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.patients.appointment-history', $patient) }}"
                                                    class="btn btn-warning btn-sm" title="Appointment History"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                                <a href="{{ route('admin.patients.analytics', $patient) }}"
                                                    class="btn btn-success btn-sm" title="Analytics"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <!-- Delete Button with Confirmation -->
                                                <button type="button" class="btn btn-danger btn-sm delete-patient"
                                                    title="Delete Patient" data-bs-toggle="tooltip"
                                                    data-patient-id="{{ $patient->id }}"
                                                    data-patient-name="{{ $patient->first_name }} {{ $patient->last_name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let baseUrl = "{{ config('app.url') }}";
        $(document).ready(function() {
            $('#patientsTable').DataTable({
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [
                    [0, 'asc']
                ],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search appointments...",
                },
                columnDefs: [{
                    orderable: false,
                    targets: [5] // Actions column
                }]
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // SweetAlert notifications
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

            @if (session('warning'))
                Swal.fire({
                    toast: true,
                    icon: 'warning',
                    title: "{{ session('warning') }}",
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#f8f9fa',
                    iconColor: '#ffc107'
                });
            @endif

            // Quick search functionality
            $('#quickSearch').on('keyup', function() {
                $('#patientsTable').DataTable().search(this.value).draw();
            });

            // Export buttons functionality (placeholder)
            $('.export-btn').on('click', function() {
                var type = $(this).data('type');
                Swal.fire({
                    title: 'Exporting Data',
                    text: 'Preparing ' + type + ' export...',
                    icon: 'info',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        });
    </script>

    <script>
        // Additional functionality for patient management
        document.addEventListener('DOMContentLoaded', function() {
            // Bulk actions
            $('#selectAll').on('click', function() {
                $('.patient-checkbox').prop('checked', this.checked);
            });

            // Bulk action handler
            $('#bulkAction').on('change', function() {
                var action = $(this).val();
                if (action) {
                    var selectedPatients = $('.patient-checkbox:checked').length;
                    if (selectedPatients > 0) {
                        Swal.fire({
                            title: 'Confirm Action',
                            text: 'Are you sure you want to ' + action + ' ' + selectedPatients +
                                ' patient(s)?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, proceed!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Perform bulk action here
                                Swal.fire(
                                    'Success!',
                                    'Action completed successfully.',
                                    'success'
                                );
                            }
                            // Reset select
                            $(this).val('');
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Selection',
                            text: 'Please select at least one patient.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        $(this).val('');
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Delete patient confirmation
            $('.delete-patient').click(function(e) {
                e.preventDefault();

                const patientId = $(this).data('patient-id');
                const patientName = $(this).data('patient-name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete patient: ${patientName}. This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create delete form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `${baseUrl}/admin/patients/${patientId}`;

                        // Add CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        // Add method spoofing
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);

                        // Submit form
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
