@extends('doctor.layouts.master')

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
</style>

@section('content')
    <div class="container-fluid mt-4">


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h3 class="mb-3 mt-3">All Prescriptions</h3>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('doctor.prescriptions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-prescription"></i> Create New Prescription
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0" id="prescriptionTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Valid Until</th>
                                        <th>Medicines</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($prescriptions as $prescription)
                                        <tr>
                                            <td>{{ $prescription->id }}</td>
                                            <td>{{ $prescription->patient->first_name }}
                                                {{ $prescription->patient->last_name }}</td>
                                            <td>{{ $prescription->prescription_date->format('d M Y') }}</td>
                                            <td>{{ $prescription->valid_until ? $prescription->valid_until->format('d M Y') : 'N/A' }}
                                            </td>
                                            <td>{{ count($prescription->medication_details) }} medicines</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $prescription->is_active ? 'success' : 'danger' }}">
                                                    {{ $prescription->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('doctor.prescriptions.show', $prescription->id) }}"
                                                        class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('doctor.prescriptions.download', $prescription->id) }}"
                                                        class="btn btn-sm btn-success" title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="{{ route('doctor.prescriptions.print', $prescription->id) }}"
                                                        class="btn btn-sm btn-secondary" title="Print" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center">No prescriptions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

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
        $(document).ready(function() {
            $('#prescriptionTable').DataTable({
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                order: [
                    [0, 'asc']
                ],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search ...",
                },
                columnDefs: [{
                    orderable: false,
                    targets: [6] // Actions column
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


        });
    </script>
@endsection
