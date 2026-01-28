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
                                <h3 class="">All Medical Records</h3>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('doctor.medical-reports.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create New Report
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Patient</th>
                                        <th>Report Name</th>
                                        <th>Report Type</th>
                                        <th>Record Date</th>

                                        <th>File</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $serial = 1; @endphp
                                    @forelse($records as $record)
                                        <tr>
                                            <td>{{ $serial++ }}</td>
                                            <td>{{ $record->patient->first_name }} {{ $record->patient->last_name }}</td>
                                            <td>{{ $record->report_title ?? 'N/A' }}</td>
                                            <td>{{ $record->report_type ?? 'N/A' }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($record->record_date)->format('d M Y') }}
                                            </td>


                                            <td>
                                                @if ($record->report_file)
                                                    <a href="{{ asset('storage/' . $record->report_file) }}" target="_blank"
                                                        class="btn btn-sm btn-secondary" title="View File"
                                                        style="height: 25px;">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">

                                                    <a href="{{ route('doctor.medical-reports.edit', $record->id) }}"
                                                        class="btn btn-sm btn-primary" title="Edit" style="height: 25px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form
                                                        action="{{ route('doctor.medical-reports.destroy', $record->id) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure?')" title="Delete"
                                                            style="height: 25px;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
                                            <td class="text-center">No medical records found.</td>
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
            $('#reportTable').DataTable({
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


        });
    </script>
@endsection
